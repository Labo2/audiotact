<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Admin Users
 *
 *
 * LICENSE: GNU General Public License, version 2 (GPLv2)
 * Copyright (c) 2001 - 2011 Ampache.org All Rights Reserved
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License v2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @package	Ampache
 * @copyright	2001 - 2011 Ampache.org
 * @license	http://opensource.org/licenses/gpl-2.0 GPLv2
 * @link	http://www.ampache.org/
 * 
 * Audiotact is an Ampache-based project developped by Oudeis (www.oudeis.fr) with the support of Labo2 (www.bibliotheque.nimes.fr)
 */

require_once '../lib/init.php';

if (!Access::check('interface','100')) {
	access_denied();
	exit();
}

show_header();

// Switch on the actions
switch ($_REQUEST['action']) {
	case 'update_user':
		if (Config::get('demo_mode')) { break; }

		if (!Core::form_verify('edit_user','post')) {
			access_denied();
			exit;
		}

		/* Clean up the variables */
		$user_id	= scrub_in($_POST['user_id']);
		$username 	= scrub_in($_POST['username']);
		$fullname 	= scrub_in($_POST['fullname']);
		$email 		= scrub_in($_POST['email']);
		$access 	= scrub_in($_POST['access']);
		$pass1 		= $_POST['password_1'];
		$pass2 		= $_POST['password_2'];

		/* Setup the temp user */
		$client = new User($user_id);

		/* Verify Input */
		if (empty($username)) {
			Error::add('username',_("Error Username Required"));
		}
		if ($pass1 !== $pass2 && !empty($pass1)) {
			Error::add('password',_("Error Passwords don't match"));
		}

		/* If we've got an error then break! */
		if (Error::occurred()) {
			$_REQUEST['action'] = 'show_edit';
			break;
		} // if we've had an oops!

		if ($access != $client->access) {
			$client->update_access($access);
		}
		if ($email != $client->email) {
			$client->update_email($email);
		}
		if ($username != $client->username) {
			$client->update_username($username);
		}
		if ($fullname != $client->fullname) {
			$client->update_fullname($fullname);
		}
		if ($pass1 == $pass2 && strlen($pass1)) {
			$client->update_password($pass1);
		}

		show_confirmation(_('User Updated'), $client->fullname . "(" . $client->username . ")" . _('updated'), Config::get('web_path'). '/admin/users.php');
	break;
	case 'add_user':
		if (Config::get('demo_mode')) { break; }

		if (!Core::form_verify('add_user','post')) {
			access_denied();
			exit;
		}

		$username	= scrub_in($_POST['username']);
		$fullname	= scrub_in($_POST['fullname']);
		$email		= scrub_in($_POST['email']);
		$access		= scrub_in($_POST['access']);
		$pass1		= $_POST['password_1'];
		$pass2		= $_POST['password_2'];

		if ($pass1 !== $pass2 || !strlen($pass1)) {
			Error::add('password',_("Error Passwords don't match"));
		}

		if (empty($username)) {
			Error::add('username',_('Error Username Required'));
		}

		/* make sure the username doesn't already exist */
		if (!User::check_username($username)) {
			Error::add('username',_('Error Username already exists'));
		}

		if (!Error::occurred()) {
			/* Attempt to create the user */
			$user_id = User::create($username, $fullname, $email, $pass1, $access);
			if (!$user_id) {
				Error::add('general',_("Error: Insert Failed"));
			}

		} // if no errors
		else {
			$_REQUEST['action'] = 'show_add_user';
			break;
		}
		if ($access == 5){ $access = _('Guest');}
		elseif ($access == 25){ $access = _('User');}
		elseif ($access == 100){ $access = _('Admin');}

		/* HINT: %1 Username, %2 Access num */
		show_confirmation(_('New User Added'),sprintf(_('%1$s has been created with an access level of %2$s'), $username, $access), Config::get('web_path').'/admin/users.php');
	break;
	case 'enable':
		$client = new User($_REQUEST['user_id']);
		$client->enable();
		show_confirmation(_('User Enabled'),$client->fullname . ' (' . $client->username . ')', Config::get('web_path'). '/admin/users.php');
	break;
	case 'disable':
		$client = new User($_REQUEST['user_id']);
		if ($client->disable()) {
			show_confirmation(_('User Disabled'),$client->fullname . ' (' . $client->username . ')', Config::get('web_path'). '/admin/users.php');
		}
		else {
			show_confirmation(_('Error'),_('Unable to Disabled last Administrator'), Config::get('web_path').'/admin/users.php');
		}
	break;
	case 'show_edit':
		if (Config::get('demo_mode')) { break; }
		$client	= new User($_REQUEST['user_id']);
		require_once Config::get('prefix') . '/templates/show_edit_user.inc.php';
	break;
	case 'confirm_delete':
		if (Config::get('demo_mode')) { break; }
		if (!Core::form_verify('delete_user')) {
			access_denied();
			exit;
		}
		$client = new User($_REQUEST['user_id']);
		if ($client->delete()) {
			show_confirmation(_('User Deleted'), sprintf(_('%s has been Deleted'), $client->username), Config::get('web_path'). "/admin/users.php");
		}
		else {
			show_confirmation(_('Delete Error'), _("Unable to delete last Admin User"), Config::get('web_path')."/admin/users.php");
		}
	break;
	case 'delete':
		if (Config::get('demo_mode')) { break; }
		$client = new User($_REQUEST['user_id']);
		show_confirmation(_('Deletion Request'),
			sprintf(_('Are you sure you want to permanently delete %s?'), $client->fullname),
			Config::get('web_path')."/admin/users.php?action=confirm_delete&amp;user_id=" . $_REQUEST['user_id'],1,'delete_user');
	break;
	/* Show IP History for the Specified User */
	case 'show_ip_history':
		/* get the user and their history */
		$working_user	= new User($_REQUEST['user_id']);

		if (!isset($_REQUEST['all'])){
			$history	= $working_user->get_ip_history(0,1);
		}
		else {
			$history	= $working_user->get_ip_history();
		}
		require Config::get('prefix') . '/templates/show_ip_history.inc.php';
	break;
	case 'show_add_user':
			if (Config::get('demo_mode')) { break; }
		require_once Config::get('prefix') . '/templates/show_add_user.inc.php';
	break;
	case 'show_preferences':
		$client = new User($_REQUEST['user_id']);
		$preferences = Preference::get_all($client->id);
		require_once Config::get('prefix') . '/templates/show_user_preferences.inc.php';
	break;
	default:
		$browse = new Browse();
		$browse->reset_filters();
		$browse->set_type('user');
		$browse->set_simple_browse(1);
		$browse->set_sort('name','ASC');
		$user_ids = $browse->get_objects();
		$browse->show_objects($user_ids);
		$browse->store();
	break;
} // end switch on action

/* Show the footer */
show_footer();

?>
