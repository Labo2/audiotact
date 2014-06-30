<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Tag Ajax
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
if (!defined('AJAX_INCLUDE')) { exit; }

switch ($_REQUEST['action']) {
	case 'show_add_tag':

	break;
	case 'add_tag':
		//Tag::add_tag_map($_GET['type'],$_GET['object_id'],$_GET['tag_id']);
	break;
	case 'remove_tag':
		$tag = new Tag($_GET['tag_id']);
		$tag->remove_map($_GET['type'],$_GET['object_id']);
	break;
	case 'browse_type':
		$browse = new Browse($_GET['browse_id']);
		$browse->set_filter('object_type', $_GET['type']);
		$browse->store();
	break;
	case 'add_filter':
		$browse = new Browse($_GET['browse_id']);
		$browse->set_filter('tag', $_GET['tag_id']);
		$browse->set_offset('6');
		$object_ids = $browse->get_objects();
		ob_start();
		$browse->show_objects($object_ids);
		$results['browse_content_tag'] = ob_get_clean();
		$browse->store();
	break;
	
	case 'random_filter':
        $browse = new Browse($_GET['browse_id']);
        $browse->set_type('tag');
		$browse->set_simple_browse(true);
        $browse->set_sort('count','ASC');

		$browse->set_simple_browse(false);
		$browse->save_objects(Tag::get_random_tags(40,array()));
		$object_ids = $browse->get_saved();
		$keys = array_keys($object_ids);
		Tag::build_cache($keys);
		
		ob_start();	
		 
		$browse2 = new Browse();
		$browse2->set_type('album_tag');
		$browse2->store();

		require_once Config::get('prefix') . '/templates/show_tagcloud.inc.php';
		$results['tag_filter'] = ob_get_clean();
		$browse->store();
	break;
	
	case 'sort_top':
        $browse = new Browse($_GET['browse_id']);
        $browse->set_type('tag');
		$browse->set_simple_browse(true);
        $browse->set_sort('count','ASC');
		
		// This one's a doozy

		$browse->set_simple_browse(false);
		$browse->save_objects(Tag::get_popular_tags(20,array()));
		$object_ids = $browse->get_saved();
		$keys = array_keys($object_ids);
		Tag::build_cache($keys);
		
		ob_start();	
		 
		$browse2 = new Browse();
		$browse2->set_type('album_tag');
		$browse2->store();

		require_once Config::get('prefix') . '/templates/show_tagcloud.inc.php';
		$results['tag_filter'] = ob_get_clean();
		$browse->store();
	break;

	case 'add_filter_alphabet':
        $browse = new Browse($_GET['browse_id']);
        $browse->set_type('tag');
		$browse->set_simple_browse(true);
        $browse->set_sort('count','ASC');
		
		// This one's a doozy
		$letter = $_REQUEST['multi_alpha_filter'];
		$browse->set_simple_browse(false);
		$browse->save_objects(Tag::get_tags_alphabet(40,array(),$letter));
		$object_ids = $browse->get_saved();
		$keys = array_keys($object_ids);
		Tag::build_cache($keys);
		
		ob_start();	
		 
		$browse2 = new Browse();
		$browse2->set_type('album_tag');
		$browse2->store();

		require_once Config::get('prefix') . '/templates/show_tagcloud.inc.php';
		$results['tag_filter'] = ob_get_clean();
		$browse->store();
	break;

	case 'moderate_tag':
		if (!Access::check('interface','100')) {debug_event('DENIED',$GLOBALS['user']->username . ' attempted to change the state of a song','1');exit;}
		$tag_to_delete = new Tag();
		$tag_to_delete->delete_new_tag();
		$tag_valid = new Tag();
		$tag_valid->validate_new_tag();
		$div = 'tag_columns';
		$results[$div] = 'La modération des tags a bien été effectuée';	
	break;

	default:
		$results['rfc3514'] = '0x1';
	break;
} // switch on action;


// We always do this
echo xml_from_array($results);
?>
