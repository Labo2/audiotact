<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Album Art
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

// Gotta do some math here!
$total_images = count($images);
$rows = floor($total_images/4);
$i = 0;
show_box_top(); ?>


<h1>Résultat de la recherche de jaquettes</h1>
<?php
if (count($images)) {
	while ($i <= $rows) {
		$j=0;
		while ($j < 4) {
			$key = $i*4+$j;
			$image_url = Config::get('web_path') . '/image.php?type=session&amp;image_index=' . $key;
			$dimensions = Core::image_dimensions(Art::get_from_source($_SESSION['form']['images'][$key]));
			if (!isset($images[$key])) { echo "<td>&nbsp;</td>\n"; }
			else { 
				if (is_array($dimensions)) { ?>
				<div class="cover_box">
					<img src="<?php echo $image_url; ?>" alt="<?php echo _('Album Art'); ?>" border="0" height="150" width="150" />
					<span class="cover_dim"><?php echo intval($dimensions['width']); ?>x<?php echo intval($dimensions['height']); ?></span>
					<a class="select_art" href="<?php echo Config::get('web_path'); ?>/admin/admin_lightbox_item.php?action=select_art&amp;image=<?php echo $key; ?>&amp;album_id=<?php echo intval($_REQUEST['album_id']); ?>"><?php echo get_user_icon('select_img', _('Sélectionner')); ?></a>
				</div>
				<?php } 
			} 
			$j++;
		} 
		$i++;
	} 
} else { ?><span class="not_found">Aucune jaquette d'album n'a été trouvée</span> <?php } ?>

<?php show_box_bottom(); ?>


<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function() {		
	var selecting = "<?php echo $web_path.Config::get('theme_path').'/images/icons/admin_browse_button.png'; ?>";
	jQuery("input.file_cat").filestyle({ image: selecting,imageheight : 27,imagewidth : 54, width : 300});
});
</script>

<?php show_box_top(); ?>
<h1>Uploader une image locale</h1>

<form class="album_img_upload" enctype="multipart/form-data" name="coverart" method="post" action="<?php echo Config::get('web_path'); ?>/admin/admin_lightbox_item.php?action=upload_album_art" style="Display:inline;">
	<div class="left">
		<input type="file" name="file" class="file_cat"/>
	</div>
<div class="formValidation">
	<input type="hidden" name="album_id" value="<?php echo $album->id; ?>" />
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo Config::get('max_upload_size'); ?>" />
</div>
</form>
<?php show_box_bottom(); ?>
