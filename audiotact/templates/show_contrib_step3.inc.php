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
require_once '../lib/init.php';
$web_path = Config::get('web_path');?>

<script type="text/javascript">
jQuery.noConflict();
jQuery(function(){	
	jQuery("#main_tabs").tabs( "enable" );
	jQuery("#main_tabs").tabs( 'select', 2);
	jQuery("#main_tabs").tabs("option", "disabled", [ 0,1,3 ] );
	contribTagScroll = new iScroll('scroll_tag', { hScrollbar: false, vScrollbar: false, onBeforeScrollStart: function (e) { var target = e.target;
			while (target.nodeType != 1) target = target.parentNode;
			if (target.tagName != 'SELECT' && target.tagName != 'INPUT' && target.tagName != 'TEXTAREA')
				e.preventDefault();
		}
	});
	jQuery('input,textarea').keyboard({
		layout       : 'french-azerty-2',
  		customLayout : { default: ['{cancel}'] },
  		usePreview:true,
  		position : {
 			of : jQuery('#content'),
  			my : 'center center',
 			at : 'center center',
 			at2: 'center top' 
		}
  	});
	jQuery('#submit_contrib .ui-widget-content').each(function(){jQuery(this).removeClass('ui-widget-content');});

	var selecting = "<?php echo $web_path.Config::get('theme_path').'/images/icons/admin_browse_button.png'; ?>";
	jQuery("input.file_img").filestyle({ image: selecting,imageheight : 27,imagewidth : 54,width : 300});
      
	jQuery("#ajax-form2").submit(function() { 
    	jQuery(this).ajaxSubmit({
			success: function() {
				jQuery("#main_tabs").tabs( "enable" );
				jQuery("#main_tabs").tabs( 'select', 3);
				jQuery("#main_tabs").tabs("option", "disabled", [ 0,1,2 ] );
			}, 
			target:jQuery("#ui-tabs-4")
		}); 
    	return false; 
	});
	jQuery('.last_step').live('click', function(){jQuery('#ajax-form2').submit();});
});
</script>


<?php
	$id_submit = $_REQUEST['id'];
	// GROUPEMENT D'ALBUMS
	$sql = "SELECT album, album_id, file, year, genre FROM `tmp_submit_data` INNER JOIN `tmp_submit_album` ON album_id = tmp_submit_album.id WHERE `tmp_submit`=$id_submit GROUP BY album_id;"; 
	$result = mysql_query($sql) or exit(mysql_error());
	
	// GROUPEMENT D'ARTISTES
	$sql2 = "SELECT artist, artist_id, bio, web, picture FROM `tmp_submit_data` INNER JOIN `tmp_submit_artist` ON artist_id = tmp_submit_artist.id WHERE `tmp_submit`=$id_submit GROUP BY artist_id;"; 
	$result2 = mysql_query($sql2) or exit(mysql_error());
 ?>
 
