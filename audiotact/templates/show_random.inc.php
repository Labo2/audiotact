<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Random
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
<?php show_box_top(_('Play Random Selection')); ?>
<form id="random" method="post" enctype="multipart/form-data" action="<?php echo Config::get('web_path'); ?>/random.php?action=get_advanced&type=<?php echo $_REQUEST['type'] ? scrub_out($_REQUEST['type']) : 'song'; ?>">
<table class="tabledata" cellpadding="3" cellspacing="0">
<tr>
	<td><?php if ($_REQUEST['type'] != 'song') { ?><a href="<?php echo Config::get('web_path'); ?>/random.php?action=advanced&type=song"><?php echo _('Songs'); ?></a><?php } else { echo _('Songs'); } ?></td>
	<td><?php if ($_REQUEST['type'] != 'album') { ?><a href="<?php echo Config::get('web_path'); ?>/random.php?action=advanced&type=album"><?php echo _('Albums'); ?></a><?php } else { echo _('Albums'); } ?></td>
	<td><?php if ($_REQUEST['type'] != 'artist') { ?><a href="<?php echo Config::get('web_path'); ?>/random.php?action=advanced&type=artist"><?php echo _('Artists'); ?></a><?php } else { echo _('Artists'); } ?></td>
</tr>
<tr><td>&nbsp;</td></tr>
</table>
<table class="tabledata" cellpadding="0" cellspacing="0">
<tr>
        <td><?php echo _('Item count'); ?></td>
        <td>
                <?php $name = 'random_' . scrub_in($_POST['random']); ${$name} = ' selected="selected"'; ?>
                <select name="random">
                <option value="1"<?php echo $random_1; ?>>1</option>
                <option value="5"<?php echo $random_5; ?>>5</option>
                <option value="10"<?php echo $random_10; ?>>10</option>
                <option value="20"<?php echo $random_20; ?>>20</option>
                <option value="30"<?php echo $random_30; ?>>30</option>
                <option value="50"<?php echo $random_50; ?>>50</option>
                <option value="100"<?php echo $random_100; ?>>100</option>
                <option value="500"<?php echo $random_500; ?>>500</option>
                <option value="1000"<?php echo $random_1000; ?>>1000</option>
                <option value="-1" ><?php echo _('All'); ?></option>
                </select>
        </td>
</tr>
<tr>
        <td><?php echo _('Length'); ?></td>
        <td>
                <?php $name = 'length_' . intval($_POST['length']); ${$name} = ' selected="selected"'; ?>
                <select name="length">
                        <option value="0"<?php echo $length_0; ?>><?php echo _('Unlimited'); ?></option>
                        <option value="15"<?php echo $length_15; ?>><?php printf(ngettext('%d minute','%d minutes',15), "15"); ?></option>
                        <option value="30"<?php echo $length_30; ?>><?php printf(ngettext('%d minute','%d minutes',30), "30"); ?></option>
                        <option value="60"<?php echo $length_60; ?>><?php printf(ngettext('%d hour','%d hours',1), "1"); ?></option>
                        <option value="120"<?php echo $length_120; ?>><?php printf(ngettext('%d hour','%d hours',2), "2"); ?></option>
                        <option value="240"<?php echo $length_240; ?>><?php printf(ngettext('%d hour','%d hours',4), "4"); ?></option>
                        <option value="480"<?php echo $length_480; ?>><?php printf(ngettext('%d hour','%d hours',8), "8"); ?></option>
                        <option value="960"<?php echo $length_960; ?>><?php printf(ngettext('%d hour','%d hours',16), "16"); ?></option>
                </select>
        </td>
</tr>
<tr>
        <td><?php echo _('Size Limit'); ?></td>
        <td>
                <?php $name = 'size_' . intval($_POST['size_limit']); ${$name} = ' selected="selected"'; ?>
                <select name="size_limit">
                        <option value="0"<?php echo $size_0; ?>><?php echo _('Unlimited'); ?></option>
                        <option value="64"<?php echo $size_64; ?>>64MB</option>
                        <option value="128"<?php echo $size_128; ?>>128MB</option>
                        <option value="256"<?php echo $size_256; ?>>256MB</option>
                        <option value="512"<?php echo $size_512; ?>>512MB</option>
                        <option value="1024"<?php echo $size_1024; ?>>1024MB</option>
                </select>
        </td>
</tr>
</table>

<?php require Config::get('prefix') . '/templates/show_rules.inc.php'; ?>

<div class="formValidation">
        <input type="submit" value="<?php echo _('Enqueue'); ?>" />
</div>
</form>
<?php show_box_bottom(); ?>
<div id="browse">
<?php
	if (is_array($object_ids)) {
		$browse = new Browse();
		$browse->set_type('song');
		$browse->save_objects($object_ids);
		$browse->show_objects();
		$browse->store();
		echo Ajax::observe('window','load',Ajax::action('?action=refresh_rightbar','playlist_refresh_load'));
	}
?>
</div>
