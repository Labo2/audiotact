<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Admin Shout
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
	exit;
}

show_header();

// Switch on the incomming action
switch ($_REQUEST['action']) {
	case 'edit_shout':
		$shout_id = $_POST['shout_id'];
		$update = shoutBox::update($_POST);
		show_confirmation(_('Shoutbox Post Updated'),'',Config::get('web_path').'/admin/shout.php');
	break;
	case 'show_edit':
		$shout = new shoutBox($_REQUEST['shout_id']);
		$object = shoutBox::get_object($shout->object_type,$shout->object_id);
		$object->format();
		$client = new User($shout->user);
		$client->format();
		require_once Config::get('prefix') . '/templates/show_edit_shout.inc.php';
		break;
	case 'delete':
		$shout_id = shoutBox::delete($_REQUEST['shout_id']);
		show_confirmation(_('Shoutbox Post Deleted'),'',Config::get('web_path').'/admin/shout.php');
	break;
	default:
		$browse = new Browse();
		$browse->set_type('shoutbox');
		$browse->set_simple_browse(true);
		$shoutbox_ids = $browse->get_objects();
		$browse->show_objects($shoutbox_ids);
		$browse->store();
	break;
} // end switch on action

show_footer();
?>
