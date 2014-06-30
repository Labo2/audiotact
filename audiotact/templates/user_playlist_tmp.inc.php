<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Right Bar
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
//require_once '../lib/init.php';
$session_id = session_id();
$web_path = Config::get('web_path');
$ajax_url = Config::get('ajax_url');
$playlist_id = $GLOBALS['user']->playlist->id;
?>

<script type="text/javascript">
jQuery.noConflict();	
jQuery(function(){
	tmp_favoritScroll = new iScroll('item_playlist_wrapper', { hScrollbar: false, vScrollbar: false, lockDirection:true	});
	
	
	/* SORTABLE - DRAGGABLE - DROPPABLE */
	/* sortable */
	var url="<?php echo $ajax_url.'?page=user_playlist&action=tmp_sortable&playlist_id='.$playlist_id ;?>";
	jQuery('#rb_current_playlist').sortable({ 
		cursor: "move", disabled : true, helper: "clone", appendTo: document.body,
		sort: function( event, ui ) { tmp_favoritScroll.disable();},
		update: function() {  
			var order = jQuery('#rb_current_playlist').sortable('serialize'); 
			jQuery.post(url,order); 
			
			tmp_favoritScroll.enable();
			jQuery('#rb_current_playlist').removeClass('activesortable');
			jQuery('#rb_current_playlist').sortable("disable");
			jQuery('#rb_current_playlist li').removeClass('selected');
		}
	});
	
	jQuery('#rb_current_playlist').disableSelection();
	
	/* Active item */
	jQuery('#rb_current_playlist li').click(function(){
		if (jQuery(this).hasClass('selected')) {
			jQuery(this).removeClass('selected');
			jQuery(this).parent().removeClass('activesortable');
			tmp_favoritScroll.enable();
			jQuery('#rb_current_playlist').sortable("disable");
		} else {
			jQuery('#rb_current_playlist li').removeClass('selected');
			jQuery(this).addClass('selected');
			jQuery(this).parent().addClass('activesortable');
			jQuery('#rb_current_playlist').sortable("enable");
			tmp_favoritScroll.disable();
		}
	});

	jQuery('.ui-widget-header').droppable({
		hoverClass: "ui-state-active",
		drop: function( event, ui ) {
			var playlist_id = jQuery(this).attr('id');
			var tmp_item_id = (ui.draggable).children('span.tmp_item_id').attr('id');
			var song_id = (ui.draggable).children('span.tmp_song_id').attr('id');
			var url = "<?php echo $ajax_url.'?page=user_playlist&action=drag_tmp_to_saved&playlist_id=';?>"+playlist_id+'&song_id='+song_id+'&tmp_item_id='+tmp_item_id;
			jQuery.post(url);
			(ui.draggable).remove();
			tmp_favoritScroll.enable();
		}
	});
	

	jQuery('input, textarea').keyboard({
		layout       : 'french-azerty-2',
  		customLayout : { default: ['{cancel}'] },
  		usePreview: false,
  		position : {
 			of : null,
  			my : 'left bottom',
 			at : 'left bottom',
 			at2: 'left-30 top-10' 
		}
  	});
}); // end
</script>	


<form method="post" id="edit_tmp_playlist" action="<?php echo $web_path.'/lightbox_item.php?action=edit_tmp_playlist' ;?>">
		<input class="playlist_name" type="text" placeholder="TITRE DE VOTRE PLAYLIST" name="name" size="25" value="<?php echo $GLOBALS['user']->playlist->playlist_name; ?>" />
		<?php $selected_genre = $GLOBALS['user']->playlist->playlist_genre; ?>
		<div class="select_genre">
		<select name="genre">
			<option value="Inclassables, autre" disabled <?php if(!$selected_genre) {echo ('selected');}; ?>>GENRE</option>
			<?php 
			$genres = array('Blues','Classique','Hip-Hop','Chanson francophone','Musiques du monde','Rock/Pop','Bruitages, samples','Expérimentale','Électronique','Jazz','Funk, soul','Création radiophonique','Musiques traditionnelles','Inclassables, autres');
			foreach ($genres as $genre) {
				echo '<option value="'.$genre.'"';
				if ($genre == $selected_genre) { echo ' selected="selected"'; }
				echo '>'.$genre.'</option>';
			} ?>
		</select>
		</div>
		<input type="hidden" name="pl_typ" value="private" />
		<input type="hidden" name="id" value="<?php echo ($session_id); ?>" />		
</form>

<ul class="fav_tools">
	<li><?php echo Ajax::button('?page=user_playlist&action=create','save_playlist',_('Save playlist'),'rb_create_playlist','','save_playlist'); ?></li>
	
</ul>

<div id="item_playlist_wrapper">
<div id="scroller">
	<ul id="rb_current_playlist">
		<?php
			$objects = array();		
			//FIXME :: this is kludgy
			if (!defined('NO_SONGS')) {
				$objects = $GLOBALS['user']->playlist->get_items();
			}
			// Limit the number of objects we show here
			if (count($objects) > 100) {
				$truncated = (count($objects) - 100);
				$objects = array_slice($objects, 0, 100, true);
			}
		
			$normal_array = array('radio','song','video','random');
		
			foreach ($objects as $uid=>$object_data) {
				$type = array_shift($object_data);
				if (in_array($type,$normal_array)) {
					$object = new $type(array_shift($object_data));
					$object->format();
				}
				if ($type == 'random') {
					$object->f_link = Random::get_type_name($type);
				} ?>

		<li id="track_<?php echo $object->id; ?>" class="<?php echo flip_class(); ?> ui-widget-content" >
			<span id="<?php echo $uid; ?>" style="display:none" class="id tmp_item_id"><?php echo $uid; ?></span>
			<span id="<?php echo $object->id; ?>" style="display:none" class="id tmp_song_id"><?php echo $object->id; ?></span>
  			<?php echo ($object->title.' - '.$object->f_artist);  ?>
  			<span class="move_item"><?php echo get_user_icon('up_down_picto',_('Up Down Item')); ?></span>
 			<?php echo Ajax::button('?page=user_playlist&action=delete_tmp_track&id=' . $uid,'trash_picto',_('Delete'),'rightbar_delete_' . $uid,'','delitem'); ?>
		</li>
		<?php } if (!count($objects)) { ?>
			<li class="error"><?php echo ('Aucun titre ajouté à vos favoris'); ?></li>
		<?php } ?>
		<?php if (isset($truncated)) { ?>
			<li class="<?php echo flip_class(); ?>">
				<?php echo $truncated . ' ' . _('More'); ?>...
			</li>
		<?php } ?>
	</ul>

<?php
// We do a little magic here to force a iframe reload depending on preference
// We do this last because we want it to load, and we want to know if there is anything
// to even pass
if (count($objects)) { Stream::run_playlist_method();}
?>
</div><!-- scroller -->
	<div id="tmp_up" class="nav_scroll_info" onclick="tmp_favoritScroll.scrollTo(0, -50, 200, true);return false">&larr; prev</div>
	<div id="tmp_down" class="nav_scroll_info" onclick="tmp_favoritScroll.scrollTo(0, 50, 200, true);return false">next &rarr;</div>
</div><!--item_playlist_wrapper-->


