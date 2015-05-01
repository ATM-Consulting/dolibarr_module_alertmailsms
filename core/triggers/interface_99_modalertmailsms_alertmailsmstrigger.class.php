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
 * 	\file		core/triggers/interface_99_modMyModule_Mytrigger.class.php
 * 	\ingroup	mymodule
 * 	\brief		Sample trigger
 * 	\remarks	You can create other triggers by copying this one
 * 				- File name should be either:
 * 					interface_99_modMyModule_Mytrigger.class.php
 * 					interface_99_all_Mytrigger.class.php
 * 				- The file must stay in core/triggers
 * 				- The class name must be InterfaceMytrigger
 * 				- The constructor method must be named InterfaceMytrigger
 * 				- The name property name must be Mytrigger
 */

require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';

// Extend DolibarrTriggers from Dolibarr 3.7
$dolibarr_version = versiondolibarrarray();
if ($dolibarr_version[0] < 3 || ($dolibarr_version[0] == 3 && $dolibarr_version[1] < 7)) { // DOL_VERSION < 3.7
	abstract class AlertMailSmsTrigger
	{
	}
} else {
	abstract class AlertMailSmsTrigger extends DolibarrTriggers
	{
	}
}

/**
 * Class InterfaceMytrigger
 */
class Interfacealertmailsmstrigger extends AlertMailSmsTrigger
{
	/**
	 * @var DoliDB Database handler
	 */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;

		$this->name = preg_replace('/^Interface/i', '', get_class($this));
		$this->family = "demo";
		$this->description = "Triggers of this module are empty functions."
			. "They have no effect."
			. "They are provided for tutorial purpose only.";
		// 'development', 'experimental', 'dolibarr' or version
		$this->version = 'development';
		$this->picto = 'alertmailsms@alertmailsms';
	}

	/**
	 * Trigger name
	 *
	 * @return string Name of trigger file
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Trigger description
	 *
	 * @return string Description of trigger file
	 */
	public function getDesc()
	{
		return $this->description;
	}

	/**
	 * Trigger version
	 *
	 * @return string Version of trigger file
	 */
	public function getVersion()
	{
		global $langs;
		$langs->load("admin");

		if ($this->version == 'development') {
			return $langs->trans("Development");
		} elseif ($this->version == 'experimental')

				return $langs->trans("Experimental");
		elseif ($this->version == 'dolibarr') return DOL_VERSION;
		elseif ($this->version) return $this->version;
		else {
			return $langs->trans("Unknown");
		}
	}

	/**
	 * Compatibility trigger function for Dolibarr < 3.7
	 *
	 * @param int           $action Trigger action
	 * @param CommonObject  $object Object trigged from
	 * @param User          $user   User that trigged
	 * @param Translate     $langs  Translations handler
	 * @param Conf          $conf   Configuration
	 * @return int                  <0 if KO, 0 if no triggered ran, >0 if OK
	 * @deprecated Replaced by DolibarrTriggers::runTrigger()
	 */
	public function run_trigger($action, $object, $user, $langs, $conf)
	{
		return $this->runTrigger($action, $object, $user, $langs, $conf);
	}

	/**
	 * Function called when a Dolibarrr business event is done.
	 * All functions "runTrigger" are triggered if file
	 * is inside directory core/triggers
	 *
	 * @param string    $action Event action code
	 * @param Object    $object Object
	 * @param User      $user   Object user
	 * @param Translate $langs  Object langs
	 * @param Conf      $conf   Object conf
	 * @return int              <0 if KO, 0 if no triggered ran, >0 if OK
	 */
	public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
	{
		//ORDER_VALIDATE | SHIPPING_VALIDATE
		$actionTrigger = $conf->global->ALERTMAILSMS_TRIGGER;
		
		//var_dump($object);exit;
		
		if ($action == $actionTrigger) 
		{
			dol_syslog("Trigger '" . $this->name . "' for action '$action' launched by " . __FILE__ . ". id=" . $object->id);
		}
		
		return 0;
	}
}