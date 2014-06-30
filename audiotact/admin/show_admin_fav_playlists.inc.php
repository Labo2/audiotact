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
$ajax_url = Config::get('ajax_url');?>

<div class="col_fav">
	<?php foreach ($object_ids as $playlist_id) {
		$playlist = new Playlist($playlist_id); $playlist->format(); $count = $playlist->get_song_count(); ?>
		<div id="playlist_<?php echo $playlist->id; ?>" class="admin_fav_item">
			<div class="cel_playlist cel_artist cel_name">
				<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/puce_subtitles.png" />
				<a title="<?php echo $playlist->name; ?>" href="<?php echo ($web_path.'/lightbox_item.php?action=show_playlist&playlist='.$playlist->id) ;?>">
				<span class="upper"><?php echo $playlist->f_name; ?></span> par <?php echo scrub_out($playlist->f_user); ?>
				</a><br />
				<span class="tag_it">Genre : <?php echo $playlist->genre; ?> / <?php echo $count; ?> morceaux</span>
			</div>
	
			<div class="cel_edit_fav">
				<?php $icon = $playlist->selected ? 'favorite_active' : 'favorite_inactive'; $button_flip_state_id = 'playlist_flip_state_' .$playlist_id; ?>
				<span id="<?php echo($button_flip_state_id); ?>">
					<?php echo Ajax::button('?page=favorits&action=flip_state_playlist&playlist_id=' . $playlist->id,$icon,_(ucfirst($icon)),'flip_playlist_' . $playlist->id); ?>
				</span>
				<a class="open_lightbox" title="<?php echo $playlist->name; ?>" href="<?php echo ($web_path.'/lightbox_item.php?action=show_playlist&playlist_id='.$playlist->id) ;?>">
					<?php echo get_user_icon('edit_favorites','',('Éditer')); ?>
				</a>
			</div>
		</div>
	<?php } ?>
	<?php if (!count($object_ids)) { ?><div class="<?php echo flip_class(); ?>"><span class="fatalerror"><?php echo _('Aucune playlist sélectionnée'); ?></span></div><?php } ?>
</div>
