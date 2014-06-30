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
$web_path = Config::get('web_path'); ?>

		<ul class="fav_tools_bottom">
			<li><!-- PLAY -->
				<div class="play"><a href="">ÉCOUTER</a>
		<span class="item_to_play" style="display:none">
				<?php $objects = $GLOBALS['user']->playlist->get_items();$normal_array = array('radio','song','video','random');echo ('myPlaylist.setPlaylist([');
				foreach ($objects as $uid=>$object_data) {$type = array_shift($object_data);
				if (in_array($type,$normal_array)) {$object = new $type(array_shift($object_data));$object->format();
				echo ('{title: "'.$object->title.' - '.$object->f_album.'",artist: "'.$object->f_artist.'",mp3: "'.call_user_func(array(Song,'play_url'),$object->id).'",poster: "'.Config::get('web_path').'/image.php?id='.$object->album.'&thumb=3"},');	}}echo (']);');?>	</span></div><!-- play -->
			</li>
			<li><div class="play"><a href="" class="sep"><?php echo get_user_icon('play_playlist_b', _('Play')); ?></a>
				<span class="item_to_play" style="display:none">
				<?php $objects = $GLOBALS['user']->playlist->get_items();$normal_array = array('radio','song','video','random');echo ('myPlaylist.setPlaylist([');
				foreach ($objects as $uid=>$object_data) {$type = array_shift($object_data);
				if (in_array($type,$normal_array)) {$object = new $type(array_shift($object_data));$object->format();
				echo ('{title: "'.$object->title.' - '.$object->f_album.'",artist: "'.$object->f_artist.'",mp3: "'.call_user_func(array(Song,'play_url'),$object->id).'",poster: "'.Config::get('web_path').'/image.php?id='.$object->album.'&thumb=3"},');	}}echo (']);');?>	</span></div><!-- play -->
			</li>	
			<!-- ENREGISTRER -->
			<li><?php echo Ajax::text('?page=user_playlist&action=create','SAUVEGARDER LA PLAYLIST','rb_create_playlist_t','','save_playlist'); ?></li>
			<li><?php echo Ajax::button('?page=user_playlist&action=create','save_playlist_b',_('Save playlist'),'rb_create_playlist_bb','','save_playlist sep'); ?></li>
			<!-- TÉLÉCHARGER -->
			<li><a class="pl_dwl" href="<?php echo Config::get('web_path'); ?>/batch.php?action=tmp_playlist&amp;id=<?php echo $GLOBALS['user']->playlist->id; ?>">TÉLÉCHARGER SUR LA CLÉ USB</a></li>
			<li><a class="sep" href="<?php echo Config::get('web_path'); ?>/batch.php?action=tmp_playlist&amp;id=<?php echo $GLOBALS['user']->playlist->id; ?>"><?php echo get_user_icon('usb_download_inactive_b',_('Batch Download'),'','usb_download'); ?></a></li>	
			<!-- SUPPRIMER -->	
			<li class="remove_all"><?php echo Ajax::text('?page=user_playlist&action=clear_tmp_fav','SUPPRIMER LA PLAYLIST','rb_clear_playlist_b'); ?></li>
			<li class="remove_all"><?php echo Ajax::button('?page=user_playlist&action=clear_tmp_fav','trash_picto_b','EFFACER','rb_clear_playlist_bb'); ?></li>
		</ul>
