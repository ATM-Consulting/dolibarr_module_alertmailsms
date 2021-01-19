<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
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
 * 	\file		admin/mymodule.php
 * 	\ingroup	mymodule
 * 	\brief		This file is an example module setup page
 * 				Put some comments here
 */
// Dolibarr environment
require_once '../config.php';

// Libraries
dol_include_once('/core/lib/admin.lib.php');
dol_include_once('/alertmailsms/class/alertmailsms.class.php');
dol_include_once('/alertmailsms/lib/alertmailsms.lib.php');

// Translations
$langs->load("alertmailsms@alertmailsms");
$langs->load("admin");

// Access control
if (! $user->admin) {
    accessforbidden();
}

// Parameters
$action = GETPOST('action', 'alpha');

/*
 * Actions
 */
if (preg_match('/set_(.*)/',$action,$reg))
{
	$code=$reg[1];
	if (dolibarr_set_const($db, $code, GETPOST($code), 'chaine', 1, '', $conf->entity) > 0)
	{
		header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}
	
if (preg_match('/del_(.*)/',$action,$reg))
{
	$code=$reg[1];
	if (dolibarr_del_const($db, $code, 0) > 0)
	{
		Header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}

/*
 * View
 */
$page_name = "AlertMailSmsSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'
    . $langs->trans("BackToModuleList") . '</a>';
print_fiche_titre($langs->trans($page_name), $linkback);

// Configuration header
$head = AlertMailSmsAdminPrepareHead();
dol_fiche_head(
    $head,
    'settings',
    $langs->trans("Module104550Name"),
    0,
    "alertmailsms@alertmailsms"
);

// Setup page goes here
$form=new Form($db);
$var=false;
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameters").'</td>'."\n";
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="center" width="100">'.$langs->trans("Value").'</td>'."\n";

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("AlertMailSmsTrigger").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_TRIGGER">';
$TTrigger = array(
	'ORDER_VALIDATE' => $langs->transnoentities('ALERTMAILSMS_TRIGGER_ORDER_VALIDATE')
	,'SHIPPING_VALIDATE' => $langs->transnoentities('ALERTMAILSMS_TRIGGER_SHIPPING_VALIDATE')
);
print $form->selectarray('ALERTMAILSMS_TRIGGER', $TTrigger, $conf->global->ALERTMAILSMS_TRIGGER);
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans('AlertMailSmsSendFile').'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_SEND_FILE">';
print $form->selectyesno('ALERTMAILSMS_SEND_FILE', $conf->global->ALERTMAILSMS_SEND_FILE, 1);
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';


$TCTypeContact = getCTypeContact($db);

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("AlertMailSmsCTypeMail").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_CTYPE_MAIL">';
print $form->selectarray('ALERTMAILSMS_CTYPE_MAIL', $TCTypeContact, $conf->global->ALERTMAILSMS_CTYPE_MAIL);
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("AlertMailSmsCTypeSms").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_CTYPE_SMS">';
print $form->selectarray('ALERTMAILSMS_CTYPE_SMS', $TCTypeContact, $conf->global->ALERTMAILSMS_CTYPE_SMS);
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("AlertMailSmsPhoneAttribute").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_PHONE_ATTRIBUTE">';
print $form->selectarray('ALERTMAILSMS_PHONE_ATTRIBUTE', array('phone_pro' => $langs->trans('AlertMailSmsPhonePro'), 'phone_perso' => $langs->trans('AlertMailSmsPhonePerso'), 'phone_mobile' => $langs->trans('AlertMailSmsPhoneMobile')), $conf->global->ALERTMAILSMS_PHONE_ATTRIBUTE);
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$form->textwithpicto($langs->trans('AlertMailSmsForceStop'), $langs->trans('AlertMailSmsForStopInfo')).'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_STOP_ON_ERR">';
print $form->selectyesno('ALERTMAILSMS_STOP_ON_ERR', $conf->global->ALERTMAILSMS_PLATFORM, 1);
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("AlertMailSmsPlatformChoice").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_PLATFORM">';
print $form->selectarray('ALERTMAILSMS_PLATFORM', array('OVH' => 'OVH'), $conf->global->ALERTMAILSMS_PLATFORM);
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("AlertMailSmsSubjectMail").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_SUBJECT_MAIL">';
print '<input type="text" name="ALERTMAILSMS_SUBJECT_MAIL" size="55" value="'.$conf->global->ALERTMAILSMS_SUBJECT_MAIL.'">';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("AlertMailSmsCoreMsgMail").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_MSG_MAIL">';
print $form->textwithpicto('<textarea rows="4" cols="52" name="ALERTMAILSMS_MSG_MAIL" style="vertical-align:middle;">'.$conf->global->ALERTMAILSMS_MSG_MAIL.'</textarea><input type="submit" class="button" value="'.$langs->trans("Modify").'">', $langs->trans('AlertMailSms_Info_Mail'), -1);
print '</form>';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans('AlertMailSmsEnabledSms').'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_SEND_SMS_ENABLED">';
print $form->selectyesno('ALERTMAILSMS_SEND_SMS_ENABLED', $conf->global->ALERTMAILSMS_SEND_SMS_ENABLED, 1);
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("AlertMailSmsPhoneNum").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_SENDER">';
print $form->textwithpicto('<input type="text" name="ALERTMAILSMS_SENDER" size="54" value="'.$conf->global->ALERTMAILSMS_SENDER.'"><input type="submit" class="button" value="'.$langs->trans("Modify").'">', $langs->trans('AlertMailSms_Info_Phone_Number'), -1);
print '';
print '</form>';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("AlertMailSmsCoreMsgSms").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_MSG_SMS">';
print $form->textwithpicto('<textarea rows="4" cols="52" name="ALERTMAILSMS_MSG_SMS" style="vertical-align:middle;">'.$conf->global->ALERTMAILSMS_MSG_SMS.'</textarea><input type="submit" class="button" value="'.$langs->trans("Modify").'">', $langs->trans('AlertMailSms_Info_Sms'), -1);
print '</form>';
print '</td></tr>';

print '</table>';


//OVH
$var = false;
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$form->textwithpicto($langs->trans("OVH"), $langs->trans('OVH_info_required')).'</td>'."\n";
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="center" width="100">'.$langs->trans("Value").'</td>'."\n";

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("AlertMailSmsOvhApiKey").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_OVH_KEY">';
print '<input type="text" name="ALERTMAILSMS_OVH_KEY" size="55" value="'.$conf->global->ALERTMAILSMS_OVH_KEY.'">';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("AlertMailSmsOvhSecret").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_OVH_SECRET">';
print '<input type="text" name="ALERTMAILSMS_OVH_SECRET" size="55" value="'.$conf->global->ALERTMAILSMS_OVH_SECRET.'">';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("AlertMailSmsOvhConsumerKey").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_OVH_CONSUMER_KEY">';
print '<input type="text" name="ALERTMAILSMS_OVH_CONSUMER_KEY" size="55" value="'.$conf->global->ALERTMAILSMS_OVH_CONSUMER_KEY.'">';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

$TInfo = getInfoAccountOvh();

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("AlertMailSmsCompteSmsOvh").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_ALERTMAILSMS_COMPTE_SMS_OVH">';
print $form->selectarray('ALERTMAILSMS_COMPTE_SMS_OVH', $TInfo[0], $conf->global->ALERTMAILSMS_COMPTE_SMS_OVH);
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("AlertMailSmsOvhCredtsLeft").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="500">';
print $langs->trans('AlertMailSmsOvhCredtsLeftValue', $TInfo[1]);
print '</td></tr>';

print '</table>';

llxFooter();

$db->close();
