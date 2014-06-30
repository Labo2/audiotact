<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Artists Music
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
$ajax_url = Config::get('ajax_url');

/*require Config::get('prefix') . '/templates/list_header.inc.php'; */
?>
<div id="items_wrapper">
<h1>DISCOGRAPHIE</h1>
<?php
foreach ($object_ids as $album_id) {
	$album = new Album($album_id);
	$album->format(); ?>
	
	
		<div class="wrapper_album">
		<div id="album_<?php echo $album->id; ?>" class="album_item">
			<div class="cel_show">
				<div class="cel_image">
					<a href="<?php echo Config::get('web_path'); ?>/albums.php?action=show&amp;album=<?php echo $album->id; ?>">
						<img height="140" width="140" alt="<?php echo($name) ?>" title="<?php echo($name) ?>" src="<?php echo Config::get('web_path'); ?>/image.php?id=<?php echo $album->id; ?>&amp;thumb=1" />
					 </a>
				</div>
				<div class="cel_hover_image album_link" title="<?php echo $album->name; ?>" rel="<?php echo ($web_path.'/lightbox_item.php?action=show_album&album='.$album->id) ;?>"></div>
				<div class="cel_add_album">
					<div class="play">
						<a href=""><?php echo get_user_icon('play_album','',_('Play')); ?>JOUER L'ALBUM</a>
						<span class="item_to_play" style="display:none">
						<?php $album_songs = new Album($album->id); $album_songs->format(); $songs = $album_songs->get_songs();	
						echo ('myPlaylist.setPlaylist([');
						foreach ($songs as $song_id) {	$song = new Song($song_id);	 $song->format();
							echo ('{title: "'.$song->title.' - '.$song->f_album.'",artist: "'.$song->f_artist.'",mp3: "'.call_user_func(array(Song,'play_url'),$song->id).'",poster: "'.Config::get('web_path').'/image.php?id='.$song->album.'&thumb=3"},');	
						} echo (']);');	?>	
						</span>
					</div>
				</div>
			</div><!-- cell_show -->
		</div><!-- album_item -->

		<div id="album_infos">
		<h2><a href="" class="album_link"  rel="<?php echo ($web_path.'/lightbox_item.php?action=show_album&album='.$album->id) ;?>" title="<?php echo $album->name; ?>"><?php echo $album->name; ?></a></h2>
		<?php if ($album->year != _('N/A')) { ?><h4><?php echo ("Année : ".$album->year);?></h4><?php } ?>
		<div id="album_tagcloud"><h3>TAGS</h3><div class="tags"><?php echo $album->f_tags; ?></div></div>
			
		</div><!-- album_infos -->
	
</div>
<?php } //end foreach ($albums as $album) ?>
	</div><!-- items_wrapper -->
<?php if (!count($object_ids)) { ?><div class="<?php echo flip_class(); ?>"><span class="fatalerror"><?php echo _('Aucun album'); ?></span></div><?php } ?>
