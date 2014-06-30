<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Album
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
?>
<script type="text/javascript">
jQuery.noConflict();	
jQuery(function(){
	lightboxScroll = new iScroll('scrollbar_wrapper', { hScrollbar: false, vScrollbar: false, onBeforeScrollStart: function (e) { var target = e.target;
			while (target.nodeType != 1) target = target.parentNode;
			if (target.tagName != 'SELECT' && target.tagName != 'INPUT' && target.tagName != 'TEXTAREA')
				e.preventDefault();
		}
	});

	jQuery('input.tag,input.pseudo, textarea.comment').keyboard({
		layout       : 'french-azerty-2',
  		customLayout : { default: ['{cancel}'] },
  		usePreview : true,
  		position : {
 			of : jQuery('#lightbox_catalog'),
  			my : 'center center',
 			at : 'center center',
 			at2: 'center top' 
		}
  	});
});
</script>


<div id="scrollbar_wrapper">
	<div id="lightbox_wrapper">
	<?php show_box_top('','info-box'); ?>
		<div id="informations">
			
			<div id="large_info">				
				<div id="info_picture">
					<div class="album_art">
						<?php $name = '[' . $album->f_artist . '] ' . scrub_out($album->full_name); ?>
							<img height="150" width="150" alt="<?php echo($name) ?>" title="<?php echo($name) ?>" src="<?php echo Config::get('web_path'); ?>/image.php?id=<?php echo $album->id; ?>&amp;thumb=1" />
					</div><!-- album_art -->
					<?php if (Access::check('interface','100')) { ?>	
					<div class="admin_picture">JAQUETTE									
						<a id="<?php echo $album->id; ?>" class="album_img_add" href="<?php echo Config::get('web_path'); ?>/admin/admin_lightbox_item.php?action=find_art&album_id=<?php echo $album->id; ?>"><img src="<?php echo $web_path.Config::get('theme_path').'/images/icons/icon_add_a_picture.png'; ?>" title="add_picture" alt="add_picture" /></a>
						<a id="<?php echo $album->id; ?>" class="album_img_delete" href="<?php echo Config::get('web_path'); ?>/admin/admin_lightbox_item.php?action=clear_art&album_id=<?php echo $album->id; ?>"><img src="<?php echo $web_path.Config::get('theme_path').'/images/icons/icon_delete_a_picture.png'; ?>" title="delete_picture" alt="delete_picture" /></a>
					</div><!-- admin_picture -->
					<?php } ?>
				</div><!-- #info_picture -->
			
				<div id="excerpt_info">
					<h1><?php echo $album->name; ?></h1>
					<?php if (Access::check('interface','100')) { ?>
						<a class="update_album_btn" id="update_album_btn" href="<?php echo $web_path. '/templates/show_update_name.inc.php?type=album&id='.$album->id; ?>">
							<img class="update" title="Éditer" alt="Éditer" src="<?php echo ($web_path . Config::get('theme_path') . '/images/icons/icon_edit_favorites.png');?>">
						</a>
					<?php } ?>
					<h2><a class="artist_link"title="<?php echo $album->f_artist_name; ?>" href="<?php echo ($web_path.'/lightbox_item.php?action=show_artist&artist='.$album->artist_id) ;?>"><?php echo $album->f_artist_name; if ($album->year != _('N/A')) { echo (" - ".$album->year);} ?></a></h2>	
					<div id="album_tagcloud"><h3>TAGS</h3><div class="tags"><?php if (Access::check('interface','100')) {echo $album->f_admin_tags ; } else { echo $album->f_tags ;} ?></div></div>	
				</div><!--#excerpt_info-->		
			</div><!-- large_info -->
		
			<div id="user_actions">
				<ul>
					<li><?php echo Ajax::text('?action=basket&type=album&id=' . $album->id,('Ajouter aux favoris'),'add_text_' . $album->id,'','fav_user_lightbox'); ?></li>
					<li class="sep"><?php echo Ajax::button('?action=basket&type=album&id=' . $album->id,'favorite_inactive',_('Add'),'play_full_' . $album->id,'','fav_user_lightbox_img'); ?></li>
					<li class="sep">
						<div class="play"><a href="" class="m_sep">Écouter l'album</a><a href=""><?php echo get_user_icon('user_play', _('Play')); ?></a>
							<span class="item_to_play" style="display:none">
							<?php $album_songs = new Album($album->id); $album_songs->format(); $songs = $album_songs->get_songs();	
							echo ('myPlaylist.setPlaylist([');
							foreach ($songs as $song_id) {	$song = new Song($song_id);	 $song->format();
								 echo ('{title: "'.$song->title.' - '.$song->f_album.'",artist: "'.$song->f_artist.'",mp3: "'.call_user_func(array(Song,'play_url'),$song->id).'",poster: "'.Config::get('web_path').'/image.php?id='.$song->album.'&thumb=3"},');	
							} echo (']);');	?>	
							</span>
						</div><!-- play -->
					</li>
						
					<li><a href="<?php echo $web_path; ?>/batch.php?action=album&amp;id=<?php echo $album->id; ?>">Télécharger</a></li>	
					<li><a href="<?php echo $web_path; ?>/batch.php?action=album&amp;id=<?php echo $album->id; ?>"><?php echo get_user_icon('user_usb', _('Download')); ?></a></li>
				</ul>	
				
				<!-- ADMIN ACCESS -->
				<?php if (Access::check('interface','100')) { ?>	
				<ul id="admin_actions">
				
					<li class="sep"><?php $icon = $album->selected ? 'favorite_active' : 'favorite_inactive'; $text = $album->selected ? 'DÉSACTIVER' : 'AJOUTER À LA SÉLECTION'; $button_flip_state_id = 'album_flip_state_lightbox_'.$album->id; ?>
						<span id="<?php echo($button_flip_state_id); ?>">
						<?php echo Ajax::text('?page=favorits&action=flip_state_album&album_id=' . $album->id,$text,'flip_album_lightbox_text_' . $album->id,'','m_sep'); ?>
						<?php echo Ajax::button('?page=favorits&action=flip_state_album&album_id=' . $album->id,$icon,_(ucfirst($icon)),'flip_album_lightbox_'.$album->id); ?>
						</span>
					</li>
					
					<li><a href="<?php echo Config::get('web_path'); ?>/admin/admin_lightbox_item.php?action=delete_album&album_id=<?php echo $album->id;?>" class="delete_album" id="<?php echo $album->id;?>">SUPPRIMER</a>
					</li>
					
					<li><a href="<?php echo Config::get('web_path'); ?>/admin/admin_lightbox_item.php?action=delete_album&album_id=<?php echo $album->id;?>" class="delete_album" id="<?php echo $album->id;?>"><?php echo get_user_icon('remove_picto', ('Supprimer')); ?></a>
					</li>
					<!--<li><a href="<?php echo $web_path; ?>/albums.php?action=update_from_tags&amp;album_id=<?php echo $album->id; ?>"><?php echo get_user_icon('cog', _('Update from tags')); ?></a></li>-->
				</ul>
				<?php  } ?>	
			
			</div><!--user_actions -->
		</div><!--informations-->
	<?php show_box_bottom(); ?>

	<?php
	$browse = new Browse();
	$browse->set_type('song');
	$browse->set_offset('100');
	$browse->set_simple_browse(true);
	$browse->set_filter('album', $album->id);
	$browse->set_sort('track', 'ASC');
	$browse->get_objects();
	$browse->show_objects();
	$browse->store();
	?>
	
	<!-- Zone de commentaires -->
	<h1 class="comment">COMMENTAIRES</h1>
	<div id="shout_objects">
	<?php $shouts = shoutBox::get_from_album($album->id);
		if (count($shouts)) { require Config::get('prefix') . '/templates/show_album_shoutbox.inc.php'; }
		else { echo ('<span class="nocomment">Aucun commentaire</span>');	} ?>
	</div>
		
	<!-- Ajout commentaires/tags -->
	<h1 class="comment">AJOUT DE COMMENTAIRES/TAGS</h1>
	<div id="show_add_comment_tag">
		
		<?php
		require_once Config::get('prefix') . '/templates/show_album_add_tag.inc.php';
		require_once Config::get('prefix') . '/templates/show_album_add_shout.inc.php';
		?>
	</div>
		
	</div><!-- #lightbox_wrapper -->
</div><!-- #scrollbar_wrapper -->
	<div id="up_lightbox" class="nav_scroll_info" onclick="lightboxScroll.scrollTo(0, -50, 200, true);return false">&larr; prev</div>
	<div id="down_lightbox" class="nav_scroll_info" onclick="lightboxScroll.scrollTo(0, 50, 200, true);return false">next &rarr;</div>
