<?php

class TAlertMailSms extends TObjetStd
{
	var $platform;
	var $errors;
	var $ovh_api;
	var $TSearch;
	var $TReplace;
	var $creditsUsed;
	var $dolibarr_version;
	
	public function __construct()
	{
		global $conf, $langs;

		$this->platform = $conf->global->ALERTMAILSMS_PLATFORM;
		$this->errors = array();
		$this->TSearch = array();
		$this->TReplace = array();
		$this->creditsUsed = 0;
		
		$this->dolibarr_version = versiondolibarrarray();
		
		$this->_includeClass($conf, $langs);
	}

	public function save()
	{
		return true;
	}
	
	public function load()
	{
		return true;
	}
	
	private function _includeClass(&$conf, &$langs)
	{		
		dol_include_once('/core/class/CMailFile.class.php');
		// dol_include_once('/core/class/CSMSFile.class.php'); // Experimental Dolibarr
		
		if (empty($this->platform)) $this->errors[] = $langs->trans("ALERTMAILSMS_ERR_NO_PLATFORM");
		
		switch ($this->platform) {
			case 'OVH':
				dol_include_once('/alertmailsms/loadOVH.php');
				
				if (empty($conf->global->ALERTMAILSMS_OVH_KEY) || empty($conf->global->ALERTMAILSMS_OVH_SECRET) || empty($conf->global->ALERTMAILSMS_OVH_CONSUMER_KEY))
				{
					$this->ovh_api = null;
				}
				else
				{
					$this->ovh_api = new OvhApi($conf->global->ALERTMAILSMS_OVH_KEY, $conf->global->ALERTMAILSMS_OVH_SECRET, 'ovh-eu', $conf->global->ALERTMAILSMS_OVH_CONSUMER_KEY);
				}
				
				break;
			
			default:
				$this->errors[] = $langs->trans("ALERTMAILSMS_ERR_NO_PLATFORM");
				break;
		}
	}
	
	public function getComptesSmsOvh()
	{
		global $langs;
		
		try
		{
			$TCompte = $this->ovh_api->get('/sms/');
			return $TCompte;
		}
		catch (Exception $e)
		{
			if ($e->hasResponse())
			{
				$rep = $e->getResponse();
				if (is_object($rep))
				{
					$rep = $langs->trans('AlertMailSmsErrorOccuredFromOvh');
				}
				
				if ($this->dolibarr_version[0] < 3 || ($this->dolibarr_version[0] == 3 && $this->dolibarr_version[1] < 7)) setEventMessage($rep, 'errors');
				else setEventMessages($rep, array(), 'errors');
			}
			
			return false;
		}
		
	}
	
	public function getCreditsLeft()
	{
		global $langs,$conf;
		
		try
		{
			$info = $this->ovh_api->get('/sms/'.$conf->global->ALERTMAILSMS_COMPTE_SMS_OVH.'/');
			return $info['creditsLeft'];
		}
		catch (Exception $e)
		{
			if ($e->hasResponse())
			{
				$rep = $e->getResponse();
				if (is_object($rep))
				{
					$rep = $langs->trans('AlertMailSmsErrorOccuredFromOvh');
				}
				
				if ($this->dolibarr_version[0] < 3 || ($this->dolibarr_version[0] == 3 && $this->dolibarr_version[1] < 7)) setEventMessage($rep, 'errors');
				else setEventMessages($rep, array(), 'errors');
			}
			
			return false;
		}
	}
	
	private function _sendMail(&$contact, &$conf, &$langs)
	{		
		if (empty($contact->email)) return false;

		$msg = str_replace($this->TSearch, $this->TReplace, $conf->global->ALERTMAILSMS_MSG_MAIL);

		// Construct mail
		$CMail = new CMailFile(	
			$conf->global->ALERTMAILSMS_SUBJECT_MAIL
			,$contact->email
			,$conf->global->MAIN_MAIL_EMAIL_FROM
			,$msg
			/*,$filename_list=array()
			,$mimetype_list=array()
			,$mimefilename_list=array()
			,$addr_cc=""
			,$addr_bcc=""
			,$deliveryreceipt=0
			,$msgishtml=0
			,$errors_to=''
			,$css=''*/
		);
		
		// Send mail
		$CMail->sendfile();
		
		if ($CMail->error) $this->errors[] = $CMail->error;
	}
	
	private function _sendSms(&$contact, &$conf, &$langs)
	{
		switch ($this->platform) {
			case 'OVH':
				$this->_sendOvhSms($contact, $conf, $langs);
				break;
			
			default:
				$this->errors[] = $langs->trans("ALERTMAILSMS_ERR_NO_PLATFORM");
				break;
		}
	}

