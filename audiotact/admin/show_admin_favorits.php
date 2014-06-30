<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Albums
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
 * Audiotact is an Ampache-based project developped by Oudeis (www.oudeis.fr) with the support of Labo2 (www.bibliotheque.nimes.fr)
 */
require_once '../lib/init.php';
session_start();

if (!Access::check('interface','100')) { access_denied(); exit;}
$web_path = Config::get('web_path');
?>

<script type="text/javascript">
jQuery.noConflict();	
jQuery(function(){
	adminScroll = new iScroll('favorits_wrap', { hScrollbar: false, vScrollbar: false});
});
</script>

<?php
$selected_object = Favorits::get_selected_object();
foreach ($selected_object as $key) { $selected_object_type = $key; }
?>	

<div id="main_tabs_content">		
	<form id="select_favorit_type" name="select_object" class="autosubmit" action="<?php echo Config::get('web_path'); ?>/admin/update_admin.php?action=select_favorit_type" method="POST">
	<?php $object_type = array('playlist','artist','album'); $selected_object_type = array ($selected_object_type);
	foreach ($object_type as $select) {
		echo '<div class="select_type">';
			echo '<input id="'.$select.'" class="'.$select.' regular-radio select_type_fav" type="radio" name="show_selected" value="'.$select.'"' ;
			if (in_array($select, $selected_object_type)) { echo ' checked="" ';}
			echo '><label for="'.$select.'">';
			if ($select=="playlist") {echo ('Playlists');} elseif ($select=="artist") {echo ('Artistes');} elseif ($select=="album") {echo ('Albums');}
			echo '</label>';
		echo ('</div>');
	} ?>
					
		<div class="formValidation">
			<?php echo Core::form_register('update_selection'); ?>
			<input type="hidden" name="tab" value="<?php echo scrub_out($_REQUEST['tab']); ?>" />
			<input type="hidden" name="method" value="<?php echo scrub_out($_REQUEST['action']); ?>" />
			<?php if (Access::check('interface','100')) { ?>
				<input type="hidden" name="user_id" value="<?php echo scrub_out($_REQUEST['user_id']); ?>" />
			<?php } ?>
		</div>
	</form>

	<div id="favorits_wrap">
		<div id="scroller">
		<?php
		$selected_playlist = Favorits::get_selected_playlist();
		$browse_playlist = new Browse();
		$browse_playlist->set_simple_browse(false);
		$browse_playlist->set_type('playlist');
		$browse_playlist->save_objects($selected_playlist);
		$browse_playlist->show_admin_objects($selected_playlist);
		$browse_playlist->store();
		
		$selected_artist = Favorits::get_selected_artist();
		$browse_album = new Browse();
		$browse_album->set_type('artist');
		$browse_album->save_objects($selected_artist);
		$browse_album-> show_admin_objects($selected_artist);
		$browse_album->store();
				
		$selected_album = Favorits::get_selected_album();
		$browse_album = new Browse();
		$browse_album->set_type('album');
		$browse_album->save_objects($selected_album);
		$browse_album-> show_admin_objects($selected_album);
		$browse_album->store(); ?>
		</div>
	</div>
	
	<div id="up" class="scroll_fav" onclick="adminScroll.scrollTo(0, -40, 200, true);return false">&larr; prev</div>
	<div id="down" class="scroll_fav" onclick="adminScroll.scrollTo(0, 40, 200, true);return false">next &rarr;</div>
</div>
