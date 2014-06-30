<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Init Library
 *
 *
 * LICENSE: GNU General Public License, version 2 (GPLv2)
 * Copyright (c) 2001 - 2011 Ampache.org All Rights Reserved
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License v2
 * as published by the Free Software Foundation
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

// Use output buffering, this gains us a few things and
// fixes some CSS issues
ob_start();

// Do a check for PHP5 because nothing will work without it
if (floatval(phpversion()) < 5) {
	echo "ERROR: Ampache requires PHP5";
	exit;
}

error_reporting(E_ERROR); // Only show fatal errors in production

$ampache_path = dirname(__FILE__);
$prefix = realpath($ampache_path . "/../");
$configfile = "$prefix/config/ampache.cfg.php";
require_once $prefix . '/lib/general.lib.php';
require_once $prefix . '/lib/class/config.class.php';
require_once $prefix . '/lib/class/vauth.class.php'; // Fixes synology bug with __autoload in certain cases

if (!function_exists('gettext')) {
	require_once $prefix . '/modules/emulator/gettext.php';
}

// Define some base level config options
Config::set('prefix',$prefix);

/*
 Check to see if this is http or https
*/
if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || Config::get('force_ssl') == true) {
	$http_type = "https://";
}
else {
	$http_type = "http://";
}

/*
 Check to make sure the config file exists. If it doesn't then go ahead and send
 them over to the install script.
*/
if (!file_exists($configfile)) {
	$path = preg_replace("/(.*)\/(\w+\.php)$/","\${1}", $_SERVER['PHP_SELF']);
	$link = $http_type . $_SERVER['HTTP_HOST'] . $path . "/install.php";
	header ("Location: $link");
	exit();
}

// Use the built in PHP function, suppress errors here so we can handle it
// properly below
$results = @parse_ini_file($configfile);

if (!count($results)) {
	$path = preg_replace("/(.*)\/(\w+\.php)$/","\${1}", $_SERVER['PHP_SELF']);
	$link = $http_type . $_SERVER['HTTP_HOST'] . $path . "/test.php?action=config";
	header ("Location: $link");
	exit();
}

/** Verify a few commonly disabled PHP functions exist and re-direct to /test if not **/
if (!function_exists('hash') OR !function_exists('inet_pton') OR (strtoupper(substr(PHP_OS,0,3)) == 'WIN' AND floatval(phpversion()) < 5.3)) {
	$path = preg_replace("/(.*)\/(\w+\.php)$/","\${1}", $_SERVER['PHP_SELF']);
	$link = $http_type . $_SERVER['HTTP_HOST'] . $path . "/test.php";
	header ("Location: $link");
	exit();
}

/** This is the version.... fluf nothing more... **/
$results['version']		= '3.6-Alpha1-DEV';
$results['int_config_version']	= '11';

$results['raw_web_path']	= $results['web_path'];
$results['web_path']		= $http_type . $_SERVER['HTTP_HOST'] . $results['web_path'];
$results['http_port']		= $_SERVER['SERVER_PORT'];
if (!$results['http_port']) {
	$results['http_port']	= '80';
}
if (!$results['site_charset']) {
	$results['site_charset'] = "UTF-8";
}
if (!$results['raw_web_path']) {
	$results['raw_web_path'] = '/';
}
if (!$_SERVER['SERVER_NAME']) {
	$_SERVER['SERVER_NAME'] = '';
}
if (isset($results['user_ip_cardinality']) && !$results['user_ip_cardinality']) {
	$results['user_ip_cardinality'] = 42;
}

/* Variables needed for vauth class */
$results['cookie_path'] 	= $results['raw_web_path'];
$results['cookie_domain']	= $_SERVER['SERVER_NAME'];
$results['cookie_life']		= $results['session_cookielife'];
$results['cookie_secure']	= $results['session_cookiesecure'];
$results['mysql_password']	= $results['database_password'];
$results['mysql_username']	= $results['database_username'];
$results['mysql_hostname']	= $results['database_hostname'];
$results['mysql_db']		= $results['database_name'];

// Define that we've loaded the INIT file
define('INIT_LOADED','1');

// Library and module includes we can't do with the autoloader
require_once $prefix . '/lib/preferences.php';
require_once $prefix . '/lib/log.lib.php';
require_once $prefix . '/lib/ui.lib.php';
require_once $prefix . '/lib/gettext.php';
require_once $prefix . '/lib/batch.lib.php';
require_once $prefix . '/lib/themes.php';
require_once $prefix . '/lib/class/localplay.abstract.php';
require_once $prefix . '/lib/class/database_object.abstract.php';
require_once $prefix . '/lib/class/playlist_object.abstract.php';
require_once $prefix . '/lib/class/media.interface.php';
require_once $prefix . '/modules/getid3/getid3.php';
require_once $prefix . '/modules/php_thumb/ThumbLib.inc.php';
require_once $prefix . '/modules/nusoap/nusoap.php';
require_once $prefix . '/modules/phpmailer/class.phpmailer.php';
require_once $prefix . '/modules/phpmailer/class.smtp.php';
require_once $prefix . '/modules/infotools/Snoopy.class.php';
require_once $prefix . '/modules/infotools/AmazonSearchEngine.class.php';
require_once $prefix . '/modules/infotools/lastfm.class.php';
//require_once $prefix . '/modules/infotools/jamendoSearch.class.php';
require_once $prefix . '/modules/php_musicbrainz/mbQuery.php';
require_once $prefix . '/modules/pclzip/pclzip.lib.php';
/* Temp Fixes */
$results = Preference::fix_preferences($results);

