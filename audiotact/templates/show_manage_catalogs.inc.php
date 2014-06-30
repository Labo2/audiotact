<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Manage Catalogs
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
<?php show_box_top(_('Show Catalogs')) ?>
<div id="information_actions">
<table>
<tr>
<td>
<ul>
	<li><a href="<?php echo Config::get('web_path'); ?>/admin/catalog.php?action=gather_album_art"><?php echo _('Gather All Art'); ?></a></li>
	<li><a href="<?php echo Config::get('web_path'); ?>/admin/catalog.php?action=add_to_all_catalogs"><?php echo _('Add to All'); ?></a> </li>
	<li><a href="<?php echo Config::get('web_path'); ?>/admin/catalog.php?action=update_all_catalogs"><?php echo _('Verify All'); ?></a></li>
	<li><a href="<?php echo Config::get('web_path'); ?>/admin/catalog.php?action=clean_all_catalogs"><?php echo _('Clean All'); ?></a></li>
	<li><a href="<?php echo Config::get('web_path'); ?>/admin/catalog.php?action=full_service"><?php echo _('Update All'); ?></a></li>
	<li><a href="<?php echo Config::get('web_path'); ?>/admin/catalog.php?action=clear_stats"><?php echo _('Clear Stats'); ?></a></li>
</ul>
</td>
<td>
	<form method="post" action="<?php echo Config::get('web_path'); ?>/admin/catalog.php?action=update_from">
	<?php /* HINT: /data/myNewMusic */ ?><?php printf (_('Add From %s'), '<span class="information">/data/myNewMusic</span>'); ?><br />
	<input type="text" name="add_path" value="/" /><br />
	<?php /* HINT: /data/myUpdatedMusic */ ?><?php printf (_('Update From %s'), '<span class="information">/data/myUpdatedMusic</span>'); ?><br />
	<input type="text" name="update_path" value="/" /><br />
<input type="submit" value="<?php echo _('Update'); ?>" />
</form>
</td>
</tr>
</table>
</div>
<?php show_box_bottom(); ?>
<?php
		$catalog_ids = Catalog::get_catalogs();
		$browse = new Browse();
		$browse->set_type('catalog');
		$browse->set_static_content(true);
		$browse->save_objects($catalog_ids);
		$browse->show_objects($catalog_ids);
		$browse->store();
?>

