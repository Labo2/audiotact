<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Edit Live Stream
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
<td colspan="6">
<form method="post" id="edit_live_stream_<?php echo $radio->id; ?>">
<table class="inline-edit" cellpadding="3" cellspacing="0">
<tr>
	<th><?php echo _('Name'); ?></th>
	<th><?php echo _('Stream URL'); ?></th>
	<th><?php echo _('Homepage'); ?></th>
	<th><?php echo _('Callsign'); ?></th>
	<th><?php echo _('Frequency'); ?></th>
	<th>&nbsp;</th>
</tr>
<tr>
<td>
	<input type="text" name="name" value="<?php echo scrub_out($radio->name); ?>" size="9" />
</td>
<td>
	<input type="text" name="url" value="<?php echo scrub_out($radio->url); ?>" size ="12" />
</td>
<td>
	<input type="text" name="site_url" value="<?php echo scrub_out($radio->site_url); ?>" size="9" />
</td>
<td>
	<input type="text" name="call_sign" value="<?php echo scrub_out($radio->call_sign); ?>" size="6" />
</td>
<td>
	<input type="text" name="frequency" value="<?php echo scrub_out($radio->frequency); ?>" size="6" />
</td>
<td>
	<input type="hidden" name="id" value="<?php echo $radio->id; ?>" />
	<input type="hidden" name="type" value="live_stream_row" />
	<?php echo Ajax::button('?action=edit_object&id=' . $radio->id . '&type=live_stream_row','download',_('Save Changes'),'save_live_stream_' . $radio->id,'edit_live_stream_' . $radio->id); ?>
</td>
</tr>
</table>
</form>
</td>
