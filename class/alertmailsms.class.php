<?php

class TAlertMailSms extends TObjetStd
{
	var $platform;
	var $send;
	var $errors;
	
	public function __construct()
	{
		global $conf;
		
		
		$this->platform = $conf->global->ALERTMAILSMS_PLATFORM;
		$this->send = false;
		$this->errors = array();
		
		$this->_includeClass();
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
	
	private function _includeClass()
	{
		global $langs;
		
		dol_include_once('/core/class/CMailFile.class.php');
		// dol_include_once('/core/class/CSMSFile.class.php'); // Experimental Dolibarr
		
		if (empty($this->platform)) $this->errors[] = $langs->trans("ALERTMAILSMS_ERR_NO_PLATFORM");
		
		switch ($this->platform) {
			case 'OVH':
				//dol_include_once('/alertmailsms/OVH/src/Api.php');
				dol_include_once('/alertmailsms/OVH/vendor/autoload.php');
				
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
		$object->phone_pro;
		
		$CSMS = new CSMSFile($object->phone_pro, $object->phone_pro, "Msg de test", 0, 0, 3, 1);
	}
	
	public function send(&$object, &$conf, $forceMail = false, $forceSms = false)
	{
		if ($object->alert_mail || $forceMail) $this->_sendMail($object, $conf);
		
		if ($object->alert_sms || $forceSms) $this->_sendSms($object, $conf);
	}
	
	public function testGetOVH($apiKey, $secretKey, $consumerKey)
	{
		
		$ovh = new Api( 
			$apiKey,
            $secretKey,
            'ovh-eu',
            $consumerKey
		);
		
		$a = $ovh->get('/me');
		var_dump($a);
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


