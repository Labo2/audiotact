<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Add Tag
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

<form id="add_album_tag" method="post" enctype="multipart/form-data" action="<?php echo Config::get('web_path'); ?>/lightbox_item.php?action=add_album_tag">
	<table class="shout_tag" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<input id="tags_1" class="tag tags" type="text" name="add_album_tag" value="" placeholder="TAGS SUGGÉRÉS POUR CET ALBUM"  />
			<p class="tag_notice">Séparer les tags par un point-virgule (;)</p>
			<p class="tag_thank" style="display:none">Votre contribution sera validée sous peu. Merci</p>
		</td>
		<td>
		<?php echo Core::form_register('add_album_tag'); ?>
			<input type="hidden" name="id_album" value="<?php echo $album->id; ?>" />
			<input class="ok_submit" type="submit" value="OK" />
		</td>
	</tr>   
   </table>
</form>
