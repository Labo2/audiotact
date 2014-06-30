<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Shoutbox
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
<?php show_box_top(''); ?>
<div id="shoutbox">
  <?php
  foreach ($shouts as $shout_id) {
	$shout = new shoutBox($shout_id);
	$object = shoutBox::get_object($shout->object_type,$shout->object_id);
	$object->format();
	$client = new User($shout->user);
	$client->format(); ?>
	<div id="shout_<?php echo $shout_id; ?>" class="shout <?php echo flip_class(); ?>">
		<img class="list_puce" src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/comment_puce.png" />
		<div class="pseudo"><?php echo $shout->user; ?> a écrit :</div>
		<div class="shouttext"><?php echo scrub_out($shout->text); ?>
		<?php if (Access::check('interface','100')) { ?>
		<a class="delete_shout" id="delete_shout" href="<?php echo $web_path. '/admin/admin_lightbox_item.php?action=delete_shout&id='.$shout_id; ?>">Supprimer ce commentaire ?</a>
		<?php } ?>
		</div>
	</div>
	<?php } ?>
</div>
<?php show_box_bottom(); ?>
