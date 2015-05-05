<?php

	dol_include_once('/alertmailsms/OVH/vendor/autoload.php');
	dol_include_once('/alertmailsms/OVH/vendor/ovh/ovh/src/Api.php');
	
	use \Ovh\Api;

	class OvhApi extends Api {}
