<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Browse Ajax
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
 * Sub-Ajax page, requires AJAX_INCLUDE
 */
require_once("../lib/init.php");
session_start();

if (!defined('AJAX_INCLUDE')) { exit; }

if (isset($_REQUEST['browse_id'])) {
	$browse_id = $_REQUEST['browse_id'];
}
else {
	$browse_id = null;
}

$browse = new Browse($browse_id);

switch ($_REQUEST['action']) {
	case 'browse':
		$object_ids = array();

		// Check 'value' with isset because it can null
		//(user type a "start with" word and deletes it)
		if ($_REQUEST['key'] && (isset($_REQUEST['multi_alpha_filter']) OR isset($_REQUEST['value']))) {
			// Set any new filters we've just added
			$browse->set_filter($_REQUEST['key'],$_REQUEST['multi_alpha_filter']);
			//$browse->set_catalog($_SESSION['catalog']);
		}

		if ($_REQUEST['sort']) {
			// Set the new sort value
			$browse->set_sort($_REQUEST['sort']);
		}
		if ($_REQUEST['catalog_key'] || $SESSION['catalog'] != 0) {
			echo ('REQUEST CATALOG');
			//$browse->set_filter('catalog',$_REQUEST['catalog_key']);
			//$_SESSION['catalog'] = $_REQUEST['catalog_key'];
		}

		ob_start();
                $browse->show_objects();
                $type = $browse->get_type();
				$type_container = "browse_content_" . $type;
                $results[$type_container] = ob_get_clean();
	break;
	
	case 'set_sort':
		if ($_REQUEST['sort']) {
			$browse->set_sort($_REQUEST['sort']);
		}

		ob_start();
		$browse->show_objects();
		$results['browse_content'] = ob_get_clean();
	break;
	
	/* Added */
	case 'sort_all':
		$browse = new Browse($_GET['browse_id']);
		$type = $browse->get_type();
		$browse->set_simple_browse(true);
		$browse->set_sort('name','ASC');
		ob_start();			
        $browse->show_objects();
		$id_container = "browse_content_" . $type;
        $results[$id_container] = ob_get_clean();

	break;

	case 'sort_new_item':		
		$browse = new Browse($_GET['browse_id']);
		$type = $browse->get_type();
		$object_ids = Stats::get_newest($type);
		ob_start();
		$browse->show_objects($object_ids); 
		$id_container = "browse_content_" . $type;       
        $results[$id_container] = ob_get_clean();
	break;
	
	case 'sort_popular_item':
		$browse = new Browse($_GET['browse_id']);
		$type = $browse->get_type();
		$object_ids = Stats::get_top($type);
		ob_start();
		$browse->show_objects($object_ids);  
		$type = $browse->get_type(); 
		$id_container = "browse_content_" . $type;       
        $results[$id_container] = ob_get_clean();
	break;


	case 'toggle_tag':
		$type = $_SESSION['tagcloud_type'] ? $_SESSION['tagcloud_type'] : 'song';
		$browse->set_type($type);
	break;
	case 'delete_object':
		switch ($_REQUEST['type']) {
			case 'playlist':
				// Check the perms we need to on this
				$playlist = new Playlist($_REQUEST['id']);
				if (!$playlist->has_access()) { exit; }

				// Delete it!
				$playlist->delete();
				$key = 'playlist_row_' . $playlist->id;
			break;
			case 'smartplaylist':
				$playlist = new Search('song', $_REQUEST['id']);
				if (!$playlist->has_access()) { exit; }
				$playlist->delete();
				$key = 'playlist_row_' . $playlist->id;
			break;
			case 'live_stream':
				if (!$GLOBALS['user']->has_access('75')) { exit; }
				$radio = new Radio($_REQUEST['id']);
				$radio->delete();
				$key = 'live_stream_' . $radio->id;
			break;
			default:

			break;
		} // end switch on type

		$results[$key] = '';

	break;
	case 'page':
		$browse->set_start($_REQUEST['start']);
		$type = $browse->get_type();
		if ($type =='album_tag') {
			$class = "browse_content_tag";
		} else {		
			$class = "browse_content_" . $type;
		}
		ob_start();
		$browse->show_objects();
		$results[$class] = ob_get_clean();
	break;
	case 'show_art':
		Art::set_enabled();
		ob_start();
		$browse->show_objects();
		$results['browse_content'] = ob_get_clean();
	break;
	case 'get_filters':
		ob_start();
		$type = $browse->get_type();
		require_once Config::get('prefix') . '/templates/browse_filters.inc.php';
		$results['browse_filters'] = ob_get_clean();
	default:
		$results['rfc3514'] = '0x1';
	break;
} // switch on action;

$browse->store();

// We always do this
echo xml_from_array($results);
?>
