<?php

class TAlertMailSms extends TObjetStd
{
	public $platform;
	public $send;
	public $errors;
	
	public function __construct()
	{
		global $conf;
		
		$this->platform = $conf->global->ALERTMAILSMS_PLATFORM;
		$this->send = false;
		$this->errors = array();
		
		$this->_includeClass();
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
		
		if (empty($this->platform)) $this->errors[] = $langs->trans("ALERTMAILSMS_ERR_NO_PLATFORM");
		
		switch ($this->platform) {
			case 'OVH':
				require_once($this->platform.'/src/Api.php');
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
	
	public function sendMail()
	{
		
	}
	
	public function sendSms()
	{
		
	}
	
	public function sendBatch()
	{
		
	}
}


