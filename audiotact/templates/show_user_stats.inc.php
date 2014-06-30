<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show User Stats
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
<?php show_box_top(sprintf('%s Favorites'), $working_user->fullname); ?>
<table class="tabledata">
<tr>
	<td valign="top">
	<?php
	if (count($favorite_artists)) {
		$items = $working_user->format_favorites($favorite_artists);
		$title = '<a href="' . Config::get('web_path') . '/stream.php?action=play_favorite&amp;type=artist">' .
			get_user_icon('all', _('Favorite Artists')) . '</a>&nbsp;' .  _('Favorite Artists');
		show_info_box($title,'artist',$items);
	}
	else {
		echo "<span class=\"error\">" . _('Not Enough Data') . "</span>";
	}
	?>
	</td>
	<td valign="top">
	<?php
	if (count($favorite_albums)) {
		$items = $working_user->format_favorites($favorite_albums);
                $title = '<a href="' . Config::get('web_path') . '/stream.php?action=play_favorite&amp;type=album">' .
                        get_user_icon('all', _('Favorite Albums')) . '</a>&nbsp;' .  _('Favorite Albums');
		show_info_box($title,'album',$items);
	}
	else {
		echo "<span class=\"error\">" . _('Not Enough Data') . "</span>";
	}
	?>
	</td>
	<td valign="top">
	<?php
	if (count($favorite_songs)) {
		$items = $working_user->format_favorites($favorite_songs);
                $title = '<a href="' . Config::get('web_path') . '/stream.php?action=play_favorite&amp;type=song">' .
                        get_user_icon('all', _('Favorite Songs')) . '</a>&nbsp;' .  _('Favorite Songs');
		show_info_box($title,'your_song',$items);
	}
	else {
		echo "<span class=\"error\">" . _('Not Enough Data') . "</span>";
	}
	?>
	</td>
</tr>
</table>
<?php show_box_bottom(); ?>
