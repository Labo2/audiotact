<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Albums
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

/* Switch on Action */
switch ($_REQUEST['action']) {
	/* ARTISTS */
	case 'show_artist':
		$artist = new Artist($_REQUEST['artist']);
		$artist->format();
		$object_ids = $artist->get_albums($_REQUEST['catalog']);
		$object_type = 'album';
		require_once Config::get('prefix') . '/templates/show_artist.inc.php';
	break;
	
	/* ALBUMS */
	case 'show_album':
		$album = new Album($_REQUEST['album']);
		$album->format();
		require Config::get('prefix') . '/templates/show_album.inc.php';

	break;
	case 'add_album_tag': 	/* Add album tag */
		$id_album = $_POST['id_album'];		
		$add_album_tag = $_POST['add_album_tag'];
		$add_album_tag = explode(";", $add_album_tag);		
		Tag::add_album_tag($id_album,$add_album_tag);
	break;
	case 'add_shout':
		$shout_id = shoutBox::create($_POST);
	break;
	
	/* PLAYLISTS */
	case 'show_playlist':
		$playlist = new Playlist($_REQUEST['playlist']);
		$playlist->format();
		$object_ids = $playlist->get_items();
		require_once Config::get('prefix') . '/templates/show_playlist.inc.php';
	break;
	
	/* USER PLAYLISTS*/
	case 'edit_tmp_playlist':
		$id = $_REQUEST['id'];
		$name = $_REQUEST['name'];
		$genre = $_REQUEST['genre'];
		$tmp_playlist = tmpPlaylist::add_info_playlist($id,$name,$genre);	
	break;
	
	case 'edit_saved_playlist':
		$id = $_REQUEST['id'];
		$name = $_REQUEST['name'];
		$genre = $_REQUEST['genre'];
				
		$update_playlist = Playlist::update_saved_playlist($id,$name,$genre);	
		echo Ajax::text('?page=user_playlist&action=show_saved_tracks&id='.$id,$name.'<br />'.$genre,'edit_playlist_'.$id);	
	break;
	
	case 'delete_user_playlist':
		$id = $_REQUEST['playlist_id'];
		Playlist::admin_delete_playlist($id);
		$url = Config::get('web_path') . '/catalog_browse_favorits.php';
		echo $url;
	break;
	
	
	/* Install index catalog */
	case 'update_catalog':
		if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {
			if ($_REQUEST['catalogs'] ) {
				foreach ($_REQUEST['catalogs'] as $catalog_id) {
					$catalog = new Catalog($catalog_id);
					$catalog->first_add_to_catalog();
				}
			}
		}
	break;
} ?>