	private function _sendOvhSms(&$contact, &$conf, &$langs)
	{
		if ($this->ovh_api === null)
		{
			if ($this->dolibarr_version[0] < 3 || ($this->dolibarr_version[0] == 3 && $this->dolibarr_version[1] < 7)) setEventMessage($langs->trans("ALERTMAILSMS_ERR_NO_PLATFORM"), 'errors');
			else setEventMessages($langs->trans("ALERTMAILSMS_ERR_NO_PLATFORM"), array(), 'errors');
			
			return;
		}
	
		$phone_number = $this->formatPhoneNumber($contact->phone_pro);
		if ($phone_number === false)
		{
			if ($this->dolibarr_version[0] < 3 || ($this->dolibarr_version[0] == 3 && $this->dolibarr_version[1] < 7)) setEventMessage($langs->trans("ALERTMAILSMS_ERR_EMPTY_PHONE"), 'errors');
			else setEventMessages($langs->trans("ALERTMAILSMS_ERR_EMPTY_PHONE"), array(), 'errors');
			
			return;
		}
		
		$msg = str_replace($this->TSearch, $this->TReplace, $conf->global->ALERTMAILSMS_MSG_SMS);
		
		try
		{
			//More info : https://api.ovh.com/console/#/sms/{serviceName}/jobs#POST
			$content = (object) array(
				'charset'=> 'UTF-8'
				,'class'=> 'phoneDisplay'
				,'coding'=> '8bit'
				,'message'=> $msg
				,'noStopClause'=> true //Do not display STOP clause in the message, this requires that this is not an advertising message
				,'priority'=> 'medium'
				,'receivers'=> array($phone_number) //The receivers list
				,'sender'=>$conf->global->ALERTMAILSMS_SENDER //The sender (num or string sould be ok)
				,'senderForResponse'=> false //Set the flag to send a special sms which can be reply by the receiver (smsResponse).
				,'validityPeriod'=> 2880//2880 //The maximum time -in minute(s)- before the message is dropped
			);
			
			$smsSend = $this->ovh_api->post('/sms/'.$conf->global->ALERTMAILSMS_COMPTE_SMS_OVH.'/jobs/', $content);
			
			if (!empty($smsSend['totalCreditsRemoved'])) $this->creditsUsed += $smsSend['totalCreditsRemoved'];
		}
		catch (Exception $e)
		{
			if ($e->hasResponse())
			{
				$rep = $e->getResponse();
				if (is_object($rep))
				{
					$rep = $langs->trans('AlertMailSmsErrorOccuredFromOvh');
				}
				
				if ($this->dolibarr_version[0] < 3 || ($this->dolibarr_version[0] == 3 && $this->dolibarr_version[1] < 7)) setEventMessage($rep, 'errors');
				else setEventMessages($rep, array(), 'errors');
			}
		}
	}

	public function formatPhoneNumber($number)
	{
					   
		$TSearch = array(" ",".","_","-","'","/","\\",":","*","?","\"","<",">","|","[","]",",",";","=");
		$res = str_replace($TSearch, '', $number);
		
		if (empty($res)) return false;
			
		// Format français
		if (!preg_match('/^\+33[1-9]{1}[0-9]{8}$/', $res))
		{
			if ($res[0] == 0) $res = substr($res, 1);
			
			$res = '+33'.$res;
		}
		
		return $res;
	} 
	
	public function send(&$contact, &$conf, &$langs, $forceMail = false, $forceSms = false)
	{
		if (empty($this->TSearch) && empty($this->TReplace))
		{
			foreach ($contact as $key => $value) {
			 	$this->TSearch[] = '__CONTACT_'.strtoupper($key).'__';
				$this->TReplace[] = $value;
			}
		}
		
		if ($contact->code_alert == $conf->global->ALERTMAILSMS_CTYPE_MAIL || $forceMail) $this->_sendMail($contact, $conf, $langs);
		elseif ($contact->code_alert == $conf->global->ALERTMAILSMS_CTYPE_SMS || $forceSms) $this->_sendSms($contact, $conf, $langs);
	}
	
	public function testGetOVH($apiKey, $secretKey, $consumerKey)
	{
		$ovh = new OvhApi( 
			$apiKey,
            $secretKey,
            'ovh-eu',
            $consumerKey
		);
		
		$a = $ovh->get('/sms/');
		return $a;
	}
}