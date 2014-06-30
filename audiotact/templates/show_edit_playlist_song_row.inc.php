<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Edit Playlist Song Row
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
<td colspan="9">
<form method="post" id="edit_track_<?php echo $track['id']; ?>" action="javascript:void(0);">
<table class="inline-edit" cellpadding="3" cellspacing="0">
<tr>
<td>
	<input type="text" name="track" size="3" maxlength="4" value="<?php echo intval($track['track']); ?>" />
</td>
<td class="cel_song"><?php echo $song->f_link; ?></td>
<td class="cel_artist"><?php echo $song->f_artist_link; ?></td>
<td class="cel_album"><?php echo $song->f_album_link; ?></td>
<td class="cel_genre"><?php echo $song->f_genre_link; ?></td>
<td class="cel_track"><?php echo $song->f_track; ?></td>
<td class="cel_time"><?php echo $song->f_time; ?></td>
<td>
	<input type="hidden" name="id" value="<?php echo $song->id; ?>" />
	<input type="hidden" name="type" value="song" />
	<?php echo Ajax::button('?page=playlist&action=save_track&playlist_id=' . $playlist->id . '&track_id=' . $track['id'],'download',_('Save Changes'),'save_track_' . $track['id'],'edit_track_' . $track['id']); ?>
</td>
</tr>
</table>
</form>
</td>
