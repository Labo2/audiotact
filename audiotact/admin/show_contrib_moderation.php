<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Preference
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
if (!Access::check('interface','100')) { access_denied();exit;}


$per_page = 1;
if ($_REQUEST['page']) { $page = $_REQUEST['page']; }
else { $page = 1;}
$start = ($page-1)*1;

$sql = "SELECT id FROM `tmp_submit` WHERE `enabled`='0' order by id limit $start, 1 ;";
$result = mysql_query($sql) or exit(mysql_error());?>

<div class="contrib_columns">
	

	<?php while ($data = mysql_fetch_assoc($result)) {
		$id_submit = $data['id'];	
		$submit_array[] = array ('id_prop' => $id_submit);
	}	
	
	/* POUR CHAQUE PROPOSITION */
	foreach ($submit_array as $key) {
		$id = $key['id_prop'];
		$matrice = array(); ?>
		
		<div id="contrib <?php echo $id;?>">
			<div id="contribScroll" class="contrib <?php echo $id;?>">
				<div id="scroller">
				<div class="artist_column">
					<?php /* RECHERCHE ARTIST */
					$sql = "SELECT artist_id, artist, bio, web, picture FROM `tmp_submit_data` INNER JOIN `tmp_submit_artist` ON `tmp_submit_data`.artist_id = tmp_submit_artist.id WHERE `tmp_submit`='$id' GROUP BY artist ;";
					$result_submit_artist = mysql_query($sql) or exit(mysql_error());	
					while ($data_submit_artist = mysql_fetch_assoc($result_submit_artist)) {
						$id_artist = $data_submit_artist['artist_id'];
						$artist = $data_submit_artist['artist'];
						$bio = $data_submit_artist['bio'];
						$web = $data_submit_artist['web'];
						$picture = $data_submit_artist['picture'];
						?>
						<div id="info_box_artist_contrib">
							<div class="image_contrib">
							<?php if ($picture) {?><img src="<?php echo $web_path.'/images_artist/'.$picture ;?>" width="134" /><?php }
							else {?><img width="134px" height="134px" src="<?php echo ($web_path . Config::get('theme_path') . '/images/blankalbum.png');?>"><?php } ?>
							</div>
							<div class="info_artist_contrib">
								<h1><?php echo $artist; ?></h1>
								<h2><?php echo $web; ?></h2>
							</div>
						
						</div>
						<div id="bio_box_contrib">
							<h2>BIOGRAPHIE</h2>
							<?php echo $bio; ?>
						</div>
						<?php } ?>
				</div><!-- artist_column -->
	
				<div class="album_column">
				<?php /* Recherche des albums */
				$sql = "SELECT album_id, album, year, genre, file, tag  FROM `tmp_submit_data` INNER JOIN `tmp_submit_album` ON `tmp_submit_data`.album_id = tmp_submit_album.id WHERE `tmp_submit`='$id'GROUP BY album;";
				$result_submit = mysql_query($sql) or exit(mysql_error());	
				while ($data_submit = mysql_fetch_assoc($result_submit)) {
					$id_album = $data_submit['album_id']; $album = $data_submit['album']; $year = $data_submit['year']; $genre = $data_submit['genre']; $file = $data_submit['file']; $tag = $data_submit['tag'];
					$submit_array_album = array ('id_album' => $id_album, 'album' => $album, 'year' => $year, 'genre' => $genre, 'file'=> $file, 'tag'=> $tag);
					$matrice[$id] = $submit_array_album;
					
					/* POUR CHAQUE ALBUM */
					foreach ($matrice as $key_album) {
						$album = $key_album['album']; $album_id = $key_album['id_album']; $year = $key_album['year']; $genre = $key_album['genre']; $file = $key_album['file']; $tag = $key_album['tag'];
						/* Search picture */
						$getID3 = new getID3;
						$newfile = $getID3->analyze($file);
						getid3_lib::CopyTagsToComments($newfile);			
						$picture = @$newfile['id3v2']['APIC'][0]['data']; // binary image data
						if ($picture) { $picture = '<img src="../image.php?type=cover&file='.$file.'" width="134">' ;}
						else { $picture = '<img src="'.$web_path . Config::get("theme_path") . '/images/blankalbum.png" width="134">' ;} ?>

						<div class="image_contrib"><?php echo $picture ; ?></div>
						<div class="info_album_contrib"><h1><?php echo $album;?></h1><h3>Année : <?php echo $year;?> - Genre : <?php echo $genre;?></h3><h4>Tags : <?php echo $tag;?></h4></div>
						<div class="songs_contrib">
							<?php /* SONGS */
							$sql = "SELECT file, title, artist, time, track FROM `tmp_submit_data` INNER JOIN `tmp_submit_artist` ON `tmp_submit_data`.artist_id = `tmp_submit_artist`.id WHERE `album_id`='$album_id' AND tmp_submit='$id';";
							$result_file = mysql_query($sql) or exit(mysql_error());
							while ($data_submit_file = mysql_fetch_assoc($result_file)) {
								$file = $data_submit_file['file']; $title = $data_submit_file['title']; $artist = $data_submit_file['artist']; $time = $data_submit_file['time']; $track = $data_submit_file['track'];?>
								<ul id="songs_item_contrib">
									<li class="song_item">			
									<?php $filename = explode('/', $file, 6); $m_file = $filename[5]; $min = floor($time/60); $sec = sprintf("%02d", ($time%60) ); $time = $min . ":" . $sec;?>										<a id="" class="audio" href="<?php echo $web_path.'/'.$m_file ; ?>"><?php echo ($title); ?></a>
									</li>
									<li class="track_item"><?php echo ($track); ?></li>
									<li class="title_item"><?php echo ($title); ?></li>
									<li class="artist_item"><?php echo ($artist); ?></li>
									<li class="time_item"><?php echo ($time); ?></li>
								</ul>
								<?php } /* end while songs */ ?>
							</div><!-- songs_contrib -->
							
							<div class="licences">
							<h4>Licence : 
							<?php 
							$sql = "SELECT `licence` FROM `tmp_submit_data` WHERE `tmp_submit`='$id' AND `album_id`='$album_id';"; 
							$result_licence = mysql_query($sql) or exit(mysql_error()); 
							while ($data_licence = mysql_fetch_assoc($result_licence)) {
								echo $data_licence['licence'];
							} ?>
							</h4>
							</div>
					<?php } /* end foreach album */
				} /* end while album */?>
				</div><!-- album_column -->
				</div>
			</div><!-- .contrib -->

			<!-- ADMIN VALIDATION -->
			<div class="validate_mod">
				VALIDER/SUPPRIMER CETTE PROPOSITION</a>
				<a id="<?php echo $id; ?>" class="admin_submit validate" href="<?php echo $web_path.'/admin/update_admin.php?action=add_submit&id='.$id ;?>">
					<img class="add_submit" title="Valider" alt="Valider" src="<?php echo ($web_path . Config::get('theme_path') . '/images/icons/icon_accept_checkbox.png');?>">
				</a>
				<a id="<?php echo $id; ?>" class="admin_submit delete" href="<?php echo $web_path.'/admin/update_admin.php?action=delete_submit&id='.$id ;?>">
					<img class="add_submit" title="Valider" alt="Valider" src="<?php echo ($web_path . Config::get('theme_path') . '/images/icons/icon_refuse_checkbox.png');?>">
				</a>
			</div><!-- .validate_mod -->
		</div><!-- #contrib -->
<?php } /* end foreach prop */?>
</div><!--contrib_columns-->
			
			
<div id="contrib_mod_pag">
<?php
	$adjacents = 3;
	$sql = "SELECT id FROM `tmp_submit` WHERE `enabled`='0' ;";
	$result = mysql_query($sql) or exit(mysql_error());		
	$total_pages = mysql_num_rows($result);

	$limit = 1; 								
	if($page) $start = ($page - 1) * $limit; 		
	else $start = 0;
	
	if ($page == 0) $page = 1;					
	$lastpage = ceil($total_pages/$limit);		

	$pagination .= "<ul>";
	
	if($lastpage > 1) {	
		if ($lastpage < 7 + ($adjacents * 2)) {	
			for ($counter = 1; $counter <= $lastpage; $counter++) {
				if ($counter == $page) { $pagination.= '<li class="active"><a href="'.$web_path.'/admin/show_contrib_moderation.php?page='.$counter.'">'.$counter.'</a></li>';}	
				else {$pagination.= '<li><a href="'.$web_path.'/admin/show_contrib_moderation.php?page='.$counter.'">'.$counter.'</a></li>';	}				
			}
		} elseif($lastpage > 5 + ($adjacents * 2)) {
			if($page < 1 + ($adjacents * 2)) {
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
					if ($counter == $page) {$pagination.= '<li class="active"><a href="'.$web_path.'/admin/show_contrib_moderation.php?page='.$counter.'">'.$counter.'</a></li>';}	
					else {$pagination.= '<li><a href="'.$web_path.'/admin/show_contrib_moderation.php?page='.$counter.'">'.$counter.'</a></li>';}						
				}
				$pagination.= "...";
				$pagination.= '<li><a href="'.$web_path.'/admin/show_contrib_moderation.php?page='.$lastpage.'">'.$lastpage.'</a></li>';	
			}
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
				$pagination.= '<li><a href="'.$web_path.'/admin/show_contrib_moderation.php?page=1">1</a></li>';
				$pagination.= '<li><a href="'.$web_path.'/admin/show_contrib_moderation.php?page=2">2</a></li>';
				$pagination.= "...";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
					if ($counter == $page) {$pagination.= '<li class="active"><a href="'.$web_path.'/admin/show_contrib_moderation.php?page='.$counter.'">'.$counter.'</a></li>';}		
					else {$pagination.= '<li><a href="'.$web_path.'/admin/show_contrib_moderation.php?page='.$counter.'">'.$counter.'</a></li>';	}				
				}
				$pagination.= "...";
				$pagination.= '<li><a href="'.$web_path.'/admin/show_contrib_moderation.php?page='.$lpm1.'">'.$lpm1.'</a></li>';
				$pagination.= '<li><a href="'.$web_path.'/admin/show_contrib_moderation.php?page='.$lastpage.'">'.$lastpage.'</a></li>';		
			} else {
				$pagination.= '<li><a href="'.$web_path.'/admin/show_contrib_moderation.php?page=1">1</a></li>';
				$pagination.= '<li><a href="'.$web_path.'/admin/show_contrib_moderation.php?page=2">2</a></li>';
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
					if ($counter == $page) {$pagination.= '<li class="active"><a href="'.$web_path.'/admin/show_contrib_moderation.php?page='.$counter.'">'.$counter.'</a></li>';}	
					else {$pagination.= '<li><a href="'.$web_path.'/admin/show_contrib_moderation.php?page='.$counter.'">'.$counter.'</a></li>';}					
				}
			}
		}
	}
	$pagination.= "</ul>";	?>
	<?=$pagination?>
</div><!-- #contrib_mod_pag-->
<div id="up" class="nav_scroll_info" onclick="contribScroll.scrollTo(0, -28, 200, true);return false">&larr; prev</div>
	<div id="down" class="nav_scroll_info" onclick="contribScroll.scrollTo(0, 28, 200, true);return false">next &rarr;</div>



