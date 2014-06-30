<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Catalogs
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
<?php require Config::get('prefix') . '/templates/list_header.inc.php'; ?>
<table class="tabledata" cellpadding="0" cellspacing="0">
<colgroup>
  <col id="col_catalog" />
  <col id="col_path" />
  <col id="col_lastverify" />
  <col id="col_lastadd" />
  <col id="col_lastclean" />
  <col id="col_action" />
</colgroup>
<tr class="th-top">
	<th class="cel_catalog"><?php echo _('Name'); ?></th>
	<th class="cel_path"><?php echo _('Path'); ?></th>
	<th class="cel_lastverify"><?php echo _('Last Verify'); ?></th>
	<th class="cel_lastadd"><?php echo _('Last Add'); ?></th>
	<th class="cel_lastclean"><?php echo _('Last Clean'); ?></th>
	<th class="cel_action"><?php echo _('Actions'); ?></th>
</tr>
<?php
	foreach ($object_ids as $catalog_id) {
		$catalog = new Catalog($catalog_id);
		$catalog->format();
?>
<tr class="<?php echo flip_class(); ?>" id="catalog_<?php echo $catalog->id; ?>">
	<?php require Config::get('prefix') . '/templates/show_catalog_row.inc.php'; ?>
</tr>
<?php } ?>
<tr class="<?php echo flip_class(); ?>">
<td colspan="6">
<?php if (!count($object_ids)) { ?>
	<span class="fatalerror"><?php echo _('Not Enough Data'); ?></span>
<?php } ?>
</td>
</tr>
<tr class="th-bottom">
	<th class="cel_catalog"><?php echo _('Name'); ?></th>
	<th class="cel_path"><?php echo _('Path'); ?></th>
	<th class="cel_lastverify"><?php echo _('Last Verify'); ?></th>
	<th class="cel_lastadd"><?php echo _('Last Add'); ?></th>
	<th class="cel_lastclean"><?php echo _('Last Clean'); ?></th>
	<th class="cel_action"><?php echo _('Actions'); ?></th>
</tr>
</table>
<?php require Config::get('prefix') . '/templates/list_header.inc.php'; ?>
