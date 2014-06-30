<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Albums
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
require_once '../lib/init.php';
session_start();

if (!Access::check('interface','100')) { access_denied(); exit;}
$web_path = Config::get('web_path');
?>

<script type="text/javascript">
jQuery.noConflict();	
jQuery(function(){
	adminScroll = new iScroll('duplicates_wrap', { hScrollbar: false, vScrollbar: false});
});
</script>

<?php
$search_type = Dba::escape($_REQUEST['search_type']);
$duplicates = Catalog::get_duplicate_songs($search_type);
?>	

<div id="main_tabs_content">		
	<h3 class="dupli">Morceaux dupliqués</h3>
	<div id="duplicates_wrap">
		<div id="scroller">
			<table id="submit_songs" class="tabledata duplicates_table" cellpadding="0" cellspacing="0">
			<colgroup>
			    <col id="col_disable" />
			    <col id="col_song" />
			    <col id="col_artist" />
			    <col id="col_album" />
			    <col id="col_length" />
			    <col id="col_bitrate" />
			    <col id="col_size" />
			    <col id="col_filename" />
			</colgroup>
			<tr class="th-top">
				<th class="cel_disable"><?php echo get_user_icon('remove_picto', ('Supprimer')); ?></th>
				<th class="cel_song"><?php echo _('Song'); ?></th>
				<th class="cel_artist"><?php echo _('Artist'); ?></th>
				<th class="cel_album"><?php echo _('Album'); ?></th>
				<th class="cel_length"><?php echo _('Length'); ?></th>
				<th class="cel_size"><?php echo _('Size'); ?></th>
				<th class="cel_filename"><?php echo _('Filename'); ?></th>
			</tr>
			<?php
				foreach ($duplicates as $item) {
					// Gather the duplicates
					$songs = Catalog::get_duplicate_info($item,$search_type);
			
					foreach ($songs as $key=>$song_id) {
						$song = new Song($song_id);
						$song->format();
						$row_key = 'duplicate_' . $song_id;
						$current_class = ($key == '0') ? 'row-highlight' : 'even';
						$button = $song->enabled ? 'disable' : 'enable';
					?>
					<tr id="<?php echo $row_key; ?>" class="<?php echo $current_class; ?>">
						<td><a href="<?php echo Config::get('web_path'); ?>/admin/admin_lightbox_item.php?action=delete_duplicated_song&song_id=<?php echo $song_id;?>" class="delete_duplicated" id="<?php echo $song_id;?>"><?php echo get_user_icon('remove_picto', ('Supprimer')); ?></a>
										</td>
						<td class="cel_song"><?php echo $song->title; ?></td>
						<td class="cel_artist"><?php echo $song->f_artist; ?></td>
						<td class="cel_album"><?php echo $song->f_album; ?></td>
						<td class="cel_length"><?php echo $song->f_time; ?></td>
						<td class="cel_size"><?php echo $song->f_size; ?></td>
						<td class="cel_filename"><?php echo scrub_out($song->file); ?></td>
					</tr>
			<?php
					} // end foreach ($dinfolist as $dinfo)
				} // end foreach ($flags as $flag)
			?>
			</table>
		</div>
	</div>
	
	<div id="up" class="scroll_fav" onclick="adminScroll.scrollTo(0, -40, 200, true);return false">&larr; prev</div>
	<div id="down" class="scroll_fav" onclick="adminScroll.scrollTo(0, 40, 200, true);return false">next &rarr;</div>
</div>
