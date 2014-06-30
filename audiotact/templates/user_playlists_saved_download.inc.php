<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Tagcloud
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
		<ul class="fav_tools_bottom">
			<li><!-- PLAY -->
				<div class="play"><a href="">ÉCOUTER</a>
				<span class="item_to_play" style="display:none"><?php 	echo ('myPlaylist.setPlaylist([');
				foreach ($objects as $item) {	$song = new Song($item['object_id']);$song->format();
				echo ('{title: "'.$song->title.' - '.$song->f_album.'",artist: "'.$song->f_artist.'",mp3: "'.call_user_func(array(Song,'play_url'),$song->id).'",poster: "'.Config::get('web_path').'/image.php?id='.$song->album.'&thumb=3"},');} echo (']);');	?>	</span></div><!-- play -->
			</li>
			<li><div class="play"><a href="" class="sep"><?php echo get_user_icon('play_playlist_b', _('Play')); ?></a>
				<span class="item_to_play" style="display:none"><?php 	echo ('myPlaylist.setPlaylist([');
				foreach ($objects as $item) {	$song = new Song($item['object_id']);$song->format();
				echo ('{title: "'.$song->title.' - '.$song->f_album.'",artist: "'.$song->f_artist.'",mp3: "'.call_user_func(array(Song,'play_url'),$song->id).'",poster: "'.Config::get('web_path').'/image.php?id='.$song->album.'&thumb=3"},');} echo (']);');	?>	</span></div><!-- play -->
			</li>	

			<!-- TÉLÉCHARGER -->
			<li><a class="pl_dwl" href="<?php echo Config::get('web_path'); ?>/batch.php?action=playlist&amp;id=<?php echo $playlist->id; ?>">TÉLÉCHARGER SUR LA CLÉ USB</a></li>
			<li><a class="sep" href="<?php echo Config::get('web_path'); ?>/batch.php?action=playlist&amp;id=<?php echo $playlist->id; ?>"><?php echo get_user_icon('usb_download_inactive_b',_('Batch Download'),'','usb_download'); ?></a></li>	
			<!-- SUPPRIMER -->	
			<li class="remove_all"><a href="<?php echo Config::get('web_path'); ?>/lightbox_item.php?action=delete_user_playlist&playlist_id=<?php echo $playlist->id;?>" class="delete_user_playlist" id="<?php echo $playlist->id;?>">SUPPRIMER CETTE PLAYLIST</a></li>
			<li class="remove_all"><a href="<?php echo Config::get('web_path'); ?>/lightbox_item.php?action=delete_user_playlist&playlist_id=<?php echo $playlist->id;?>" class="delete_user_playlist" id="<?php echo $playlist->id;?>"><?php echo get_user_icon('trash_picto_b', _('Supprimer cette playlist')); ?></a></li>
		</ul>	