Config::set_by_array($results,1);

// Modules (These are conditionally included depending upon config values)
if (Config::get('ratings')) {
	require_once $prefix . '/lib/rating.lib.php';
}

/* Set a new Error Handler */
$old_error_handler = set_error_handler('ampache_error_handler');

/* Check their PHP Vars to make sure we're cool here */
$post_size = @ini_get('post_max_size');
if (substr($post_size,strlen($post_size)-1,strlen($post_size)) != 'M') {
	/* Sane value time */
	ini_set('post_max_size','8M');
}

// In case the local setting is 0
ini_set('session.gc_probability','5');

if (! isset($results['memory_limit']) || $results['memory_limit'] < 24) {
	$results['memory_limit'] = 24;
}

set_memory_limit($results['memory_limit']);

/**** END Set PHP Vars ****/

// If we want a session
if (!defined('NO_SESSION') && Config::get('use_auth')) {
	/* Verify their session */
	if (!vauth::session_exists('interface',$_COOKIE[Config::get('session_name')])) { vauth::logout($_COOKIE[Config::get('session_name')]); exit; }

	// This actually is starting the session
	vauth::check_session();

	/* Create the new user */
	$GLOBALS['user'] = User::get_from_username($_SESSION['userdata']['username']);

	/* If the user ID doesn't exist deny them */
	if (!$GLOBALS['user']->id AND !Config::get('demo_mode')) { vauth::logout(session_id()); exit; }

	vauth::session_extend(session_id());

	/* Load preferences and theme */
	$GLOBALS['user']->update_last_seen();
}
elseif (!Config::get('use_auth')) {

	/*if (defined('ADMIN_LOG')) {
		echo ('admin_log');
		vauth::check_session();
		$GLOBALS['user'] = User::get_from_username($_SESSION['userdata']['username']);
		vauth::session_extend(session_id());
	} else {*/
	
	if (!vauth::session_exists('interface',$_COOKIE[Config::get('session_name')])) {
		//echo ('ij');
		vauth::create_cookie();
		$auth['success'] = 1;
		$auth['username'] = 'User';
		$auth['fullname'] = "Ampache User";
		$auth['id'] = -1;
		$auth['type'] = mysql;
		$auth['offset_limit'] = 50;
		$auth['access'] = Config::get('default_auth_level') ? User::access_name_to_level(Config::get('default_auth_level')) : '100';

		vauth::session_create($auth);
		$_SESSION['userdata'] = $auth;
		vauth::check_session();
		$GLOBALS['user'] = User::get_from_username(User);
	}
	else {

		vauth::check_session();
		if ($_SESSION['userdata']['username']) {
			$GLOBALS['user'] = User::get_from_username($_SESSION['userdata']['username']);
			vauth::session_extend(session_id());
			
		}
		else {
			$GLOBALS['user'] = new User($auth['username']);
			$GLOBALS['user']->id = '-1';
			$GLOBALS['user']->username = $auth['username'];
			$GLOBALS['user']->fullname = $auth['fullname'];
			$GLOBALS['user']->access = $auth['access'];
		}
	} // si pas admin
	/*}*/
		if (!$GLOBALS['user']->id AND !Config::get('demo_mode')) { vauth::logout(session_id()); exit; }
		$GLOBALS['user']->update_last_seen();
echo ($auth['username']);	
}
// If Auth, but no session is set
else {
	if (isset($_REQUEST['sid'])) {
		session_name(Config::get('session_name'));
		session_id(scrub_in($_REQUEST['sid']));
		session_start();
		$GLOBALS['user'] = User::get_from_username($_SESSION['userdata']['username']);
	}
	else {
		$GLOBALS['user'] = new User();
	}

} // If NO_SESSION passed

// Load the Preferences from the database
Preference::init();

// We need to create the tmp playlist for our user only if we have a session
if (session_id()) {
	$GLOBALS['user']->load_playlist();
}

/* Add in some variables for ajax done here because we need the user */
Config::set('ajax_url',Config::get('web_path') . '/server/ajax.server.php',1);

// Load gettext mojo
load_gettext();

/* Set CHARSET */
header ("Content-Type: text/html; charset=" . Config::get('site_charset'));

/* Clean up a bit */
unset($array);
unset($results);

/* Set up the flip class */
flip_class(array('odd','even'));

/* Check to see if we need to perform an update */
if (!preg_match('/update\.php/', $_SERVER['PHP_SELF'])) {
	if (Update::need_update()) {
		header("Location: " . Config::get('web_path') . "/update.php");
		exit();
	}
}
// For the XMLRPC stuff
$GLOBALS['xmlrpc_internalencoding'] = Config::get('site_charset');

// If debug is on GIMMIE DA ERRORS
if (Config::get('debug')) {
	error_reporting(E_ALL);
}

// Merge GET then POST into REQUEST effectively stripping COOKIE without
// depending on a PHP setting change for the effect
$_REQUEST = array_merge($_GET,$_POST);
?>
