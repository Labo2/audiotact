<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Shout
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

require_once 'lib/init.php';

show_header();

// Switch on the incomming action
switch ($_REQUEST['action']) {
	case 'add_shout':
		// Must be at least a user to do this
		if (!Access::check('interface','25')) {
			access_denied();
			exit;
		}

		if (!Core::form_verify('add_shout','post')) {
			access_denied();
			exit;
		}

		$shout_id = shoutBox::create($_POST);
		$return_album =  $_POST['object_id'];
		header("Location:" . Config::get('web_path') . '/albums.php?action=show&album='.$return_album);
	break;
	case 'show_add_shout':
		// Get our object first
		$object = shoutBox::get_object($_REQUEST['type'],$_REQUEST['id']);

		if (!$object->id) {
			Error::add('general',_('Invalid Object Selected'));
			Error::display('general');
			break;
		}

		// Now go ahead and display the page where we let them add a comment etc
		require_once Config::get('prefix') . '/templates/show_add_shout.inc.php';
	break;
	default:
		header("Location:" . Config::get('web_path'));
	break;
} // end switch on action

show_footer();
?>
