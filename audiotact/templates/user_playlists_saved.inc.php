<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Playlist
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
 * 
 * Audiotact is an Ampache-based project developped by Oudeis (www.oudeis.fr) with the support of the Labo2 (www.bibliotheque.nimes.fr)

 */

/**
 * Playlist Box
 * This box is used for actions on the main screen and on a specific playlist page
 * It changes depending on where it is
 */
$web_path = Config::get('web_path');
$ajax_url = Config::get('ajax_url');
?>
<p class="title"><img class="list_puce" src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/comment_puce.png" />VOS PLAYLISTS DE FAVORIS</p>
<ul id="saved_playlist" >
	<li id="unsave" class=""><?php echo Ajax::text('?page=user_playlist&action=show_tmp_tracks','Favoris non classés','show_edit_playlist'); ?></li>	
	<?php 
		foreach ($user_playlists as $playlist) { ?>
		<li id="<?php echo $playlist['id'] ;?>" class="ui-widget-header">
		<?php echo Ajax::text('?page=user_playlist&action=show_saved_tracks&id='.$playlist['id'],$playlist['name'].'<br />'.$playlist['genre'],'edit_playlist_'.$playlist['id']); ?>
		</li>	
	<?php } ?>
</ul>
