<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Live Stream Row
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
<td class="cel_add">
	<?php echo Ajax::button('?action=basket&type=live_stream&id=' . $radio->id,'add',_('Add'),'add_radio_' . $radio->id); ?>
</td>
<td class="cel_streamname"><?php echo $radio->f_name_link; ?></td>
<td class="cel_callsign"><?php echo $radio->f_callsign; ?></td>
<td class="cel_frequency"><?php echo $radio->f_frequency; ?></td>
<td class="cel_tag"><?php echo $radio->f_tag; ?></td>
<td class="cel_action">
	<?php if (Access::check('interface','50')) { ?>
		<?php echo Ajax::button('?action=show_edit_object&type=live_stream_row&id=' . $radio->id,'edit',_('Edit'),'edit_radio_' . $radio->id); ?>
	<?php } ?>
	<?php if (Access::check('interface','75')) { ?>
		<?php echo Ajax::button('?page=browse&action=delete_object&type=live_stream&id=' . $radio->id,'delete',_('Delete'),'delete_radio_' . $radio->id); ?>
	<?php } ?>
</td>
