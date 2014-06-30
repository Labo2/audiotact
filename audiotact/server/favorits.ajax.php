<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Favorits Ajax
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
	case 'flip_state_album':
		if (!Access::check('interface','100')) {
			debug_event('DENIED',$GLOBALS['user']->username . ' attempted to change the state of a song','1');
			exit;
		}

		$album = new Album($_REQUEST['album_id']);
		$new_selected = $album->selected ? '0' : '1';
		$album->update_selected($new_selected,$album->id);
		$album->selected = $new_selected;
		$album->format();

		$id = 'album_flip_state_' . $album->id;
		$button = $album->selected ? 'favorite_active_song' : 'favorite_hover';
		$results[$id] = Ajax::button('?page=favorits&action=flip_state_album&album_id=' . $album->id,$button,_(ucfirst($button)),'flip_state_' . $album->id);
		
		$id = 'album_flip_state_lightbox_' . $album->id;
		$button = $album->selected ? 'favorite_active' : 'favorite_inactive';
		$text = $album->selected ? 'DÉSACTIVER' : 'AJOUTER À LA SÉLECTION';
		
		ob_start();
		echo Ajax::text('?page=favorits&action=flip_state_album&album_id=' . $album->id,$text,'flip_album_lightbox_text_' . $album->id,'','m_sep');
		echo Ajax::button('?page=favorits&action=flip_state_album&album_id=' . $album->id,$button,_(ucfirst($button)),'flip_state_lightbox_' . $album->id);
		$results[$id] = ob_get_contents();
		ob_end_clean();
	break;
	
	case 'flip_state_artist':
		if (!Access::check('interface','100')) {
			debug_event('DENIED',$GLOBALS['user']->username . ' attempted to change the state of a song','1');
			exit;
		}
		$artist = new Artist($_REQUEST['artist_id']);
		$new_selected = $artist->selected ? '0' : '1';
		$artist->update_selected_artist($new_selected,$artist->id);
		$artist->selected = $new_selected;
		$artist->format();

		$id = 'artist_flip_state_' . $artist->id;
		$button = $artist->selected ? 'favorite_active_song' : 'favorite_hover';
		$results[$id] = Ajax::button('?page=favorits&action=flip_state_artist&artist_id=' . $artist->id,$button,_(ucfirst($button)),'flip_state_' . $artist->id);
		
		$id = 'artist_flip_state_lightbox_' . $artist->id;
		$button = $artist->selected ? 'favorite_active' : 'favorite_inactive';
		$text = $artist->selected ? 'DÉSACTIVER' : 'AJOUTER À LA SÉLECTION';

		ob_start();
		echo Ajax::text('?page=favorits&action=flip_state_artist&artist_id=' . $artist->id,$text,'flip_artist_lightbox_text_' . $artist->id,'','m_sep');
		echo Ajax::button('?page=favorits&action=flip_state_artist&artist_id=' . $artist->id,$button,_(ucfirst($button)),'flip_state_lightbox_' . $artist->id);
		$results[$id] = ob_get_contents();
		ob_end_clean();
	break;
	
	case 'flip_state_playlist':
		$playlist = new Playlist($_REQUEST['playlist_id']);
		$new_selected = $playlist->selected ? '0' : '1';
		$playlist->update_selected_playlist($new_selected,$playlist->id);
		$playlist->selected = $new_selected;
		$playlist->format();

		$id = 'playlist_flip_state_'.$playlist->id;
		$button = $playlist->selected ? 'favorite_active_song' : 'favorite_hover';
		$results[$id] = Ajax::button('?page=favorits&action=flip_state_playlist&playlist_id='. $playlist->id,$button,_(ucfirst($button)),'flip_state_'.$playlist->id);
		
		$id = 'playlist_flip_state_lightbox_'.$playlist->id;
		$button = $playlist->selected ? 'favorite_active' : 'favorite_inactive';
		$text = $playlist->selected ? 'DÉSACTIVER' : 'AJOUTER À LA SÉLECTION';
		ob_start();
		echo Ajax::text('?page=favorits&action=flip_state_playlist&playlist_id=' . $playlist->id,$text,'flip_playlist_lightbox_text' . $playlist->id,'','m_sep');
		echo Ajax::button('?page=favorits&action=flip_state_playlist&playlist_id='. $playlist->id,$button,_(ucfirst($button)),'flip_state_lightbox_'.$playlist->id);
		$results[$id] = ob_get_contents();
		ob_end_clean();
	break;

		default:
		$results['rfc3514'] = '0x1';
	break;
} // switch on action;

// We always do this
echo xml_from_array($results);
?>
