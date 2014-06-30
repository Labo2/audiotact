<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show User Row
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
	<td class="cel_username">
		<a href="<?php echo $web_path; ?>/stats.php?action=show_user&amp;user_id=<?php echo $client->id; ?>">
			<?php echo $client->fullname; ?> (<?php echo $client->username; ?>)
		</a>
	</td>
  <td class="cel_lastseen"><?php echo $last_seen; ?></td>
  <td class="cel_registrationdate"><?php echo $create_date; ?></td>
  <td class="cel_activity"><?php echo $client->f_useage; ?></td>
	<?php if (Config::get('track_user_ip')) { ?>
		<td class="cel_lastip">
		<a href="<?php echo $web_path; ?>/admin/users.php?action=show_ip_history&amp;user_id=<?php echo $client->id; ?>">
			<?php echo $client->ip_history; ?>
		</a>
		</td>
	<?php } ?>
	<td class="cel_action">
		<a href="<?php echo $web_path; ?>/admin/users.php?action=show_edit&amp;user_id=<?php echo $client->id; ?>"><?php echo get_user_icon('edit', _('Edit')); ?></a>
		<a href="<?php echo $web_path; ?>/admin/users.php?action=show_preferences&amp;user_id=<?php echo $client->id; ?>"><?php echo get_user_icon('preferences', _('Preferences')); ?></a>
	<?php
	//FIXME: Fix this for the extra permission levels
	if ($client->disabled == '1') {
		echo "<a href=\"".$web_path."/admin/users.php?action=enable&amp;user_id=$client->id\">" . get_user_icon('enable', _('Enable')) . "</a>";
	}
	else {
		echo "<a href=\"".$web_path."/admin/users.php?action=disable&amp;user_id=$client->id\">" . get_user_icon('disable', _('Disable')) ."</a>";
	}
	?>
		<a href="<?php echo $web_path; ?>/admin/users.php?action=delete&amp;user_id=<?php echo $client->id; ?>"><?php echo get_user_icon('delete', _('Delete')); ?></a>
	</td>
       <?php
	if (($client->is_logged_in()) AND ($client->is_online())) {
		echo "<td class=\"cel_online user_online\"> &nbsp; </td>";
	} elseif ($client->disabled == 1) {
		echo "<td class=\"cel_online user_disabled\"> &nbsp; </td>";
	} else {
		echo "<td class=\"cel_online user_offline\"> &nbsp; </td>";
	}
?>
