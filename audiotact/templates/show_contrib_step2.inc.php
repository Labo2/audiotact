<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at http://getid3.sourceforge.net                 //
//            or http://www.getid3.org                         //
/////////////////////////////////////////////////////////////////
//                                                             //
// /demo/demo.simple.php - part of getID3()                    //
// Sample script for scanning a single directory and           //
// displaying a few pieces of information for each file        //
// See readme.txt for more details                             //
//                                                            ///
/////////////////////////////////////////////////////////////////
require_once '../lib/init.php';
$web_path = Config::get('web_path');
session_start();
?>

<script type="text/javascript">
jQuery.noConflict();
jQuery(function(){	
	jQuery("#main_tabs").tabs( "enable" );
	jQuery("#main_tabs").tabs( 'select', 1);
	jQuery("#main_tabs").tabs("option", "disabled", [ 0,2,3 ] );
	contribUploadScroll = new iScroll('scroll', { hScrollbar: false, vScrollbar: false, onBeforeScrollStart: function (e) { var target = e.target;
			while (target.nodeType != 1) target = target.parentNode;
			if (target.tagName != 'SELECT' && target.tagName != 'INPUT' && target.tagName != 'TEXTAREA')
				e.preventDefault();
		}
	});
	jQuery('input').keyboard({
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

	var action = jQuery('#ajax-form').attr('action');
	jQuery('#ajax-form').submit(function(e){
		var data = jQuery('#ajax-form').serialize();
		e.preventDefault();
		jQuery.post(action, data, function(data) {
    		jQuery("#ui-tabs-3").load("templates/show_contrib_step3.inc.php?id="+data, function(){});
  		});
	});
	jQuery('.next_step_3').live('click', function(){ jQuery('#ajax-form').submit();});
	jQuery('span.help').live('click',function(){jQuery("#show_help").toggle();});
});
</script>

<div id="main_tabs_content">
	<div id="submit_contrib" >
	<div id="scroll" class="step-2 step">
		<?php $id_submit = $_REQUEST['id'] ;
		$sql = "SELECT tmp_submit_data.id, fileformat, file, album_id, genre, title, album, year, artist, artist_id, track, artist_id 
		FROM `tmp_submit_data` 
		INNER JOIN  `tmp_submit_album` ON album_id = tmp_submit_album.id 
		INNER JOIN  `tmp_submit_artist` ON artist_id = tmp_submit_artist.id WHERE `tmp_submit` =$id_submit ;";	
		$result = mysql_query($sql) or exit(mysql_error()); ?>

		<form id="ajax-form" class="autosubmit" method="POST" action="submit_music_write_tag.php?action=write_song_tag&id_submit=<?php echo $id_submit;?>">
			<table id="submit_songs">
				<tr class="values">
					<td class="track"> N°</td>
					<td class="title"> TITRE </td>
					<td class="artist"> ARTISTE </td>
					<td class="album"> ALBUM </td>
					<td class="licence"> LICENCE <span class="help"><img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/help.png" title="À propos des licences" alt="À propos des licences" /></span></td>
				</tr>
		
				<?php
				while ($data = mysql_fetch_assoc($result)) {
					$dirname =  $data['file'];
					$id = $data['id'];?>
					<tr>
						<td class="track"><input name="<?php echo ('submit['.$id.'][track]')?>" value="<?php echo $data['track'] ?>" /></td>
						<td class="title"><input name="<?php echo ('submit['.$id.'][title]')?>" value="<?php echo $data['title'] ?>" /></td>
						<td class="artist">
							<input name="<?php echo ('submit['.$id.'][artist]')?>" value="<?php echo $data['artist'] ?>" />
							<input type="hidden" name="<?php echo ('submit['.$id.'][artist_id]')?>" value="<?php echo $data['artist_id'] ?>" />
						</td>
			
						<td class="album">
							<input name="<?php echo ('submit['.$id.'][album]')?>" value="<?php echo $data['album'] ?>" />
							<input type="hidden" name="<?php echo ('submit['.$id.'][album_id]')?>" value="<?php echo $data['album_id'] ?>" />
							<input type="hidden" name="<?php echo ('submit['.$id.'][genre]')?>" value="<?php echo $data['genre'] ?>" />
							<input type="hidden" name="<?php echo ('submit['.$id.'][year]')?>" value="<?php echo $data['year'] ?>" />
							
							<?php $getID3 = new getID3;
							$fileinfo = $getID3->analyze($dirname);
							if (isset($fileinfo['id3v2']['APIC'][0]['data'])) { /*$cover = $getID3->info['id3v2']['APIC'][0]['data'];*/ $cover = 'true' ; } 
							elseif (isset($fileinfo['id3v2']['PIC'][0]['data'])) { $cover = 'true';} 
							else { $cover = ''; } ?>
							<input type="hidden" name="<?php echo ('submit['.$id.'][cover]')?>" value="<?php echo $cover ?>" />
							<input id="fileformat" type="hidden" name="<?php echo ('submit['.$id.'][fileformat]')?>" value="<?php echo $data['fileformat'] ?>" />
							<input id="filename" type="hidden" name="<?php echo ('submit['.$id.'][filename]')?>" value="<?php echo $data['file'] ?>" />
							<input id="id" type="hidden" name="<?php echo ('submit['.$id.'][id]')?>" value="<?php echo $data['id'] ?>" />
						</td>
						<td class="licence">
							<div class="select_licence">
								<select name="<?php echo ('submit['.$id.'][licence]')?>">
									<option value="" disabled <?php if(!$selected_licence) {echo ('selected');}; ?>>LICENCE</option>
									<?php $licences = array('Paternité','Paternité, Pas de Modification','Paternité, Pas d’Utilisation Commerciale, Pas de Modification','Paternité, Pas d’Utilisation Commerciale','Paternité, Pas d’Utilisation Commerciale, Partage dans les mêmes conditions','Paternité, Partage dans les mêmes conditions');
									foreach ($licences as $licence) { 
										echo '<option value="'.$licence.'"';
										if ($licence == $selected_licence) { echo ' selected="selected"'; }
										echo '>'.$licence.'</option>';
									} ?>
								</select>
							</div>
						</td>
					</tr>	
				<?php } /*end while */ ?>
			</table>
			</div>
			<input class="submit next_step next_step_3" id ="button" type="submit" name="WriteTags" value="Save Changes">
		</form>
		
		<div id="show_help" style="display:none"><img height="270px" src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/licences.png" title="À propos des licences" alt="À propos des licences" /></div>
		<div id="up_contrib" class="nav_scroll_info" onclick="contribUploadScroll.scrollTo(0, -40, 200, true);return false">&larr; prev</div>
		<div id="down_contrib" class="nav_scroll_info" onclick="contribUploadScroll.scrollTo(0, 40, 200, true);return false">next &rarr;</div>
	</div><!-- submit_contrib-->
</div><!--main_tabs_content-->


