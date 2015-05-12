<?php

class TAlertMailSms extends TObjetStd
{
	var $platform;
	var $send;
	var $errors;
	var $ovh_api;
	
	public function __construct()
	{
		global $conf, $langs;

		$this->platform = $conf->global->ALERTMAILSMS_PLATFORM;
		$this->send = false;
		$this->errors = array();
		
		$this->_includeClass($conf, $langs);
	}
	
	public function resetVar()
	{
		$this->send = false;
		$this->errors = array();
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
				$this->ovh_api = new OvhApi($conf->global->ALERTMAILSMS_OVH_KEY, $conf->global->ALERTMAILSMS_OVH_SECRET, 'ovh-eu', $conf->global->ALERTMAILSMS_OVH_CONSUMER_KEY);
				break;
			
			default:
				$this->errors[] = $langs->trans("ALERTMAILSMS_ERR_NO_PLATFORM");
				break;
		}
	}
	
	public function getAlert(&$PDOdb, $Tfk_socpeople)
	{
		$res = array();
		$sql = 'SELECT rowid, alert_mail, alert_sms FROM '.MAIN_DB_PREFIX.'socpeople WHERE rowid IN ('.implode(',', $Tfk_socpeople).')';
		
		$PDOdb->Execute($sql);
		while ($line = $PDOdb->Get_line()) 
		{
			$res[] = $line;
		}
		
		return $res;
	}
	
	private function _sendMail(&$object, &$conf)
	{
		// ^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$
		// $object->email;
		
		if (empty($object->email)) return false;
		
		$TSearch = array('__CONTACTCIVILITY__', '__CONTACTNAME__', '__CONTACTFIRSTNAME__', '__CONTACTADDRESS__');
		$TReplace = array($object->civility_id, $object->lastname, $object->firstname, $object->address);

		$msg = str_replace($TSearch, $TReplace, $conf->global->ALERTMAILSMS_MSG_MAIL);

		// Construct mail
		$CMail = new CMailFile(	$conf->global->ALERTMAILSMS_SUBJECT_MAIL
								,$object->email
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
	
	private function _sendSms(&$object, &$conf)
	{
		$phone_number = $this->formatPhoneNumber($object->phone_pro);
		
		$TSearch = array('__CONTACTCIVILITY__', '__CONTACTNAME__', '__CONTACTFIRSTNAME__', '__CONTACTADDRESS__');
		$TReplace = array($object->civility_id, $object->lastname, $object->firstname, $object->address);
		
		/*
		$TSearch = array();
		$TReplace = array();
		
		foreach ($object as $key => $value) {
		 	$TSearch[] = '__CONTACT_'.strtoupper($key).'__';
			$TReplace[] = $value;
		}
		*/


		$msg = str_replace($TSearch, $TReplace, $conf->global->ALERTMAILSMS_MSG_SMS);
			
		try
		{
			//TODO sélectionner le bon compte
			$TCompteSms = $this->ovh_api->get('/sms/'); //Array des comptes sms disponibles
			//if (!$TCompteSms) $TCompteSms = array('PHTEST');
			
			//More info : https://api.ovh.com/console/#/sms/{serviceName}/jobs#POST
			$content = (object) array(
				'charset'=> 'UTF-8'
				,'class'=> 'phoneDisplay'
				,'coding'=> '8bit'
				,'message'=> $msg
				,'noStopClause'=> false //Do not display STOP clause in the message, this requires that this is not an advertising message
				,'priority'=> 'medium'
				,'receivers'=> array($phone_number) //The receivers list
				,'sender'=>$conf->global->ALERTMAILSMS_PHONE_NUMBER //The sender
				,'senderForResponse'=> false //Set the flag to send a special sms which can be reply by the receiver (smsResponse).
				,'validityPeriod'=> 2880 //The maximum time -in minute(s)- before the message is dropped
			);
			
			$smsSend = $this->ovh_api->post('/sms/'.$TCompteSms[0].'/jobs/', $content);
		}
		catch (Exception $e)
		{
			if ($e->hasResponse())
			{
				$dolibarr_version = versiondolibarrarray();	
		
				if ($dolibarr_version[0] < 3 || ($dolibarr_version[0] == 3 && $dolibarr_version[1] < 7)) setEventMessage($e->getResponse(), 'errors');
				else setEventMessages($e->getResponse(), array(), 'errors');
			}
		}
		
	}

	public function formatPhoneNumber($number)
	{
		$search = array(' ', '.', ',', '-', '_', '/');
		$res = str_replace($search, '', $number);
		
		// Format français
		if (!preg_match('/^\+33[1-9]{1}[0-9]{8}$/', $res))
		{
			if ($res[0] == 0) $res = substr($res, 1);
			
			$res = '+33'.$res;
		}
		
		return $res;
	} 
	
	public function send(&$object, &$conf, $forceMail = false, $forceSms = false)
	{
		if ($object->alert_mail || $forceMail) $this->_sendMail($object, $conf);
		
		if ($object->alert_sms || $forceSms) $this->_sendSms($object, $conf);
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

include_once(DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php');

class TContact extends Contact
{
	var $alert_mail;
	var $alert_sms;
	
	public function fetch($id, $user=0, $ref_ext='')
	{
		$res = parent::fetch($id, $user, $ref_ext);
		
		if ($res > 0)
		{
			$this->getAlertAttributes();
		}
		
		return $res;
	}
	
	public function getAlertAttributes()
	{
		$sql = 'SELECT alert_mail, alert_sms FROM '.MAIN_DB_PREFIX.'socpeople WHERE rowid = '.$this->id;
		$resql = $this->db->query($sql);
		
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
					
				$this->alert_mail = $obj->alert_mail;
				$this->alert_sms = $obj->alert_sms;
			}
		}
	}
	
	public function setAlertAttributes($alert_mail, $alert_sms)
	{
		$this->alert_mail = $alert_mail;
		$this->alert_sms = $alert_sms;
		
		$sql = 'UPDATE '.MAIN_DB_PREFIX.'socpeople SET alert_mail = '.$alert_mail.', alert_sms = '.$alert_sms.' WHERE rowid = '.$this->id;
		return $this->db->query($sql);
	}
}

