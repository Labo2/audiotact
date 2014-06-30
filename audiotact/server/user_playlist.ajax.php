<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Playlist Ajax
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
	case 'create':
		if (!Access::check('interface','25')) {
			debug_event('DENIED','Error:' . $GLOBALS['user']->username . ' does not have user access, unable to create playlist','1');
			break;
		}

		// Pull the current active playlist items
		$objects = $GLOBALS['user']->playlist->get_items();
		
		$name = $GLOBALS['user']->playlist->playlist_name;
		$genre = $GLOBALS['user']->playlist->playlist_genre;
		$playlist_name = 'Playlist';

		// generate the new playlist
		$playlist_id = Playlist::create($name,'private',$genre);
		if (!$playlist_id) { break; }
		$playlist = new Playlist($playlist_id);

		// Itterate through and add them to our new playlist
		foreach ($objects as $object_data) {
			$type = array_shift($object_data);

			if ($type == 'song') {
				//$songs[] = array_shift($object_data);
				$songs[] = array(array_shift($object_data),array_pop($object_data));
			}
		} // object_data

		// Add our new songs
		$playlist->add_songs($songs,'ORDERED');
		
		$user_playlists = Playlist::get_playlists_from_session();
		$id = session_id();
		$tmp_playlist = tmpPlaylist::add_info_playlist($id,'','');
		
		ob_start();		
		require Config::get('prefix') . '/templates/user_playlists_saved.inc.php';
		$results['show_playlist_submit'] = ob_get_clean();
		
		ob_start();
		$GLOBALS['user']->playlist->clear_tmp();
		$results['rightbar'] = ob_get_clean();
	break;
	
	case 'clear_tmp_fav':
		$GLOBALS['user']->playlist->clear();
		$results['rightbar'] = ajax_include('user_playlist_tmp.inc.php');
	break;
	
	case 'delete_tmp_track':
		$GLOBALS['user']->playlist->delete_track($_REQUEST['id']);
		$results['rightbar'] = ajax_include('user_playlist_tmp.inc.php');
	break;
	
	case 'tmp_sortable':
		$playlist_id = $_REQUEST['playlist_id'];
		foreach( $_POST['track'] as $order => $song_id ) {
			$order = $order+1;
			$update_order = new tmpPlaylist();
			$update_order->update_order($order,$song_id,$playlist_id); 
		}
		$results['rightbar'] = ajax_include('user_playlist_tmp.inc.php');
	break;
	
	case 'drag_tmp_to_saved':
		$playlist_id = $_REQUEST['playlist_id'] ;
		$song_id = $_REQUEST['song_id'];
		$tmp_item_id = $_REQUEST['tmp_item_id'];
		$insert_song = Playlist::add_song($song_id,$playlist_id);
		ob_start();
		$GLOBALS['user']->playlist->delete_track($tmp_item_id);
		$results['rightbar'] = ob_get_clean();
	break;

	case 'show_tmp_tracks':				
		ob_start();
		require Config::get('prefix') . '/templates/user_playlist_tmp.inc.php';
		$results['rightbar'] = ob_get_clean();
		ob_start();
		require Config::get('prefix') . '/templates/user_playlist_tmp_download.php';
		$results['playlist_download'] = ob_get_clean();
	break;
	
	case 'show_saved_tracks':
		$playlist_id = $_REQUEST['id'] ;
		$playlist = new Playlist($playlist_id);
		$objects = $playlist->get_items();
		ob_start();
		require Config::get('prefix') . '/templates/user_playlists_saved_tracks.inc.php';
		$results['rightbar'] = ob_get_clean();
		ob_start();
		require Config::get('prefix') . '/templates/user_playlists_saved_download.inc.php';
		$results['playlist_download'] = ob_get_clean();
	break;
	
	case 'delete_track_saved':
		$playlist = new Playlist($_REQUEST['playlist_id']);
		$playlist->format();
		$playlist->delete_track_saved($_REQUEST['song_id']);
		$objects = $playlist->get_items();
		
		ob_start();
		require Config::get('prefix') . '/templates/user_playlists_saved_tracks.inc.php';
		$results['rightbar'] = ob_get_clean();
		ob_start();
		
		require Config::get('prefix') . '/templates/user_playlists_saved_download.inc.php';
		$results['playlist_download'] = ob_get_clean();
	break;
	case 'saved_sortable':
		$playlist_id = $_REQUEST['playlist_id'];
		foreach( $_POST['track'] as $order => $song_id ){
			$order = $order+1;
			$update_order = new Playlist();
			$update_order->update_order($order,$song_id,$playlist_id); 
		}
		ob_start();
		require Config::get('prefix') . '/templates/user_playlists_saved_tracks.inc.php';
		$results['rightbar'] = ob_get_clean();
		ob_start();
		
		require Config::get('prefix') . '/templates/user_playlists_saved_download.inc.php';
		$results['playlist_download'] = ob_get_clean();
	break;
	case 'drag_append_playlist':
		$playlist_id = $_REQUEST['playlist_id'] ;
		$song_id = $_REQUEST['song_id'];
		$old_playlist_id = $_REQUEST['old_playlist_id'];
		$insert_song = Playlist::add_song($song_id,$playlist_id);
		$delete_song = Playlist::delete_song($song_id,$old_playlist_id);
		ob_start();
		$results['rightbar'] = ob_get_clean();
	break;

	default:
		$results['rfc3514'] = '0x1';
	break;
} // switch on action;

// We always do this
echo xml_from_array($results);
?>
