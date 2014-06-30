<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Flag Row
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
<tr id="flagged_<?php echo $flag->id; ?>" class="<?php echo flip_class(); ?>">
	<td class="cel_object"><?php echo $flag->f_name; ?></td>
	<td class="cel_username"><?php echo $flag->f_user; ?></td>
	<td class="cel_flag"><?php $flag->print_flag(); ?></td>
	<td class="cel_comment"><?php echo scrub_out($flag->comment); ?></td>
	<td class="cel_status"><?php $flag->print_status(); ?></td>
	<td class="cel_action">
	<?php if ($flag->approved) { ?>
		<?php echo Ajax::button('?page=flag&action=reject&flag_id=' . $flag->id,'disable',_('Reject'),'reject_flag_' . $flag->id); ?>
	<?php } else { ?>
		<?php echo Ajax::button('?page=flag&action=accept&flag_id=' . $flag->id,'enable',_('Enable'),'enable_flag_' . $flag->id); ?>
	<?php } ?>
	</td>
</tr>
