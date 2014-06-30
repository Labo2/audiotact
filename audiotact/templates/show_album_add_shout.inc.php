<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Add Shout
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

<form id="add_shout" method="post" enctype="multipart/form-data" action="<?php echo Config::get('web_path'); ?>/lightbox_item.php?action=add_shout">
	<table class="shout_tag" cellpadding="0" cellspacing="0">
		<tr>
			<td><input class="pseudo" type="text" name="pseudonyme" value="" placeholder="VOTRE PSEUDONYME"/></td>
		</tr>
		<tr>
			<td><textarea class="comment" rows="5" cols="70" name="comment" placeholder="VOTRE COMMENTAIRE"></textarea>
			<p class="tag_thank_comment" style="display:none">Votre contribution sera validée sous peu. Merci</p>
			</td>
			<td>
				<?php echo Core::form_register('add_shout'); ?>
				<input type="hidden" name="object_id" value="<?php echo $album->id; ?>" />
				<input type="hidden" name="object_type" value="album" />
				<input class="ok_submit" type="submit" value="OK" />
			</td>
		</tr>
	</table>
</form>

