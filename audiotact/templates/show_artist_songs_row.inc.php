<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Song Row
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
<td class="cel_add">
	<?php echo Ajax::button('?action=basket&type=song&id=' . $song->id,'favorite_inactive_song',_('Add'),'add_' . $song->id); ?>
</td>
	
<td class="cel_add play"><a href=""><?php echo get_user_icon('speaker_play','',_('Play')); ?></a>
	<span class="item_to_play" style="display:none"> 
		<?php echo ('myPlaylist.setPlaylist([{title: "'.$song->title.' - '.$song->f_album.'",artist: "'.$song->f_artist.'",mp3: "'.call_user_func(array(Song,'play_url'),$song->id).'",poster: "'.Config::get('web_path').'/image.php?id='.$song->album.'&thumb=3"}]);'); ?>
	</span>
</td>

<td class="cel_track"><?php echo $song->f_track; ?></td>
<td class="cel_song"><a href="<?php echo Song::play_url($song->id); ?>" title="<?php echo scrub_out($song->title); ?>"><?php echo $song->f_title; ?></a></td>
<td class="cel_artist_song"><a href="<?php echo Song::play_url($song->id); ?>" title="<?php echo scrub_out($song->title); ?>"><?php echo $song->f_artist; ?></a></td>

<td class="cel_time"><?php echo $song->f_time; ?></td>

<td class="cel_action">
	<a href="<?php echo Config::get('web_path'); ?>/stream.php?action=download&amp;song_id=<?php echo $song->id; ?>">
		<?php echo get_user_icon('usb_download_inactive',_('Download')); ?>
	</a>
	<?php /*if (Access::check('interface','100')) { echo Ajax::button('?action=show_edit_object&type=song_row&id=' . $song->id,'edit',_('Edit'),'edit_song_' . $song->id); }*/ ?>
</td>