<div id="main_tabs_content">
	<div id="submit_contrib">
	<div id="scroll_tag" class="step-3 step">
	<div id="scroller">
		<form id="ajax-form2" class="autosubmit"  enctype="multipart/form-data" method="POST" action="<?php echo $web_path.'/submit_music_write_tag.php?action=write_album_artist_tag&id='.$id_submit ?>">
			<!-- ARTISTS -->
			<div id="artists_column">
			<?php while ($data = mysql_fetch_array($result2)) { 
					$artist = $data['artist']; ?>
					<div class="artist_box">
						<div class="box_img">
							<img width="150px" height="150px" src="<?php echo $web_path . Config::get('theme_path').'/images/artist.png' ;?>">
						</div>
						<div class="box_info">
							<h1><?php echo $data['artist'] ; ?></h1>
							<input placeholder="SITE WEB" name="<?php echo ('update['.$artist.'][web]')?>" value="<?php echo $data['web'] ?>" />
							<h2>Visuel de l'artiste</h2>
							<p>Pour associer un visuel à votre artiste, veuillez cibler un visuel ci-dessous (jpeg, jpg / 2Mo maximum).</p>
							<input type="file" name="<?php echo ('update['.$artist.'][picture_artist]')?>" accept="image/jpeg, image/gif, image/png" class="file_img">
						</div>
						
						<div class="box_bio">
							<h3>BIOGRAPHIE</h3>
							<textarea class="bio" rows="5" cols="70" name="<?php echo ('update['.$artist.'][bio]')?>"></textarea>
						</div>
						<input type="hidden" name="<?php echo ('update['.$artist.'][artist]')?>" value="<?php echo $data['artist'] ?>" />
						<input type="hidden" name="<?php echo ('update['.$artist.'][artist_id]')?>" value="<?php echo $data['artist_id'] ?>" />
					</div><!-- artist_box -->
				<?php } /* end while artist */ ?>
			</div><!-- artists_column -->

			<div id="albums_column">
			<?php /* Foreach album*/
				while ($data = mysql_fetch_array($result)) { 
					$album = $data['album'];?>
					<div class="album_box">
						<div class="box_img">
							<?php
								$getID3 = new getID3;
								$filename = $data['file'];
								$newfile = $getID3->analyze($filename);
								getid3_lib::CopyTagsToComments($newfile);				
								$picture = @$newfile['id3v2']['APIC'][0]['data']; 
								$file = urlencode($filename);
								if ($picture) { $picture = '<img src="image.php?type=cover&file='.$file.'" width="150px">' ;} 
								else { $picture = '<img src="'.$web_path.Config::get('theme_path').'/images/artist.png" width="150px">' ; }
								echo $picture ; ?>
						</div>
						
						<div class="box_info">
							<h1><?php echo $data['album'] ; ?></h1>
							<h2>Visuel de l’album</h2>
							<p>Si vous souhaitez changer la jaquette de l’album, veuillez cibler un visuel ci-dessous (jpeg, jpg / 2Mo maximum).</p>	
							<input type="file" name="<?php echo ('submit['.$album.'][picture]')?>" accept="image/jpeg, image/gif, image/png" class="file_img">
							<?php /* PICTURE */	
							echo '<select style="display:none" name="APICpictureType">';
							$APICtypes = getid3_id3v2::APICPictureTypeLookup('', true);
							foreach ($APICtypes as $key => $value) {
								echo '<option value="'.htmlentities($key, ENT_QUOTES).'">'.htmlentities($value).'</option>';
							}
							echo '</select>';?>
						</div>
					
						<div class="box_bio">
							<div class="item_info">		
								<label>Année</label>
								<input class="year" name="<?php echo ('submit['.$album.'][year]')?>" value="<?php echo $data['year'] ?>" />
							</div>
							<div class="item_info box_genre">	
								<label>Genre</label>
								<?php $genre = $data['genre'];
								$ArrayOfGenresTemp = getid3_id3v1::ArrayOfGenres();  
								foreach ($ArrayOfGenresTemp as $key => $value) {  $ArrayOfGenres[$value] = $value; }
								unset($ArrayOfGenresTemp); unset($ArrayOfGenres['Cover']); unset($ArrayOfGenres['Remix']); unset($ArrayOfGenres['Unknown']);
								$ArrayOfGenres['']      = '- Unknown -'; $ArrayOfGenres['Cover'] = '-Cover-'; $ArrayOfGenres['Remix'] = '-Remix-';
								asort($ArrayOfGenres); 
								$genre = array($genre); 
								?>
								<div class="select_tag_genre">
									<select name="<?php echo ('submit['.$album.'][genre]')?>">
									<?php foreach ($ArrayOfGenres as $key => $value) {
											echo '<option value="'.htmlentities($key, ENT_QUOTES).'"';
											if (in_array($key, $genre)) { echo ' selected="selected"'; unset($genre[array_search($key, $genre)]); sort($genre); }
											echo '>'.htmlentities($value).'</option>';
									} ?>
									</select>
								</div>
								<input class="other" type="text" name="<?php echo ('submit['.$album.'][GenreOther]')?>" size="10" value="" placeholder="Autre">
							</div>
							<label>Tags</label>
							<input class="tags" name="<?php echo ('submit['.$album.'][tag]')?>" value="" />
						</div><!--box_bio-->
						<input type="hidden" name="<?php echo ('submit['.$album.'][album]')?>" value="<?php echo $data['album'] ?>" />
						<input type="hidden" name="<?php echo ('submit['.$album.'][album_id]')?>" value="<?php echo $data['album_id'] ?>" />
					</div><!-- album_box -->
				<?php } /* endwhile albums */ ?>
			</div><!-- #albums_column -->
		</div></div>
		<input class="submit last_step next_step" id ="button" type="submit" name="WriteAlbumTags" value="Save Changes">
		</form>
		
	<div id="up_contrib" class="nav_scroll_info" onclick="contribTagScroll.scrollTo(0, -40, 200, true);return false">&larr; prev</div>
	<div id="down_contrib" class="nav_scroll_info" onclick="contribTagScroll.scrollTo(0, 40, 200, true);return false">next &rarr;</div>
	</div><!-- submit_contrib -->
</div><!--main_tabs_content-->
