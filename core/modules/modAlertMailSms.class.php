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
 * 	\defgroup	mymodule	MyModule module
 * 	\brief		MyModule module descriptor.
 * 	\file		core/modules/modMyModule.class.php
 * 	\ingroup	mymodule
 * 	\brief		Description and activation file for module MyModule
 */
include_once DOL_DOCUMENT_ROOT . "/core/modules/DolibarrModules.class.php";

/**
 * Description and activation class for module MyModule
 */
class modAlertMailSms extends DolibarrModules
{

	/**
	 * 	Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * 	@param	DoliDB		$db	Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;

		$this->db = $db;

		// Id for module (must be unique).
		// Use a free id here
		// (See http://wiki.dolibarr.org/index.php/List_of_modules_id for available ranges).
		$this->numero = 104550;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'alertmailsms';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "ATM";
		// Module label (no space allowed)
		// used if translation string 'ModuleXXXName' not found
		// (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		// Module description
		// used if translation string 'ModuleXXXDesc' not found
		// (where XXX is value of numeric property 'numero' of module)
		$this->description = "Description of module MyModule";
		// Possible values for version are: 'development', 'experimental' or version
		$this->version = '1.2.1';
		// Key used in llx_const table to save module status enabled/disabled
		// (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
		// Where to store the module in setup page
		// (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png
		// use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png
		// use this->picto='pictovalue@module'
		$this->picto = 'alertmailsms@alertmailsms'; // mypicto@mymodule
		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /mymodule/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /mymodule/core/modules/barcode)
		// for specific css file (eg: /mymodule/css/mymodule.css.php)
		$this->module_parts = array(
			// Set this to 1 if module has its own trigger directory
			'triggers' => 1,
			// Set this to 1 if module has its own login method directory
			//'login' => 0,
			// Set this to 1 if module has its own substitution function file
			//'substitutions' => 0,
			// Set this to 1 if module has its own menus handler directory
			//'menus' => 0,
			// Set this to 1 if module has its own theme directory (theme)
			// 'theme' => 0,
			// Set this to 1 if module overwrite template dir (core/tpl)
			// 'tpl' => 0,
			// Set this to 1 if module has its own barcode directory
			//'barcode' => 0,
			// Set this to 1 if module has its own models directory
			//'models' => 0,
			// Set this to relative path of css if module has its own css file
			//'css' => array('mymodule/css/mycss.css.php'),
			// Set this to relative path of js file if module must load a js on all pages
			// 'js' => array('mymodule/js/mymodule.js'),
			// Set here all hooks context managed by module
			// 'hooks' => array('hookcontext1','hookcontext2'),
			// To force the default directories names
			// 'dir' => array('output' => 'othermodulename'),
			// Set here all workflow context managed by module
			// Don't forget to depend on modWorkflow!
			// The description translation key will be descWORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2
			// You will be able to check if it is enabled with the $conf->global->WORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2 constant
			// Implementation is up to you and is usually done in a trigger.
			// 'workflow' => array(
			//     'WORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2' => array(
			//         'enabled' => '! empty($conf->module1->enabled) && ! empty($conf->module2->enabled)',
			//         'picto' => 'yourpicto@mymodule',
			//         'warning' => 'WarningTextTranslationKey',
			//      ),
			// ),
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/mymodule/temp");
		$this->dirs = array();

		// Config pages. Put here list of php pages
		// stored into mymodule/admin directory, used to setup module.
		$this->config_page_url = array("alertmailsms_setup.php@alertmailsms");

		// Dependencies
		// A condition to hide module
		$this->hidden = false;
		// List of modules class name as string that must be enabled if this module is enabled
		// Example : $this->depends('modAnotherModule', 'modYetAnotherModule')
		$this->depends = array('modSociete');
		// List of modules id to disable if this one is disabled
		$this->requiredby = array();
		// List of modules id this module is in conflict with
		$this->conflictwith = array();
		// Minimum version of PHP required by module
		$this->phpmin = array(5, 3);
		// Minimum version of Dolibarr required by module
		$this->need_dolibarr_version = array(3, 6);
		// Language files list (langfiles@mymodule)
		$this->langfiles = array("alertmailsms@alertmailsms");
		// Constants
		// List of particular constants to add when module is enabled
		// (name, type ['chaine' or ?], value, description, visibility, entity ['current' or 'allentities'], delete on unactive)
		// Example:
		$this->const = array(
			0 => array(
				'ALERTMAILSMS_PLATFORM',
				'chaine',
				'OVH',
				'Platforme utilisée pour l\'envoi des sms',
				1,
			    'current',
			    0,
			),
			1 => array(
				'ALERTMAILSMS_TRIGGER',
				'chaine',
				'SHIPPING_VALIDATE',
				'Platforme utilisée pour l\'envoi des sms',
				1,
			    'current',
			    0,
			)
		);

		// Array to add new pages in new tabs
		// 	Example:
		$this->tabs = array(
			//'contact:+AlertMailSms:AlertMailSmsTabName:alertmailsms@alertmailsms:$user->rights->societe->contact->creer:/alertmailsms/tab.php?id=__ID__',
			//	// To add a new tab identified by code tabname1
			//	'objecttype:+tabname1:Title1:langfile@mymodule:$user->rights->mymodule->read:/mymodule/mynewtab1.php?id=__ID__',
			//	// To add another new tab identified by code tabname2
			//	'objecttype:+tabname2:Title2:langfile@mymodule:$user->rights->othermodule->read:/mymodule/mynewtab2.php?id=__ID__',
			//	// To remove an existing tab identified by code tabname
			//	'objecttype:-tabname'
		);
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// 'contact'          to add a tab in contact view
		// 'contract'         to add a tab in contract view
		// 'group'            to add a tab in group view
		// 'intervention'     to add a tab in intervention view
		// 'invoice'          to add a tab in customer invoice view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'member'           to add a tab in fundation member view
		// 'opensurveypoll'	  to add a tab in opensurvey poll view
		// 'order'            to add a tab in customer order view
		// 'order_supplier'   to add a tab in supplier order view
		// 'payment'		  to add a tab in payment view
		// 'payment_supplier' to add a tab in supplier payment view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'project'          to add a tab in project view
		// 'stock'            to add a tab in stock view
		// 'thirdparty'       to add a tab in third party view
		// 'user'             to add a tab in user view

		// Dictionaries
		if (! isset($conf->alertmailsms->enabled)) {
			$conf->alertmailsms=new stdClass();
			$conf->alertmailsms->enabled = 0;
		}
		$this->dictionaries = array();
		
		// Boxes
		// Add here list of php file(s) stored in core/boxes that contains class to show a box.
		$this->boxes = array(); // Boxes list
		// Example:
		$this->boxes = array(
			//0 => array(
			//	'file' => 'mybox@mymodule',
			//	'note' => '',
			//	'enabledbydefaulton' => 'Home'
			//)
		);

		// Permissions
		$this->rights = array(); // Permission array used by this module
		$r = 0;

		/*$this->rights[$r][0] = 104551;
		$this->rights[$r][1] = 'Autoriser association alerte mail';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'assign';
		$this->rights[$r][5] = 'mail';
		$r++;*/
		
	}

	/**
	 * Function called when module is enabled.
	 * The init function add constants, boxes, permissions and menus
	 * (defined in constructor) into Dolibarr database.
	 * It also creates data directories
	 *
	 * 	@param		string	$options	Options when enabling module ('', 'noboxes')
	 * 	@return		int					1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		$sql = array();
		
		return $this->_init($sql, $options);
	}

	/**
	 * Function called when module is disabled.
	 * Remove from database constants, boxes and permissions from Dolibarr database.
	 * Data directories are not deleted
	 *
	 * 	@param		string	$options	Options when enabling module ('', 'noboxes')
	 * 	@return		int					1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();

		return $this->_remove($sql, $options);
	}

}
