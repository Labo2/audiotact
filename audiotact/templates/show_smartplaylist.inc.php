<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/*
 Copyright (c) Ampache.org
 All rights reserved.

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License v2
 as published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

*/
?>
<?php 
ob_start();
require Config::get('prefix') . '/templates/show_smartplaylist_title.inc.php';
$title = ob_get_contents();
ob_end_clean();
show_box_top('<div id="playlist_row_' . $playlist->id . '">' . $title .
        '</div>');
?>
<div id="information_actions">
<ul>
        <?php if (Access::check_function('batch_download')) { ?>
	<li>
		<a href="<?php echo Config::get('web_path'); ?>/batch.php?action=search&amp;id=<?php echo $playlist->id; ?>"><?php echo get_user_icon('batch_download', _('Batch Download')); ?></a>
		<?php echo _('Batch Download'); ?>
	</li>
        <?php } ?>
	<li>
		<?php echo Ajax::button('?action=basket&type=smartplaylist&id=' . $playlist->id,'add',_('Add All'),'play_playlist'); ?>
		<?php echo _('Add All'); ?>
	</li>
	<?php if ($playlist->has_access()) { ?>
	<li>
		<?php echo Ajax::button('?action=show_edit_object&type=smartplaylist_title&id=' . $playlist->id,'edit',_('Edit'),'edit_playlist_' . $playlist->id); ?>
		<?php echo _('Edit'); ?>
	</li>
	<li>
		<a href="<?php echo Config::get('web_path'); ?>/smartplaylist.php?action=delete_playlist&playlist_id=<?php echo $playlist->id; ?>">
			<?php echo get_user_icon('delete'); ?>
		</a>
		<?php echo _('Delete'); ?>
	</li>
	<?php } ?>
</ul>
</div>

<form id="editplaylist" name="editplaylist" method="post" action="<?php echo Config::get('web_path'); ?>/smartplaylist.php?action=update_playlist&playlist_id=<?php echo $playlist->id; ?>" enctype="multipart/form-data" style="Display:inline">

<?php require Config::get('prefix') . '/templates/show_rules.inc.php'; ?>

<div class="formValidation">
	<input class="button" type="submit" value="<?php echo _('Save Changes'); ?>" />
</div>

</form>

<?php show_box_bottom(); ?>
