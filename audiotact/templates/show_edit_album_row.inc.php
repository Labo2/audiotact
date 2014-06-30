<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Edit Album Row
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
<td colspan="7">
<form method="post" id="edit_album_<?php echo $album->id; ?>" action="#">
<table class="inline-edit" cellpadding="3" cellspacing="0">
<tr>
<td>
	<input type="text" name="name" value="<?php echo scrub_out($album->full_name); ?>" />
</td>
<td>
	<?php
	if ($album->artist_count == '1') {
		show_artist_select('artist',$album->artist_id);
	}
	else {
		echo _('Various');
	}
	?>
</td>
<td>
	<input type="text" name="year" value="<?php echo scrub_out($album->year); ?>" />
</td>
<td>
	<input type="text" name="disk" value="<?php echo scrub_out($album->disk); ?>" />
</td>
<td>
	<input type="hidden" name="id" value="<?php echo $album->id; ?>" />
	<input type="hidden" name="type" value="album_row" />
	<?php echo Ajax::button('?action=edit_object&id=' . $album->id . '&type=album_row','download',_('Save Changes'),'save_album_' . $album->id,'edit_album_' . $album->id); ?>
</td>
</tr>
</table>
</form>
</td>
