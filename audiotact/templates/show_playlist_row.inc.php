<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Playlist Row
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

?>
<div class="cel_show">
	<div class="cel_image">
		<?php $ids = array();
		foreach ($object_idp as $object) {
			$song = new Song($object['object_id']); $song->format(); $album_id = $song->album; $ids[] = $album_id;
		} 
		$ids = array_unique($ids); $url = urlencode(serialize($ids)); ?> 
		<a href=""><img src="<?php echo $web_path; ?>/image.php?thumb=3&type=playlist&id=<?php echo ($url); ?>" width="140" height="140" alt="" title="<?php echo $name; ?>" /></a>
	</div>
	<div class="cel_hover_image"></div>
	<div class="cel_name">
		<a title="<?php echo $playlist->name; ?>" href="<?php echo ($web_path.'/lightbox_item.php?action=show_playlist&playlist='.$playlist->id) ;?>">
			<?php echo $playlist->f_name; ?> / <?php echo $playlist->genre; ?> 
		</a>
	</div>
</div><!-- cel_show -->

<div class="cel_hover_action">
	<ul>
		<li>
			<div class="cel_add play"><a href=""><?php echo get_user_icon('add','',_('Play')); ?></a>
				<span class="item_to_play" style="display:none"><a href=""><?php echo get_user_icon('add','',_('Play')); ?></a>
				<?php $playlist_songs = new Playlist($playlist->id); $playlist_songs->format(); $songs = $playlist_songs->get_songs();	
				echo ('myPlaylist.setPlaylist([');
				foreach ($songs as $song_id) {	$song = new Song($song_id);	$song->format();
					echo('{title: "'.$song->title.' - '.$song->f_album.'",artist: "'.$song->f_artist.'",mp3: "'.call_user_func(array(Song,'play_url'),$song->id).'",poster: "'.Config::get('web_path').'/image.php?id='.$song->album.'&thumb=3"},');
				} echo (']);');	?>
				</span>
			</div>
		</li>
		
		<li>	
			<?php if (Access::check('interface','100')) { 
				$icon = $playlist->selected ? 'favorite_active_song' : 'favorite_hover'; $button_flip_state_id = 'playlist_flip_state_' .$playlist_id; ?>
				<span id="<?php echo($button_flip_state_id); ?>"><?php echo Ajax::button('?page=favorits&action=flip_state_playlist&playlist_id=' . $playlist->id,$icon,_(ucfirst($icon)),'flip_playlist_' . $playlist->id); ?></span>
			<?php } else {
				echo Ajax::button('?action=basket&type=playlist&id=' . $playlist->id,'favorite_hover',_('Add'),'add_playlist_' . $playlist->id,'','fav_user');
			}?>
		</li>
		
		<li>
		<a class="open_lightbox" title="<?php echo $playlist->name; ?>" href="<?php echo ($web_path.'/lightbox_item.php?action=show_playlist&playlist='.$playlist->id) ;?>"><?php echo get_user_icon('info','',('Informations')); ?></a>
		</li>
		
		<li>
			<a href="<?php echo Config::get('web_path'); ?>/batch.php?action=playlist&amp;id=<?php echo $playlist->id; ?>" class="download_link">
		       <?php echo get_user_icon('batch_download',_('Batch Download')); ?>
			</a>
		</li>
	</ul>
</div><!--cel_hover_action-->

