<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Logging Library
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

/*
 * log_event
 * Logs an event to a defined log file based on config options
 */
function log_event($username, $event_name, $event_description, $log_name) {
	/* Set it up here to make sure it's _always_ the same */
	$time		= time();
	// Turn time into strings
	$log_day	= date('Ymd', $time);
	$log_time	= date('Y-m-d H:i:s', $time);

	/* must have some name */
	$log_name	= $log_name ? $log_name : 'ampache';
	$username	= $username ? $username : 'ampache';

	$log_filename	= Config::get('log_path') . "/$log_name.$log_day.log";
	$log_line	= "$log_time [$username] ($event_name) -> $event_description \n";

	// Do the deed
	$log_write = error_log($log_line, 3, $log_filename);

	if (!$log_write) {
		echo "Warning: Unable to write to log ($log_filename) Please check your log_path variable in ampache.cfg.php";
	}

} // log_event

/*
 * ampache_error_handler
 * An error handler for ampache that traps as many errors as it can and logs
 * them.
*/
function ampache_error_handler($errno, $errstr, $errfile, $errline) {

	/* Default level of 1 */
	$level = 1;

	switch ($errno) {
		case E_WARNING:
			$error_name = 'Runtime Error';
		break;
		case E_COMPILE_WARNING:
		case E_NOTICE:
		case E_CORE_WARNING:
			$error_name = 'Warning';
			$level = 6;
		break;
		case E_ERROR:
			$error_name = 'Fatal run-time Error';
		break;
		case E_PARSE:
			$error_name = 'Parse Error';
		break;
		case E_CORE_ERROR:
			$error_name = 'Fatal Core Error';
		break;
		case E_COMPILE_ERROR:
			$error_name = 'Zend run-time Error';
		break;
		case E_STRICT:
			$error_name = "Strict Error";
		break;
		default:
			$error_name = "Error";
			$level = 2;
		break;
	} // end switch

	// List of things that should only be displayed if they told us to turn
	// on the firehose
	$ignores = array(
		// We know var is deprecated, shut up
		'var: Deprecated. Please use the public/private/protected modifiers',
		// getid3 spews errors, yay!
		'getimagesize() [',
		'Non-static method getid3',
		'Assigning the return value of new by reference is deprecated',
		// The XML-RPC lib is broken (kinda)
		'used as offset, casting to integer'
	);

	foreach($ignores as $ignore) {
		if (strpos($errstr, $ignore) !== false) {
			$error_name = 'Ignored ' . $error_name;
			$level = 6;
		}
	}

	if (strpos($errstr,"date.timezone") !== false) {
		$error_name = 'Warning';
		$errstr = 'You have not set a valid timezone (date.timezone) in your php.ini file. This may cause display issues with dates. This warning is non-critical and not caused by Ampache.';
	}

	$log_line = "[$error_name] $errstr in file $errfile($errline)";
	debug_event('PHP', $log_line, $level, '', 'ampache');

} // ampache_error_handler

/**
 * debug_event
 * This function is called inside ampache, it's actually a wrapper for the
 * log_event. It checks for conf('debug') and conf('debug_level') and only
 * calls log event if both requirements are met.
 */
function debug_event($type, $message, $level, $file = '', $username = '') {

	if (!Config::get('debug') || $level > Config::get('debug_level')) {
		return false;
	}

	if (!$username && isset($GLOBALS['user'])) {
		$username = $GLOBALS['user']->username;
	}

	log_event($username, $type, $message, $file);

} // debug_event

?>
