<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) <year>  <name of author>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file		lib/alertmailsms.lib.php
 *	\ingroup	alertmailsms
 *	\brief		This file is an example module library
 *				Put some comments here
 */

 
function AlertMailSmsAdminPrepareHead()
{
	global $langs, $conf;
	   
	$langs->load("alertmailsms@alertmailsms");
	   
	$h = 0;
	$head = array();
	   
	$head[$h][0] = dol_buildpath("/alertmailsms/admin/alertmailsms_setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;
	$head[$h][0] = dol_buildpath("/alertmailsms/admin/alertmailsms_about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;
	   
	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//      'entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//      'entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'alertmailsms');
	   
	return $head;
}
 
function getCTypeContact(&$db)
{
	$sql = 'SELECT code, libelle FROM '.MAIN_DB_PREFIX.'c_type_contact WHERE element = "commande" AND source="external" AND active = 1';
	$resql = $db->query($sql);
	
	if ($resql)
	{
		if ($db->num_rows($resql) > 0)
		{
			$res = array('' => '');
			
			while ($row = $db->fetch_array($resql))
			{
				$res[$row['code']] = $row['libelle'];
			}
			
			return $res;
		}
		
		return array();
	}
	else
	{
		setEventMessage($db->lasterror(), 'errors');
		return false;
	}
}

function getInfoAccountOvh()
{
	global $langs,$conf;
	
	$res = array(
		0 => array('' => '')
		,1 => 0
	);
	
	if (!empty($conf->global->ALERTMAILSMS_SEND_SMS_ENABLED) && !empty($conf->global->ALERTMAILSMS_OVH_KEY) && !empty($conf->global->ALERTMAILSMS_OVH_SECRET) && !empty($conf->global->ALERTMAILSMS_OVH_CONSUMER_KEY))
	{
		$TAlertMailSms = new TAlertMailSms;
		$TCompteSms = $TAlertMailSms->getComptesSmsOvh();
		
		if ($TCompteSms)
		{
			foreach ($TCompteSms as $index => $value)
			{
				$res[0][$value] = $value;
			}
		}
		
		$res[1] = $TAlertMailSms->getCreditsLeft();
	}
	
	return $res;
}
