<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Artist
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
require_once 'lib/init.php';
session_start();
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

	var icon_add_picture = "<?php echo $web_path.Config::get('theme_path').'/images/icons/icon_add_a_picture.png'; ?>";	
	jQuery("input.add_picture").filestyle({ 
          image: icon_add_picture,
          imageheight : 19,
          imagewidth : 19,
          width : 200
      });

	});
</script>


<div id="scrollbar_wrapper">
	<div id="lightbox_wrapper">
		<?php
		$browse = new Browse();
		$browse->set_type($object_type);
		show_box_top('','info-box');
		?>
	
		<div id="informations">
			<div id="left_info">
				
				<div id="info_picture">					
					<div id="artist_picture">
					<?php $photo = $artist->artist_picture;
					if ($photo) { $image_link_min = $web_path.'/images_artist/'.$photo ; echo '<img width="150px" height="150px" src="'.$image_link_min.'">'; } 
					else { echo '<img width="150px" height="150px" src="'.$web_path . Config::get('theme_path').'/images/artist.png">';} ?>
					</div><!--#artist_picture-->
					
					<?php if (Access::check('interface','100')) { ?>
					<div class="admin_picture">
						VISUEL 
						<!-- btn add picture-->	
						<form method="post" class="artist_img_upload" enctype="multipart/form-data" action="<?php echo Config::get('web_path'); ?>/admin/admin_lightbox_item.php?action=update_artist_picture">	
						     <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
						     <input type="file" name="photo_artist" id="photo_artist" class="add_picture" />	
							<?php echo Core::form_register('update_artist_picture'); ?>
							<input type="hidden" class="id_artist" name="id_artist" value="<?php echo $artist->id; ?>" />
							<input type="hidden" name="name_artist" value="<?php echo $artist->f_name; ?>" />
						</form>
						<!-- btn delete picture-->					
						<a id="<?php echo $artist->id; ?>" class="artist_img_delete" href="<?php echo Config::get('web_path'); ?>/admin/admin_lightbox_item.php?action=delete_artist_picture&id_artist=<?php echo $artist->id; ?>"><img src="<?php echo $web_path.Config::get('theme_path').'/images/icons/icon_delete_a_picture.png'; ?>" title="delete_picture" alt="delete_picture" /></a>
	
					</div><!--admin_picture-->	
					<?php } ?>			
				</div><!-- #info_picture -->

			<div id="excerpt_info">
				<!-- Name 
				<div id="info_name">-->
				<h1><?php echo $artist->f_full_name; ?></h1>
				<?php if (Access::check('interface','100')) { ?>
				<a class="update_album_btn" id="update_album_btn" href="<?php echo $web_path. '/templates/show_update_name.inc.php?type=artist&id='.$artist->id; ?>">
					<img class="update" title="Éditer" alt="Éditer" src="<?php echo ($web_path . Config::get('theme_path') . '/images/icons/icon_edit_favorites.png');?>">
				</a>
				<?php } ?>
				<?php if ($artist->website) { $web = $artist->website ; } else { $web = "Non renseigné" ;}?>
				<!-- Web -->
				<div id="info_web">
					<h3>SITE WEB : <?php echo $web; ?></h3>
					<?php if (Access::check('interface','100')) { ?>
					<a class="update_album_btn" id="update_album_btn" href="<?php echo $web_path. '/templates/show_update_name.inc.php?type=web&id='.$artist->id; ?>">
						<img class="update" title="Éditer" alt="Éditer" src="<?php echo ($web_path . Config::get('theme_path') . '/images/icons/icon_edit_favorites.png');?>">
					</a>
					<?php } ?>
				</div>
			</div><!-- #excerpt_info -->
		</div><!-- #left_info -->
		
		
		<div id="right_info">
			<h1>BIOGRAPHIE </h1>
			<?php if (Access::check('interface','100')) { ?>
			<a class="update_bio_btn" id="update_bio_btn" href="<?php echo Config::get('web_path'); ?>/admin/admin_lightbox_item.php?action=update_artist_bio">
				<img class="update active" title="Éditer" alt="Éditer" src="<?php echo ($web_path . Config::get('theme_path') . '/images/icons/icon_edit_favorites.png');?>">
				<img class="updating" style="display:none" title="Éditer" alt="Éditer" src="<?php echo ($web_path . Config::get('theme_path') . '/images/icons/icon_edit_button.png');?>">
			</a>
			<form id="update_artist_bio" method="POST" action="<?php echo Config::get('web_path'); ?>/admin/admin_lightbox_item.php?action=update_artist_bio">
				<textarea class="biotext" name="artist_bio" value="<?php echo $artist->biography; ?>"><?php echo $artist->biography; ?></textarea>
				<input id="id" type="hidden" name="artist_id" value="<?php echo $artist->id ; ?>" />
			</form>
			<?php } else { ?>
			<div class="bio"><?php echo nl2br($artist->biography); ?></div>
			<?php }?>
		</div><!-- #right_info -->	
	</div><!-- #informations -->


	<div id="user_actions">
		<ul><li><?php echo Ajax::text('?action=basket&type=artist&id=' . $artist->id,('Ajouter à vos favoris'),'add_text_' . $artist->id,'','fav_user_lightbox'); ?></li>	
			<li class="sep"><?php echo Ajax::button('?action=basket&type=artist&id=' . $artist->id,'favorite_inactive',_('Add'),'add_' . $artist->id,'','fav_user_lightbox_img'); ?></li>
						
			<li class="sep"><div class="play"><a href="" class="m_sep">Écouter l'artiste</a><a href=""><?php echo get_user_icon('user_play', _('Play')); ?></a>
					<span class="item_to_play" style="display:none">
						<?php $artist_songs = new Artist($artist->id); $artist_songs->format(); $songs = $artist_songs->get_songs();
						echo ('myPlaylist.setPlaylist([');	
						foreach ($songs as $song_id) { $song = new Song($song_id); $song->format();
					 		echo ('{title: "'.$song->title.' - '.$song->f_album.'",artist: "'.$song->f_artist.'",mp3: "'.call_user_func(array(Song,'play_url'),$song->id).'",poster: "'.Config::get('web_path').'/image.php?id='.$song->album.'&thumb=3"},');	
						} echo (']);');	?>
					</span>	
				</div>
			</li>
			<li><a href="<?php echo $web_path; ?>/batch.php?action=artist&id=<?php echo $artist->id; ?>">Télécharger </a></li>
			<li><a href="<?php echo $web_path; ?>/batch.php?action=artist&id=<?php echo $artist->id; ?>"><?php echo get_user_icon('user_usb', _('Download')); ?></a></li>		
		</ul>
		
		<!-- ADMINISTRATION ACTIONS -->
		<?php if (Access::check('interface','100')) { ?>
			<ul id="admin_actions">
				<li class="sep">
					<?php $icon = $artist->selected ? 'favorite_active' : 'favorite_inactive'; $text = $artist->selected ? 'DÉSACTIVER' : 'AJOUTER À LA SÉLECTION'; $button_flip_state_id = 'artist_flip_state_lightbox_' .$artist->id; ?>
					<span id="<?php echo($button_flip_state_id); ?>">
						<?php echo Ajax::text('?page=favorits&action=flip_state_artist&artist_id=' . $artist->id,$text,'flip_artist_lightbox_text_' . $artist->id,'','m_sep'); ?>
						<?php echo Ajax::button('?page=favorits&action=flip_state_artist&artist_id=' . $artist->id,$icon,_(ucfirst($icon)),'flip_artist_lightbox_' . $artist->id); ?>
					</span>
				</li>
	
				<li><a href="<?php echo Config::get('web_path'); ?>/admin/admin_lightbox_item.php?action=delete_artist&artist_id=<?php echo $artist->id;?>" class="delete_artist" id="<?php echo $artist->id;?>">SUPPRIMER</a>
				</li>
				<li><a href="<?php echo Config::get('web_path'); ?>/admin/admin_lightbox_item.php?action=delete_artist&artist_id=<?php echo $artist->id;?>" class="delete_artist" id="<?php echo $artist->id;?>"><?php echo get_user_icon('remove_picto', ('Supprimer')); ?></a>
				</li>	
			</ul>	
		<?php } ?>
	</div><!-- user_actions -->

	<?php show_box_bottom(); ?>
	<?php $browse->show_particular_objects($object_ids); $browse->store();?>
	
</div><!-- #lightbox_wrapper -->
</div><!-- #scrollbar_wrapper -->
<div id="up_lightbox" class="nav_scroll_info" onclick="lightboxScroll.scrollTo(0, -50, 200, true);return false">&larr; prev</div>
<div id="down_lightbox" class="nav_scroll_info" onclick="lightboxScroll.scrollTo(0, 50, 200, true);return false">next &rarr;</div>
