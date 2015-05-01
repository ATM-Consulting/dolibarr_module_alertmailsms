<?php

require('config.php');
require('./class/alertmailsms.class.php');
require('./lib/alertmailsms.lib.php');

dol_include_once('/core/lib/contact.lib.php');
dol_include_once('/contact/class/contact.class.php');

// Load translation files required by the page
$langs->load("alertmailsms@alertmailsms");

// Get parameters
$id = GETPOST('id', 'int');
$action = GETPOST('action', 'alpha');

// Access control
if (!$user->rights->societe->contact->creer) {
	accessforbidden();
}

// Default action
if (empty($action) && empty($id) && empty($ref)) {
	$action='view';
}

$object = new TContact($db);
if ($id > 0)
{
	$object->fetch($id);
}

$form = new Form($db);
llxHeader('', $langs->trans('AlertMailSmsTabContact'), '');

$head = contact_prepare_head($object);
dol_fiche_head($head, 'AlertMailSms', $langs->trans('AlertMailSmsTabContact'),0,'contact');

/*
 * ACTIONS
 *
 * Put here all code to do according to value of "action" parameter
 */
if ($action == 'update' && !GETPOST('cancel') && $user->rights->societe->contact->creer) 
{
	/*
	 * UPDATE
	 */
	 
	$action='view';
	
	$alert_mail = GETPOST('alert_mail', 'int');
	$alert_sms = GETPOST('alert_sms', 'int');
	
	$object->setAlertAttributes($alert_mail, $alert_sms);
}

if ($action == 'edit')
{
	/*
	 * EDIT
	 */
	 
	print '<form name="perso" method="POST" action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="update">';
    print '<input type="hidden" name="id" value="'.$object->id.'">';
	
	print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

    print '<table class="border" width="100%">';

    $linkback = '<a href="'.DOL_URL_ROOT.'/contact/list.php">'.$langs->trans("BackToList").'</a>';

    // Ref
    print '<tr><td width="20%">'.$langs->trans("Ref").'</td><td colspan="3">';
    print $form->showrefnav($object, 'id', $linkback);
    print '</td></tr>';

    // Name
    print '<tr><td width="20%">'.$langs->trans("Lastname").' / '.$langs->trans("Label").'</td><td width="30%">'.$object->lastname.'</td>';
    print '<td width="20%">'.$langs->trans("Firstname").'</td><td width="30%">'.$object->firstname.'</td></tr>';

	// Alert inputs
	print '<tr><td width="20%">'.$langs->trans("AlertMailSms_Mail").'</td><td width="30%">'.$form->selectyesno('alert_mail', $object->alert_mail, 1).'</td>';
    print '<td width="20%">'.$langs->trans("AlertMailSms_Sms").'</td><td width="30%">'.$form->selectyesno('alert_sms', $object->alert_sms, 1).'</td></tr>';

	print "</table><br>";
	
	print '<center>';
    print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
    print ' &nbsp; ';
    print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
    print '</center>';
	
    print "</form>";
}
else 
{
	/*
	 * VIEW
	 */
	
	print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

    print '<table class="border" width="100%">';

    $linkback = '<a href="'.DOL_URL_ROOT.'/contact/list.php">'.$langs->trans("BackToList").'</a>';

    // Ref
    print '<tr><td width="20%">'.$langs->trans("Ref").'</td><td colspan="3">';
    print $form->showrefnav($object, 'id', $linkback);
    print '</td></tr>';

    // Name
    print '<tr><td width="20%">'.$langs->trans("Lastname").' / '.$langs->trans("Label").'</td><td width="30%">'.$object->lastname.'</td>';
    print '<td width="20%">'.$langs->trans("Firstname").'</td><td width="30%">'.$object->firstname.'</td></tr>';

	// Alert inputs
	print '<tr><td width="20%">'.$langs->trans("AlertMailSms_Mail").'</td><td width="30%">'.($object->alert_mail ? $langs->trans('Yes') : $langs->trans('No')).'</td>';
    print '<td width="20%">'.$langs->trans("AlertMailSms_Sms").'</td><td width="30%">'.($object->alert_sms ? $langs->trans('Yes') : $langs->trans('No')).'</td></tr>';

	print "</table>";
}

dol_fiche_end();

if ($action != 'edit')
{
    print '<div class="tabsAction">';

    if ($user->rights->societe->contact->creer)
    {
        print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&amp;action=edit">'.$langs->trans('Modify').'</a>';
    }

    print "</div>";
}
		
// End of page
llxFooter();
