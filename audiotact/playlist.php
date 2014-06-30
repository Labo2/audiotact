<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Playlist
 *
 * This is the playlist document, it handles all things playlist.
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

// We special-case this so we can send a 302 if the delete succeeded
if ($_REQUEST['action'] == 'delete_playlist') {
	// Check rights
	$playlist = new Playlist($_REQUEST['playlist_id']);
	if ($playlist->has_access()) {
		$playlist->delete();
		// Go elsewhere
		header('Location: ' . Config::get('web_path') . '/browse.php?action=playlist');
	}
}

show_header();


/* Switch on the action passed in */
switch ($_REQUEST['action']) {
	case 'add_dyn_song':
		/* Check Rights */
		if (!$playlist->has_access()) {
			access_denied();
			break;
		}

		$playlist->add_dyn_song();
		$_SESSION['data']['playlist_id']        = $playlist->id;
		show_playlist($playlist);
	break;
	case 'create_playlist':
		/* Check rights */
		if (!Access::check('interface','25')) {
			access_denied();
			break;
		}

		$playlist_name	= scrub_in($_REQUEST['playlist_name']);
		$playlist_type	= scrub_in($_REQUEST['type']);

		$playlist->create($playlist_name,$playlist_type);
		$_SESSION['data']['playlist_id']        = $playlist->id;
		show_confirmation(_('Playlist Created'), sprintf(_('%1$s (%2$s) has been created'), $playlist_name,  $playlist_type),'playlist.php');
	break;
	case 'delete_playlist':
		// If we made it here, we didn't have sufficient rights.
		access_denied();
	break;
	case 'remove_song':
		/* Check em for rights */
		if (!$playlist->has_access()) {
			access_denied();
			break;
		}
		$playlist->remove_songs($_REQUEST['song']);
		show_playlist($playlist);
	break;
	case 'show_playlist':
		$playlist = new Playlist($_REQUEST['playlist_id']);
		$playlist->format();
		$object_ids = $playlist->get_items();
		require_once Config::get('prefix') . '/templates/show_playlist.inc.php';
	break;
	case 'show_import_playlist':
		require_once Config::get('prefix') . '/templates/show_import_playlist.inc.php';
	break;
	case 'import_playlist':
		/* first we rename the file to it's original name before importing.
		Otherwise the playlist name will have the $_FILES['filename']['tmp_name'] which doesn't look right... */
		$dir = dirname($_FILES['filename']['tmp_name']) . "/";
		$filename = $dir . basename($_FILES['filename']['name']);
		move_uploaded_file($_FILES['filename']['tmp_name'], $filename );

		$catalog = new Catalog();
		$result = $catalog->import_m3u($filename);

		if($result == false) {
			$url   = Config::get('web_path') . '/playlist.php?action=show_import_playlist';
			$title = _('Playlist Not Imported');
			$body  = $reason;
		} else {
			$url   = Config::get('web_path') . '/playlist.php?action=show_playlist&amp;playlist_id='.$playlist_id;
			$title = _('Playlist Imported');
			$body  = basename($_FILES['filename']['name']);
			$body .= "<br />";
			$body .= $reason;
		}
		show_confirmation($title,$body,$url);
	break;
	case 'set_track_numbers':
		/* Make sure they have permission */
		if (!$playlist->has_access()) {
			access_denied();
			break;
		}
                $song_ids = scrub_in($_REQUEST['song']);
                foreach ($song_ids as $song_id) {
                        $track = scrub_in($_REQUEST['tr_' . $song_id]);
                        $changes[] = array('song_id' => $song_id, 'track' => $track);
                }

                $playlist->update_track_numbers($changes);

                show_playlist($playlist);
        break;
	case 'prune_empty':
		/* Make sure they have permission */
		if (!$GLOBALS['user']->has_access(100)) {
			access_denied();
			break;
		}

		prune_empty_playlists();
		$url = Config::get('web_path') . '/playlist.php';
		$title = _('Empty Playlists Deleted');
		$body  = '';
		show_confirmation($title,$body,$url);
	break;
	case 'normalize_tracks':
		$playlist = new Playlist($_REQUEST['playlist_id']);

		/* Make sure they have permission */
		if (!$playlist->has_access()) {
			access_denied();
			break;
		}

		/* Normalize the tracks */
		$playlist->normalize_tracks();
		$object_ids = $playlist->get_items();
	default:
		require_once Config::get('prefix') . '/templates/show_playlist.inc.php';
	break;
} // switch on the action

show_footer();
?>
