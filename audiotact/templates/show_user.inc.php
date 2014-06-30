<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show User
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

$last_seen      = $client->last_seen ? date("m\/d\/y - H:i",$client->last_seen) : _('Never');
$create_date    = $client->create_date ? date("m\/d\/y - H:i",$client->create_date) : _('Unknown');
$client->format();
?>
<?php show_box_top($client->fullname); ?>
<table cellspacing="0">
<tr>
	<td valign="top">
		<strong><?php echo _('Full Name'); ?>:</strong> <?php echo $client->fullname; ?><br />
		<strong><?php echo _('Create Date'); ?>:</strong> <?php echo $create_date; ?><br />
		<strong><?php echo _('Last Seen'); ?>:</strong> <?php echo $last_seen; ?><br />
		<strong><?php echo _('Activity'); ?>:</strong> <?php echo $client->f_useage; ?><br />
		<?php if ($client->is_logged_in() AND $client->is_online()) { ?>
			<i style="color:green;"><?php echo _('User is Online Now'); ?></i>
		<?php } else { ?>
			<i style="color:red;"><?php echo _('User is Offline Now'); ?></i>
		<?php } ?>

	</td>
	<td valign="top">
		<h2><?php echo _('Active Playlist'); ?></h2>
		<div style="padding-left:10px;">
		<?php
			$tmp_playlist = new tmpPlaylist(tmpPlaylist::get_from_userid($client->id));
			$object_ids = $tmp_playlist->get_items();
			foreach ($object_ids as $object_data) {
				$type = array_shift($object_data);
				$object = new $type(array_shift($object_data));
				$object->format();
		?>
		<?php echo $object->f_link; ?><br />
		<?php } ?>
		</div>
	</td>
</tr>
</table>
<?php show_box_bottom(); ?>
<?php
	$data = Song::get_recently_played($client->id);
	require Config::get('prefix') . '/templates/show_recently_played.inc.php';
?>

