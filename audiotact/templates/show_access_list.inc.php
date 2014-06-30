<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Access List
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

/*!
	@header show access list
	@discussion default display for access admin page

*/
?>
<?php show_box_top(_('Access Control')); ?>
<div id="information_actions" class="left-column">
<ul>
	<li>
		<a href="<?php echo Config::get('web_path'); ?>/admin/access.php?action=show_add_current"><?php echo get_user_icon('add_user',_('Add Current Host')); ?></a>
		<?php echo _('Add Current Host'); ?>
	</li>
	<li>
		<a href="<?php echo Config::get('web_path'); ?>/admin/access.php?action=show_add_rpc"><?php echo get_user_icon('cog',_('Add API / RPC Host')); ?></a>
		<?php echo _('Add API / RPC Host'); ?>
	</li>
	<li>
		<a href="<?php echo Config::get('web_path'); ?>/admin/access.php?action=show_add_local"><?php echo get_user_icon('home',_('Add Local Network Definition')); ?></a>
		<?php echo _('Add Local Network Definition'); ?>
	<li>
		<a href="<?php echo Config::get('web_path'); ?>/admin/access.php?action=show_add_advanced"><?php echo get_user_icon('add_key',_('Advanced Add')); ?></a>
		<?php echo _('Advanced Add'); ?>
	</li>

</ul>
</div>
<?php show_box_bottom(); ?>
<?php show_box_top(_('Access Control Entries')); ?>
<?php Ajax::start_container('browse_content'); ?>
<?php if (count($list)) { ?>
<table cellspacing="1" cellpadding="3" class="tabledata">
<tr class="table-data">
	<th><?php echo _('Name'); ?></th>
	<th><?php echo _('Start Address'); ?></th>
	<th><?php echo _('End Address'); ?></th>
	<th><?php echo _('Level'); ?></th>
	<th><?php echo _('User'); ?></th>
	<th><?php echo _('Type'); ?></th>
	<th><?php echo _('Action'); ?></th>
</tr>
<?php
	/* Start foreach List Item */
	foreach ($list as $access_id) {
		$access = new Access($access_id);
		$access->format();
?>
<tr class="<?php echo flip_class(); ?>">
	<td><?php echo scrub_out($access->name); ?></td>
	<td><?php echo $access->f_start; ?></td>
	<td><?php echo $access->f_end; ?></td>
	<td><?php echo $access->f_level; ?></td>
	<td><?php echo $access->f_user; ?></td>
	<td><?php echo $access->f_type; ?></td>
	<td>
		<a href="<?php echo Config::get('web_path'); ?>/admin/access.php?action=show_edit_record&amp;access_id=<?php echo scrub_out($access->id); ?>"><?php echo get_user_icon('edit', _('Edit')); ?></a>
		<a href="<?php echo Config::get('web_path'); ?>/admin/access.php?action=show_delete_record&amp;access_id=<?php echo scrub_out($access->id); ?>"><?php echo get_user_icon('delete', _('Delete')); ?></a>
	</td>
</tr>
	<?php  } // end foreach ?>
</table>
<?php  } // end if count ?>
<?php Ajax::end_container(); ?>
<?php show_box_bottom(); ?>
