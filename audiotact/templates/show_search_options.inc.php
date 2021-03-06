<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Search Options
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
<?php show_box_top(_('Options'),'info-box'); ?>
<div id="information_actions">
<ul>
<li>
	<?php echo Ajax::button('?action=basket&type=browse_set&browse_id=' . $browse->id,'add',_('Add Search Results'),'add_search_results'); ?>
	<?php echo _('Add Search Results'); ?>
</li>
	<?php if (Access::check_function('batch_download')) { ?>
<li>
	<a href="<?php echo Config::get('web_path'); ?>/batch.php?action=browse&amp;type=<?php echo scrub_out($_REQUEST['type']); ?>&amp;browse_id=<?php echo $browse->id; ?>"><?php echo get_user_icon('batch_download', _('Batch Download')); ?></a>
	<?php echo _('Batch Download'); ?>
</li>
	<?php } ?>
</ul>
</div>
<?php show_box_bottom(); ?>
