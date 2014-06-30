<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Login
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

define('NO_SESSION', '1');
//define('ADMIN_LOG','1');
require_once 'lib/init.php';

/* We have to create a cookie here because IIS
 * can't handle Cookie + Redirect
 */
vauth::create_cookie();
Preference::init();

/**
 * If Access Control is turned on then we don't
 * even want them to be able to get to the login
 * page if they aren't in the ACL
 */
if (Config::get('access_control')) {
	if (!Access::check_network('interface', '', '5')) {
		debug_event('access_denied', 'Access Denied:' . $_SERVER['REMOTE_ADDR'] . ' is not in the Interface Access list', '3');
		access_denied();
		exit();
	}
} // access_control is enabled

/* Clean Auth values */

unset($auth);

/* Check for posted username and password, or appropriate environment
variable if using HTTP auth */
if (($_POST['username'] && $_POST['password']) ||
	(in_array('http', Config::get('auth_methods')) &&
	($_SERVER['REMOTE_USER'] || $_SERVER['HTTP_REMOTE_USER']))) {

	if ($_POST['rememberme']) {
		vauth::create_remember_cookie();
	}

	/* If we are in demo mode let's force auth success */
	if (Config::get('demo_mode')) {
		$auth['success']		= true;
		$auth['info']['username']	= 'Admin - DEMO';
		$auth['info']['fullname']	= 'Administrative User';
		$auth['info']['offset_limit']	= 25;
	}
	else {
		if ($_POST['username'] && $_POST['password']) {
			$username = scrub_in($_POST['username']);
			$password = $_POST['password'];
		}
		else {
			if ($_SERVER['REMOTE_USER']) {
				$username = $_SERVER['REMOTE_USER'];
			}
			elseif ($_SERVER['HTTP_REMOTE_USER']) {
				$username = $_SERVER['HTTP_REMOTE_USER'];
			}
			$password = '';
		}

		$auth = vauth::authenticate($username, $password);
		
		if ($auth['success']) {
			$username = $auth['username'];
		}
		else {
			debug_event('Login', scrub_out($username) . ' attempted to login and failed', '1');
			Error::add('general', _('Error Username or Password incorrect, please try again'));
		}

		$user = User::get_from_username($username);

		if ($user->disabled) {
			$auth['success'] = false;
			Error::add('general', _('User Disabled please contact Admin'));
			debug_event('Login', scrub_out($username) . ' is disabled and attempted to login', '1');
		} // if user disabled
		elseif (Config::get('prevent_multiple_logins')) {
			$session_ip = $user->is_logged_in();
			$current_ip = inet_pton($_SERVER['REMOTE_ADDR']);
			if ($current_ip && ($current_ip != $session_ip)) {
				$auth['success'] = false;
				Error::add('general',_('User Already Logged in'));
				debug_event('Login', scrub_out($username) . ' is already logged in from ' . $session_ip . ' and attempted to login from ' . $current_ip, '1');
			} // if logged in multiple times
		} // if prevent multiple logins
		elseif (Config::get('auto_create') && $auth['success'] &&
			! $user->username) {
			/* This is run if we want to autocreate users who don't
			exist (useful for non-mysql auth) */
			$access	= Config::get('auto_user') 
				? User::access_name_to_level(Config::get('auto_user')) 
				: '5';
			$name	= $auth['name'];
			$email	= $auth['email'];

			/* Attempt to create the user */
			if (User::create($username, $name, $email, 
				hash('sha256', mt_rand()), $access)) {
				$user = User::get_from_username($username);
			}
			else {
				$auth['success'] = false;
				Error::add('general', _('Unable to create local account'));
			}
		} // End if auto_create

	} // if we aren't in demo mode

} // if they passed a username/password

/* If the authentication was a success */
if ($auth['success']) {
	// $auth->info are the fields specified in the config file
	//   to retrieve for each user
	$key = session_id();
	vauth::destroy($key);
	
	vauth::session_create($auth);

	// Not sure if it was me or php tripping out,
	//   but naming this 'user' didn't work at all
	$_SESSION['userdata'] = $auth;

	// Record the IP of this person!
	if (Config::get('track_user_ip')) {
		$user->insert_ip_history();
	}

	/* Make sure they are actually trying to get to this site and don't try 
	 * to redirect them back into an admin section
	 */
	$web_path = Config::get('web_path');
	if ((substr($_POST['referrer'], 0, strlen($web_path)) == $web_path) &&
		strpos($_POST['referrer'], 'install.php')	=== false &&
		strpos($_POST['referrer'], 'login.php')		=== false &&
		strpos($_POST['referrer'], 'logout.php')	=== false &&
		strpos($_POST['referrer'], 'update.php')	=== false &&
		strpos($_POST['referrer'], 'activate.php')	=== false &&
		strpos($_POST['referrer'], 'admin')		=== false ) {

			header('Location: ' . $_POST['referrer']);
			exit();
	} // if we've got a referrer
	header('Location: ' . Config::get('web_path') . '/index.php');
	exit();
} // auth success

require Config::get('prefix') . '/templates/show_login_form.inc.php';

?>
