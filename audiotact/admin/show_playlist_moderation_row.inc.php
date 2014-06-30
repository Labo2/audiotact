<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Playlists
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
$web_path = Config::get('web_path');

foreach ($object_ids as $playlist_id) {
	$playlist = new Playlist($playlist_id); $playlist->format(); $object_id = $playlist->get_items(); $count = $playlist->get_song_count(); $songs = $playlist->get_songs(); ?>
	<div class="playlist_item_wrap">
		<div class="cel_edit_playlist dontend">	
			<?php $state = $playlist->type;
			if ($state == 'private') { $icon = 'empty_checkbox';} elseif ($state == 'valid') {$icon = 'accept_checkbox';} elseif ($state == 'delete') {$icon = 'refuse_checkbox';}  					$button_flip_state_id = 'button_flip_playlist_state_'.$playlist->id; ?>
				<span id="<?php echo($button_flip_state_id); ?>" >
					<?php echo Ajax::button('?page=playlist&action=flip_playlist_state&playlist_id=' . $playlist->id,$icon,ucfirst($icon),'flip_playlist_'.$playlist->id); ?>
				</span>
		</div> <!--cel_edit_playlist-->

		<div class="cel_show_playlist">
			<p class="information dontend">Le <?php echo $playlist->date; ?>, Pseudonyme a créé cette playlist :</p>
			<p class="subtitles playlist_title dontend"><img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/puce_subtitles.png" /><span style="text-transform:uppercase"><?php echo $playlist->name; ?></span> / <?php echo $playlist->genre; ?></p>
			<ul class="playlist_items">
				<?php
				foreach ($object_id as $object) {
					$song = new Song($object['object_id']);
					$song->format();
					$playlist_track = $object['track'];?>
					<li><?php echo $playlist_track.'. '.$song->f_link; ?></li>
				<?php } ?> 
			</ul>
		</div><!--cel_show_playlist-->	
	</div><!--playlist_item_wrap-->	
<?php } ?>
	
<?php if (!count($object_ids)) { ?><span class="fatalerror"><?php echo ('Aucune playlist en attente de modération'); ?></span><?php } ?>






