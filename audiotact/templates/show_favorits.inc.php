<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Random Albums
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

<a id="favorits_prev" href=""><img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/btn_nav_gauche.png" /></a>
<a id="favorits_next" href=""><img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/btn_nav_droite.png" /></a>

<div id="favorits_items_wrapper">
	<div id="favorits_items_content">
		<?php if ($selected) {	
			
			if ($key == "album") {			
				foreach ($selected as $album_id) {
					$selected = new Album($album_id); $selected->format(); ?>
		       		<div class="favorits">
		       			<a href="<?php echo $web_path; ?>/catalog_browse.php?tab=1&type=album&name=<?php echo $selected->f_name; ?>&id=<?php echo $album_id; ?>">
		                	<img src="<?php echo $web_path; ?>/image.php?thumb=3&amp;id=<?php echo $album_id; ?>" width="80" height="80" alt="<?php echo $name; ?>" title="<?php echo $name; ?>" />
		                <div class="info">
		                	<span class="name"><?php echo $selected->f_name; ?></span><br />
		                	<span class="sub_name"><?php echo $selected->f_artist ; ?></span>
		                </div></a>
		             </div>
				<?php } // end foreach album 
	
			} elseif ($key == "artist") {
				foreach ($selected as $artist_id) {
					$selected = new Artist($artist_id); $selected->format(); ?>
			        <div class="favorits">
			        	<a href="<?php echo $web_path; ?>/catalog_browse.php?tab=0&type=artist&name=<?php echo $selected->name; ?>&id=<?php echo $artist_id; ?>">
		                 <?php  $photo = $selected->artist_picture;
							if ($photo) { $image_link_min = $web_path.'/images_artist/'.$photo ; echo '<img width="80px" height="80px" src="'.$image_link_min.'">'; } 
							else { echo '<img width="80px" height="80px" src="'.$web_path . Config::get('theme_path').'/images/artist.png">';} ?>
			               
			                <div class="info">
			                	<span class="name"><?php echo $selected->name; ?></span><br />
			                	<span class="sub_name"><?php echo $selected->albums; ?> album<?php if (($selected->albums) > 1) {echo ('s');}?></span>
			                </div>
			            </a>
		            </div>
			     <?php } // end foreach artist 
			     
			} elseif ($key == "playlist") {
				foreach ($selected as $playlist_id) {
					$selected = new Playlist($playlist_id); $selected->format();
					$object_idp = $selected->get_items(); $count = $selected->get_song_count(); $ids = array();?>
		         	
		         	<div class="favorits">
			         	<a href="<?php echo $web_path; ?>/catalog_browse.php?tab=2&type=playlist&name=<?php echo $selected->f_name; ?>&id=<?php echo $playlist_id; ?>">
				       		<?php  foreach ($object_idp as $object) { $song = new Song($object['object_id']); $song->format(); $album_id = $song->album; $ids[] = $album_id;} 
				        	$ids = array_unique($ids);
				      		$url = urlencode(serialize($ids)); ?> 
							<img src="<?php echo $web_path; ?>/image.php?thumb=3&type=playlist&id=<?php echo ($url); ?>" width="80" height="80" alt="" title="<?php echo $name; ?>" />
				            <div class="info">
				              <span class="name"><?php echo $selected->name; ?></span><br />
				              <span class="sub_name"><?php echo $selected->genre ; ?></span>
				           </div>
				         </a>
		             </div>
		     	<?php } // end foreach playlist ?>
			<?php } // end if playlist
	} // end if selected
	?>
	</div><!-- favorits_items_content -->
</div><!--favorits_items_wrapper -->
	

