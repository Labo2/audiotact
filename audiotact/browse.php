<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Browse
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
 */
//Audiotact is an Ampache-based project developped by Oudeis (www.oudeis.fr) with the support of Labo2 (www.bibliotheque.nimes.fr)
/**
 * Browse Page
 * This page shows the browse menu, which allows you to browse by many different
 * fields including artist, album, and catalog.
 *
 * This page also handles the actual browse action
 */

/* Base Require */
require_once 'lib/init.php';

session_start();

// This page is a little wonky we don't want the sidebar until we know what
// type we're dealing with so we've got a little switch here that creates the
// type.. this feels hackish...
$browse = new Browse();
switch ($_REQUEST['action']) {
	case 'tag':
	case 'file':
	case 'album':
	case 'artist':
	case 'playlist':
	case 'smartplaylist':
	case 'live_stream':
	case 'video':
	case 'song':
		$browse->set_type($_REQUEST['action']);
		$browse->set_simple_browse(true);
	break;
} // end switch

show_header();
$web_path = Config::get('web_path');
echo ('BROWSE');
switch($_REQUEST['action']) {
	case 'file':
	break;
	case 'album':
		$browse->set_filter('catalog',$_SESSION['catalog']);
		$browse->set_sort('name','ASC');
		$browse->show_objects();
	break;
	case 'tag':
		$browse->set_sort('count','ASC');
		// This one's a doozy
		$browse->set_simple_browse(false);
		$browse->save_objects(Tag::get_tags(Config::get('offset_limit'),array()));
		$object_ids = $browse->get_saved();
		$keys = array_keys($object_ids);
		Tag::build_cache($keys);
		show_box_top(_('Tag Cloud'),$class);
		$browse2 = new Browse();
		$browse2->set_type('song');
		$browse2->store();
		require_once Config::get('prefix') . '/templates/show_tagcloud.inc.php';
		show_box_bottom();
		require_once Config::get('prefix') . '/templates/browse_content.inc.php';
	break;
	case 'artist':
		$browse->set_filter('catalog',$_SESSION['catalog']);
		$browse->set_sort('name','ASC');
		$browse->show_objects();
	break;
	case 'song':
		$browse->set_filter('catalog',$_SESSION['catalog']);
		$browse->set_sort('title','ASC');
		$browse->show_objects();
	break;
	case 'live_stream':
		$browse->set_sort('name','ASC');
		$browse->show_objects();
	break;
	case 'catalog':
	break;
	case 'playlist':
		$browse->set_sort('type','ASC');
		$browse->set_filter('playlist_type','1');
		$browse->show_objects();
	break;
	case 'smartplaylist':
		$browse->set_sort('type', 'ASC');
		$browse->set_filter('playlist_type','1');
		$browse->show_objects();
	break;
	case 'video':
		$browse->set_sort('title','ASC');
		$browse->show_objects();
	break;
	default:
	break;
} // end Switch $action

$browse->store();

/* Show the Footer */
show_footer();
?>
