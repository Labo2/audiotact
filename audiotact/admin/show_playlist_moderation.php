<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Admin Shout
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
$web_path = Config::get('web_path');
if (!Access::check('interface','100')) { access_denied(); exit;}?>

<script type="text/javascript">
jQuery.noConflict();	
jQuery(function(){
	jQuery('.playlist_columns').columnize({ columns: 4 });
	//jQuery('#playlist_mod_wrap').mCustomScrollbar({scrollButtons:{enable:true},advanced:{ updateOnContentResize: true}});
	playlistScroll = new iScroll('playlist_mod_wrap', { hScrollbar: false, vScrollbar: false});
}); 
</script>

<div id="main_tabs_content">
	<div id="playlist_mod_wrap">
	<div id="scroller">
		<p class="subtitles"><img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/puce_subtitles.png" />Validation des dernières playlists ajoutées</p>
		<div class="playlist_columns">
			<div>
			<?php
			$browse = new Browse();
			$browse->set_simple_browse(true);
			$browse->set_type('playlist');
			$browse->set_sort('type','ASC');
			$browse->set_filter('playlist_type','private');
			$browse->store();
			$browse->show_particular_objects(); // show_playlist_moderation.php
			?>
			</div>
		</div><!--playlist_columns-->
		</div>
	</div><!--#playlist_mod_wrap-->
	<?php echo Ajax::text('?page=playlist&action=moderate_playlist','VALIDER CES PLAYLISTS ?','moderate_playlist','','validate_mod'); ?>
	<div id="up" class="nav_scroll_info" onclick="playlistScroll.scrollTo(0, -40, 200, true);return false">&larr; prev</div>
	<div id="down" class="nav_scroll_info" onclick="playlistScroll.scrollTo(0, 40, 200, true);return false">next &rarr;</div>
</div>
