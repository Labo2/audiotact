<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Localplay Add Instance
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
<?php show_box_top(_('Add Localplay Instance')); ?>
<form method="post" action="<?php echo Config::get('web_path'); ?>/localplay.php?action=add_instance">
<table cellpadding="3" cellspacing="0" class="tabledata">
<?php foreach ($fields as $key=>$field) { ?>
<tr>
	<td><?php echo $field['description']; ?></td>
	<td><input type="text" name="<?php echo $key; ?>" /></td>
</tr>
<?php } ?>
</table>
	<div class="formValidation">
		<input type="submit" value="<?php echo _('Add Instance'); ?>" />
  </div>
</form>
<?php show_box_bottom(); ?>
