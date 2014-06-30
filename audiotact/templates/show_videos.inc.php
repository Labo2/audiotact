<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Videos
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

$web_path = Config::get('web_path');

?>
<?php require Config::get('prefix') . '/templates/list_header.inc.php'; ?>
<table class="tabledata" cellpadding="0" cellspacing="0">
<colgroup>
  <col id="col_add" />
  <col id="col_title" />
  <col id="col_codec" />
  <col id="col_resolution" />
  <col id="col_length" />
  <col id="col_tags" />
  <col id="col-action" />
</colgroup>
<tr class="th-top">
	<th class="cel_add"><?php echo _('Add'); ?></th>
	<th class="cel_title"><?php echo Ajax::text('?page=browse&action=set_sort&type=video&sort=title',_('Title'),'sort_video_title'); ?></th>
	<th class="cel_codec"><?php echo Ajax::text('?page=browse&action=set_sort&type=video&sort=codec',_('Codec'),'sort_video_codec'); ?></th>
	<th class="cel_resolution"><?php echo Ajax::text('?page=browse&action=set_sort&type=video&sort=resolution',_('Resolution'),'sort_video_rez'); ?></th>
	<th class="cel_length"><?php echo Ajax::text('?page=browse&action=set_sort&type=video&sort=length',_('Time'),'sort_video_length'); ?></th>
	<th class="cel_tags"><?php echo _('Tags'); ?></th>
	<th class="cel_action"><?php echo _('Action'); ?></th>
</tr>
<?php
/* Foreach through every artist that has been passed to us */
foreach ($object_ids as $video_id) {
		$video = new Video($video_id);
		$video->format();
?>
<tr id="video_<?php echo $video->id; ?>" class="<?php echo flip_class(); ?>">
	<?php require Config::get('prefix') . '/templates/show_video_row.inc.php'; ?>
</tr>
<?php } //end foreach  ?>
<?php if (!count($object_ids)) { ?>
<tr class="<?php echo flip_class(); ?>">
	<td colspan="7"><span class="fatalerror"><?php echo _('Not Enough Data'); ?></span></td>
</tr>
<?php } ?>
<tr class="th-bottom">
	<th class="cel_add"><?php echo _('Add'); ?></th>
        <th class="cel_title"><?php echo _('Title'); ?></th>
	<th class="cel_codec"><?php echo _('Codec'); ?></th>
	<th class="cel_resolution"><?php echo _('Resolution'); ?></th>
	<th class="cel_length"><?php echo Ajax::text('?page=browse&action=set_sort&type=video&sort=length',_('Time'),'sort_video_length_bottom'); ?></th>
	<th class="cel_tags"><?php echo _('Tags'); ?></th>
	<th class="cel_action"><?php echo _('Action'); ?></th>
</tr>
</table>
<?php require Config::get('prefix') . '/templates/list_header.inc.php'; ?>
