<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Playlist
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
 * Playlist Box
 * This box is used for actions on the main screen and on a specific playlist page
 * It changes depending on where it is
 */
$web_path = Config::get('web_path');
$ajax_url = Config::get('ajax_url');
?>

<script type="text/javascript">
jQuery.noConflict();	
jQuery(function(){
	//jQuery('#scrollbar_wrapper').mCustomScrollbar({ scrollButtons:{enable:true}, advanced:{ updateOnContentResize: true} });
	lightboxScroll = new iScroll('scrollbar_wrapper', { hScrollbar: false, vScrollbar: false});
});
</script>


<div id="scrollbar_wrapper">
	<div id="lightbox_wrapper">
	<?php show_box_top('','info-box'); ?>
		
		<div id="informations">
			<div id="large_info">			
				<div id="info_picture">
					<div class="album_art">
					<?php $object_idp = $playlist->get_items(); $ids = array();
						foreach ($object_idp as $object) {
							$song = new Song($object['object_id']); $song->format(); $album_id = $song->album; $ids[] = $album_id;
		        		} 
				        $ids = array_unique($ids);
				      	$url = urlencode(serialize($ids)); ?> 
						<img src="<?php echo $web_path; ?>/image.php?thumb=3&type=playlist&id=<?php echo ($url); ?>" width="150px" height="150px" alt="" title="<?php echo $name; ?>" />
					</div><!-- album_art -->
				</div><!-- #info_picture -->
				
				<!-- INFO PLAYLIST -->
				<div id="excerpt_info">
					<h1><?php echo $playlist->name ?></h1>
					<h2><?php echo 'Genre : '.$playlist->genre ?></h2>
					<h3><?php $date = $playlist->date;
	echo $date;?></h3>
				</div><!--# excerpt_info -->
			</div><!-- large_info -->
		</div><!--informations-->
	
		<div id="user_actions">
			<ul> 
				<li><?php echo Ajax::text('?action=basket&type=playlist&id=' . $playlist->id,('Ajouter aux favoris'),'add_text_' . $playlist->id,'','fav_user_lightbox'); ?></li>
    			<li class="sep"><?php echo Ajax::button('?action=basket&type=playlist&id=' . $playlist->id,'favorite_inactive',_('Add'),'add_playlist_' . $playlist->id,'','fav_user_lightbox_img'); ?></li>
   				<li class="sep"><div class="play">
				<a href="" class="m_sep">ÉCOUTER LA PLAYLIST</a><a href=""><?php echo get_user_icon('user_play', _('Play')); ?></a>
					<span class="item_to_play" style="display:none">
					<?php $playlist_songs = new Playlist($playlist->id); $playlist_songs->format(); $songs = $playlist_songs->get_songs();	
						echo ('myPlaylist.setPlaylist([');
						foreach ($songs as $song_id) {	$song = new Song($song_id);	$song->format();
			 				echo('{title: "'.$song->title.' - '.$song->f_album.'",artist: "'.$song->f_artist.'",mp3: "'.call_user_func(array(Song,'play_url'),$song->id).'",poster: "'.Config::get('web_path').'/image.php?id='.$song->album.'&thumb=3"},');
						} echo (']);');	?>
					</span>
				</div></li><!-- play -->
				<li><a href="<?php echo Config::get('web_path'); ?>/batch.php?action=playlist&amp;id=<?php echo $playlist->id; ?>">TÉLÉCHARGER</a></li>
				<li><a href="<?php echo Config::get('web_path'); ?>/batch.php?action=playlist&amp;id=<?php echo $playlist->id; ?>"><?php echo get_user_icon('user_usb', _('Batch Download')); ?></a></li>
			</ul>
			
			<?php if (Access::check('interface','100')) { ?>
			<ul id="admin_actions">
			<li class="sep"><?php $icon = $playlist->selected ? 'favorite_active' : 'favorite_inactive'; $text = $playlist->selected ? 'DÉSACTIVER' : 'AJOUTER À LA SÉLECTION'; $button_flip_state_id = 'playlist_flip_state_lightbox_' .$playlist->id; ?>
					<span id="<?php echo($button_flip_state_id); ?>">
					<?php echo Ajax::text('?page=favorits&action=flip_state_playlist&playlist_id=' . $playlist->id,$text,'flip_playlist_lightbox_text' . $playlist->id,'','m_sep'); ?>
					<?php echo Ajax::button('?page=favorits&action=flip_state_playlist&playlist_id=' . $playlist->id,$icon,_(ucfirst($icon)),'flip_playlist_lightbox_' . $playlist->id); ?>
					</span>
			</li>
			<li><a href="<?php echo Config::get('web_path'); ?>/admin/admin_lightbox_item.php?action=delete_playlist&playlist_id=<?php echo $playlist->id;?>" class="delete_playlist" id="<?php echo $playlist->id;?>">SUPPRIMER</a></li>
			<li><a href="<?php echo Config::get('web_path'); ?>/admin/admin_lightbox_item.php?action=delete_playlist&playlist_id=<?php echo $playlist->id;?>" class="delete_playlist" id="<?php echo $playlist->id;?>"><?php echo get_user_icon('remove_picto', ('Supprimer')); ?></a></li>					
				<!--<li><?php echo Ajax::button('?action=show_edit_object&type=playlist_title&id=' . $playlist->id,'edit',_('Edit'),'edit_playlist_' . $playlist->id); ?></li>-->
			</ul>
			<?php } ?>
		</div><!-- user_actions -->
			<?php show_box_bottom(); ?>

	<?php
	$browse = new Browse();
	$browse->set_type('playlist_song');
	$browse->add_supplemental_object('playlist', $playlist->id);
	$browse->set_static_content(true);
	$browse->show_objects($object_ids);
	$browse->store();
	?>
	


		
		</div><!--lightbox_wrapper-->
</div><!--scrollbar_wrapper-->
<div id="up_lightbox" class="nav_scroll_info" onclick="lightboxScroll.scrollTo(0, -50, 200, true);return false">&larr; prev</div>
<div id="down_lightbox" class="nav_scroll_info" onclick="lightboxScroll.scrollTo(0, 50, 200, true);return false">next &rarr;</div>
