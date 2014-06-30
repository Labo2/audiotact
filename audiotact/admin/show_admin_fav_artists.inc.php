<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Artists
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

session_start();
$web_path = Config::get('web_path'); ?>

<div class="col_fav">
	<?php foreach ($object_ids as $artist_id) { 
		$artist = new Artist($artist_id, $_SESSION['catalog']); $artist->format(); ?>
		<div id="artist_<?php echo $artist->id; ?>" class="admin_fav_item">
			<div class="cel_artist cel_name">
				<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/puce_subtitles.png" />
				<a title="<?php echo $artist->name; ?>" href="<?php echo ($web_path.'/lightbox_item.php?action=show_artist&artist='.$artist->id) ;?>"><span class="upper"><?php echo $artist->name; ?></span></a> / <?php echo $artist->albums; ?> albums
			</div>
			
			<div class="cel_edit_fav">
				<?php $icon = $artist->selected ? 'favorite_active' : 'favorite_inactive'; $button_flip_state_id = 'artist_flip_state_' .$artist_id; ?>
				<span id="<?php echo($button_flip_state_id); ?>" >
					<?php echo Ajax::button('?page=favorits&action=flip_state_artist&artist_id=' . $artist->id,$icon,_(ucfirst($icon)),'flip_artist_' . $artist->id); ?>
				</span>
				<a class="open_lightbox"title="<?php echo $artist->name; ?>" href="<?php echo ($web_path.'/lightbox_item.php?action=show_artist&artist='.$artist->id) ;?>">
					<?php echo get_user_icon('edit_favorites','',('Éditer')); ?>
				</a>
			</div>
		</div>
	<?php } //end foreach ($artists as $artist) ?>
	<?php if (!count($object_ids)) { ?><div class="<?php echo flip_class(); ?>"><span class="fatalerror"><?php echo ('Aucun artiste sélectionné'); ?></span></div><?php } ?>
</div>
