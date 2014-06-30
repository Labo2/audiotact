<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * XML Server
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
 * Audiotact is an Ampache-based project developped by Oudeis (www.oudeis.fr) with the support of the Labo2 (www.bibliotheque.nimes.fr)

 */

/**
 * This is accessed remotly to allow outside scripts access to ampache information
 * as such it needs to verify the session id that is passed
 */
define('NO_SESSION','1');
require_once '../lib/init.php';

// If it's not a handshake then we can allow it to take up lots of time
if ($_REQUEST['action'] != 'handshake') {
	set_time_limit(0);
}

/* Set the correct headers */
header("Content-type: text/xml; charset=" . Config::get('site_charset'));
header("Content-Disposition: attachment; filename=information.xml");

// If we don't even have access control on then we can't use this!
if (!Config::get('access_control')) {
	ob_end_clean();
	debug_event('Access Control','Error Attempted to use XML API with Access Control turned off','3');
	echo xmlData::error('501',_('Access Control not Enabled'));
	exit;
}

/**
 * Verify the existance of the Session they passed in we do allow them to
 * login via this interface so we do have an exception for action=login
 */
if (!vauth::session_exists('api', $_REQUEST['auth']) AND $_REQUEST['action'] != 'handshake' AND $_REQUEST['action'] != 'ping') {
        debug_event('Access Denied','Invalid Session attempt to API [' . $_REQUEST['action'] . ']','3');
        ob_end_clean();
        echo xmlData::error('401',_('Session Expired'));
        exit();
}

// If the session exists then let's try to pull some data from it to see if we're still allowed to do this
$session = vauth::get_session_data($_REQUEST['auth']);
$username = ($_REQUEST['action'] == 'handshake' || $_REQUEST['action'] == 'ping') ? $_REQUEST['user'] : $session['username'];

if (!Access::check_network('init-api',$username,'5')) {
        debug_event('Access Denied','Unauthorized access attempt to API [' . $_SERVER['REMOTE_ADDR'] . ']', '3');
        ob_end_clean();
        echo xmlData::error('403',_('Unauthorized access attempt to API - ACL Error'));
        exit();
}

if ($_REQUEST['action'] != 'handshake' AND $_REQUEST['action'] != 'ping') {
        vauth::session_extend($_REQUEST['auth']);
        $GLOBALS['user'] = User::get_from_username($session['username']);
}

// Get the list of possible methods for the Ampache API
$methods = get_class_methods('api');

// Define list of internal functions that should be skipped
$internal_functions = array('set_filter');

// Recurse through them and see if we're calling one of them
foreach ($methods as $method) {
	if (in_array($method,$internal_functions)) { continue; }

	// If the method is the same as the action being called
	// Then let's call this function!
	if ($_GET['action'] == $method) {
		call_user_func(array('api',$method),$_GET);
		// We only allow a single function to be called, and we assume it's cleaned up!
		exit;
	}

} // end foreach methods in API

// If we manage to get here, we still need to hand out an XML document
ob_end_clean();
echo xmlData::error('405',_('Invalid Request'));
