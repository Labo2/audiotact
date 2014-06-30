<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Albums
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
	<?php foreach ($object_ids as $album_id) {
		$album = new Album($album_id);
		$album->format(); ?>
		
		<div id="album_<?php echo $album->id; ?>" class="admin_fav_item">
			<div class="cel_artist cel_name">
				<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/puce_subtitles.png" />
				<a title="<?php echo $album->name; ?>" href="<?php echo ($web_path.'/lightbox_item.php?action=show_album&album='.$album->id) ;?>"><span class="upper"><?php echo $album->f_name; ?> </span>de <?php echo $album->f_artist; ?></a><br />
				<span class="tag_it"><?php echo $album->f_tags; ?></span>
			</div>
		
			<!-- Select favorits - Admin -->
			<div class="cel_edit_fav">
				<?php $icon = $album->selected ? 'favorite_active' : 'favorite_inactive'; $button_flip_state_id = 'album_flip_state_'.$album_id; ?>
				<span id="<?php echo($button_flip_state_id); ?>">
					<?php echo Ajax::button('?page=favorits&action=flip_state_album&album_id=' . $album->id,$icon,_(ucfirst($icon)),'flip_album_'.$album->id); ?>
				</span>
				<a class="open_lightbox" title="<?php echo $album->name; ?>" href="<?php echo ($web_path.'/lightbox_item.php?action=show_album&album='.$album->id) ;?>">
				<?php echo get_user_icon('edit_favorites','',('Éditer')); ?>
				</a>
			</div>
		</div><!--admin_fav_item -->
	<?php } ?>
	
	<?php if (!count($object_ids)) { ?><div class="<?php echo flip_class(); ?>"><span class="fatalerror"><?php echo _('Aucun album sélectionné'); ?></span></div><?php } ?>
</div><!-- col_fav -->
