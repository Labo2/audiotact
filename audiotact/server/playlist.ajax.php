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
	case 'delete_track':
		// Create the object and remove the track
		$playlist = new Playlist($_REQUEST['playlist_id']);
		$playlist->format();
		if ($playlist->has_access()) {
			$playlist->delete_track($_REQUEST['track_id']);
		}
		$object_ids = $playlist->get_items();
		ob_start();
		$browse = new Browse();
	        $browse->set_type('playlist_song');
	        $browse->add_supplemental_object('playlist',$playlist->id);
	        $browse->save_objects($object_ids);
	        $browse->show_objects($object_ids);
		$browse->store();
		$results['browse_content'] = ob_get_clean();
	break;

	case 'edit_track':
		$playlist = new Playlist($_REQUEST['playlist_id']);
		if (!$playlist->has_access()) {
			$results['rfc3514'] = '0x1';
			break;
		}
		// They've got access, show the edit page
		$track = $playlist->get_track($_REQUEST['track_id']);
		$song = new Song($track['object_id']);
		$song->format();
		require_once Config::get('prefix') . '/templates/show_edit_playlist_song_row.inc.php';
		$results['track_' . $track['id']] = ob_get_clean();
	break;
	
	case 'save_track':
		$playlist = new Playlist($_REQUEST['playlist_id']);
		if (!$playlist->has_access()) {
			$results['rfc3514'] = '0x1';
			break;
		}
		$playlist->format();
		// They've got access, save this guy and re-display row
		$playlist->update_track_number($_GET['track_id'],$_POST['track']);
		$track = $playlist->get_track($_GET['track_id']);
		$song = new Song($track['object_id']);
		$song->format();
		$playlist_track = $track['track'];
		require Config::get('prefix') . '/templates/show_playlist_song_row.inc.php';
		$results['track_' . $track['id']] = ob_get_clean();
	break;

	case 'append':
		// Pull the current active playlist items
		$objects = $GLOBALS['user']->playlist->get_items();
		// Create the playlist object
		$playlist = new Playlist($_REQUEST['playlist_id']);
		// We need to make sure that they have access
		if (!$playlist->has_access()) {
			break;
		}

		$songs = array();

		// Itterate through and add them to our new playlist
		foreach ($objects as $element) {
			$type = array_shift($element);
			switch ($type) {
				case 'song':
					$songs[] = array_shift($element);
				break;
			} // end switch
		} // foreach

		// Override normal include procedure
		Ajax::set_include_override(true);

		// Add our new songs
		$playlist->add_songs($songs,'ORDERED');
		$playlist->format();
		$object_ids = $playlist->get_items();
		ob_start();
		require_once Config::get('prefix') . '/templates/show_playlist.inc.php';
		$results['content'] = ob_get_contents();
		ob_end_clean();
	break;
	
	case 'add_filter':
		$browse = new Browse($_GET['browse_id']);
		$browse->set_filter('playlist_type','public');
		$browse->set_filter('genre', $_GET['genre']);
		$object_ids = $browse->get_objects();
		ob_start();
		$browse->show_objects($object_ids);
		$results['browse_content_playlist'] = ob_get_clean();
		$browse->store();
		// Retrieve current objects of type based on combined filters
	break;
	
	case 'add_filter_alphabet':
		$browse = new Browse($_GET['browse_id']);
		$browse->set_filter('playlist_type','public');
		
		if ($_REQUEST['key'] && (isset($_REQUEST['multi_alpha_filter']) OR isset($_REQUEST['value']))) {
			$browse->set_filter($_REQUEST['key'],$_REQUEST['multi_alpha_filter']);
			$browse->set_catalog($_SESSION['catalog']);
		}

		if ($_REQUEST['sort']) {
			$browse->set_sort($_REQUEST['sort']);
		}
		if ($_REQUEST['catalog_key'] || $SESSION['catalog'] != 0) {
			$browse->set_filter('catalog',$_REQUEST['catalog_key']);
			$_SESSION['catalog'] = $_REQUEST['catalog_key'];
		}
        $object_ids = $browse->get_objects();
		ob_start();		
        $browse->show_objects();       
        $results['browse_content_playlist'] = ob_get_clean();
        $browse->store();

	break;
	
	case 'playlist_sort_new':
     	$browse = new Browse($_GET['browse_id']);
		
        
		$browse->save_objects(Playlist::get_new(array()));
		$browse->set_filter('playlist_type','public');
        $object_ids = $browse->get_saved();
		ob_start();		
        $browse->show_objects();       
        $results['browse_content_playlist'] = ob_get_clean();
        $browse->store();
	break;

	case 'flip_playlist_state':
		if (!Access::check('interface','100')) {debug_event('DENIED',$GLOBALS['user']->username . ' attempted to change the state of a song','1');exit;}
		$playlist = new Playlist($_REQUEST['playlist_id']);		
		$state = $playlist->type;
		
		if ($state == 'private') {$new_state = 'valid';} 
		elseif ($state == 'valid') {$new_state = 'delete';} 
		elseif ($state == 'delete') {$new_state = 'private';}

		$playlist->update_playlist_state($new_state,$playlist->id);
		$playlist->type = $new_state;
		$playlist->format();

		$id = 'button_flip_playlist_state_'.$playlist->id; 
		if ($new_state == 'private') {$icon = 'empty_checkbox';} 
		elseif ($new_state == 'valid') {$icon = 'accept_checkbox';} 
		elseif ($new_state == 'delete') {$icon = 'refuse_checkbox';}
		
		$results[$id] = Ajax::button('?page=playlist&action=flip_playlist_state&playlist_id=' . $playlist->id,$icon,ucfirst($icon),'flip_playlist_'.$playlist->id); 
	break;
	
	case 'moderate_playlist':
		$playlist_to_delete = new Playlist();
		$playlist_to_delete->delete_playlist();
		$playlist_valid = new Playlist();
		$playlist_valid->validate_playlist();
		
		ob_start();?>
			<script type="text/javascript">
			jQuery.noConflict();	
			jQuery(function(){
				jQuery('.playlist_columns').columnize({ columns: 4 });
				//jQuery('#playlist_mod_wrap').mCustomScrollbar({scrollButtons:{enable:true},advanced:{ updateOnContentResize: true}});
				playlistScroll = new iScroll('playlist_mod_wrap', { hScrollbar: false, vScrollbar: false});

			}); 
			</script>
			<div id="scroller">
			<p class="subtitles"><img src="<?php echo Config::get('web_path'); ?><?php echo Config::get('theme_path'); ?>/images/icons/puce_subtitles.png" />Validation des dernières playlists ajoutées</p>
			<div class="playlist_columns">
				<div>
				<?php
				$browse = new Browse();
				$browse->set_simple_browse(true);
				$browse->set_type('playlist');
				$browse->set_sort('type','ASC');
				$browse->set_filter('playlist_type','private');
				$browse->store();
				$browse->show_particular_objects(); 
				?>
				</div>
			</div>
			</div>
		<?php $results['playlist_mod_wrap'] = ob_get_contents();
		ob_end_clean();
	break;
	
	default:
		$results['rfc3514'] = '0x1';
	break;
} // switch on action;

// We always do this
echo xml_from_array($results);
?>
