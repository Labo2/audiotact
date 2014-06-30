<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Browse catalog
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

/**
 * Browse Catalog Page
 * This page shows the browse menu, which allows you to browse by many different
 * fields including artist, album, and catalog.
 *
 * This page also handles the actual browse action
 */

/* Base Require */
require_once 'lib/init.php';
session_start();
define('CATALOG','1');
show_header();
$web_path = Config::get('web_path');
$currentUrl = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
$session_id = session_id();
?>

<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function() {

	/* TIMER */
	var idleTime = 270000; /* Tps de la session - miliseconds*/ var redirectAfter = 30; /* + Tps avant la redirection */ var running = false;  var timer; 		
	if (jQuery("#dialog-session").length == 0) { var confirmDialog = jQuery('<div id="dialog-session" style="display:none">Votre session va expirer dans <span id="counter"></span> secondes.<br />Voulez-vous continuer ?</div>').appendTo('body');}
	confirmDialog.dialog({ title: 'Êtes-vous toujours là ?', autoOpen: false, closeOnEscape: false, draggable: false, resizable: false, height:160, width:500, modal: true, dialogClass: "lightbox_session", buttons: { "Oui, continuer": function() { confirmDialog.dialog( "close" ); }}, close: function(event, ui) { clearInterval(timer); running = false; jQuery.ajax({ url: <?php echo ("'".$currentUrl."'") ;?>, async: false }); },}); 	
	jQuery.idleTimer(idleTime);
	jQuery(document).bind("idle.idleTimer", function(){
	if(jQuery.data(document,'idleTimer') === 'idle' && !running){
		var counter = 30; running = true; jQuery('#counter').html(30); confirmDialog.dialog('open'); 
		timer = setInterval(function(){ counter -= 1;
			if(counter === 0) {
				confirmDialog.html('Votre session a expiré. Pour des raisons de sécurité, vous allez être redirigé sur la page dacceuil.'); confirmDialog.dialog('disable');
				window.location = <?php echo ("'".$web_path."/logout_user.php?id=".$session_id."'")?>; } 
			else { jQuery('#counter').html(counter); };}, 1000);
	};});
	/* END TIMER */
	
/* Open favorits lightbox*/	
var type = "<?php echo $_REQUEST['type'];?>";
if (type) {
	var tab = parseInt("<?php echo $_REQUEST['tab'];?>");
	jQuery('#tabs').tabs( 'select', tab);
	var url = "<?php echo $web_path ;?>/lightbox_item.php?action=show_"+type+"&"+type+"=<?php echo $_REQUEST['id'] ;?>" ;
	var name= "<?php echo $_REQUEST['name'];?>";
				
	if (jQuery("#lightbox_catalog").length == 0) {var ajaxDialog = jQuery('<div id="lightbox_catalog" style="display:hidden" title="'+(name)+'"></div>').appendTo('body');}    
		ajaxDialog.dialog({
			autoOpen: false, width:860, height:700, modal:true, resizable: false, draggable: false, 
			show: { effect: "clip", easing:"swing", duration:750 },hide: { effect: "clip"},close: function(event, ui) { ajaxDialog.remove();}
		});
		ajaxDialog.load(url, function(){
			lightboxScroll = new iScroll('scrollbar_wrapper', { hScrollbar: false, vScrollbar: false, onBeforeScrollStart: function (e) { var target = e.target;
			while (target.nodeType != 1) target = target.parentNode;
			if (target.tagName != 'SELECT' && target.tagName != 'INPUT' && target.tagName != 'TEXTAREA')
				e.preventDefault();
		}
	});
	});
		ajaxDialog.dialog("open");
		return false;
}/*end lightbox favorits*/	

/* Open Search */
var search = "<?php echo $_REQUEST['search'];?>";
if (search) {
	if (jQuery("#lightbox_search").length == 0) { var ajaxDialog = jQuery('<div id="lightbox_search" style="display:hidden"></div>').appendTo('body');}
    ajaxDialog.dialog({
    	autoOpen: false, 
    	width:1050, height:50, modal:true, position: "center top+80", resizable: false, draggable: false,
    	dialogClass: "search_box",
		show: { effect: "clip"/*, easing:"swing", duration:750 */},
		hide: { duration:150 },
		close: function(event, ui) { ajaxDialog.remove();}
	});

    ajaxDialog.load("<?php echo $web_path. '/templates/show_search_bar.inc.php';?>");
    ajaxDialog.dialog("open");
   }


jQuery('#jquery_jplayer_N').live('click', function(){
	var img_link = jQuery(this).children('img').attr('src');
	var img_split = img_link.split('&');
	var album_id = img_split[0].split('?id=');
	var id = album_id[1]; 
	jQuery('#tabs').tabs( 'select', 'album');
	var url = "<?php echo $web_path ;?>/lightbox_item.php?action=show_album&album="+id ;
	var name= jQuery('.jp-title .jp-artist').text();

	if (jQuery("#lightbox_catalog").length == 0) {var ajaxDialog = jQuery('<div id="lightbox_catalog" style="display:hidden" title="'+(name)+'"></div>').appendTo('body');}    
		ajaxDialog.dialog({
			autoOpen: false, width:860, height:700, modal:true, resizable: false, draggable: false, 
			show: { effect: "clip", easing:"swing", duration:750 },hide: { effect: "clip"},close: function(event, ui) { ajaxDialog.remove();}
		});
		ajaxDialog.load(url, function(){
			lightboxScroll = new iScroll('scrollbar_wrapper', { hScrollbar: false, vScrollbar: false, onBeforeScrollStart: function (e) { var target = e.target;
			while (target.nodeType != 1) target = target.parentNode;
			if (target.tagName != 'SELECT' && target.tagName != 'INPUT' && target.tagName != 'TEXTAREA')
				e.preventDefault();
		}
	});
	});
		ajaxDialog.dialog("open");
		return false;

	});	
});/* end */
</script>

<div id="tabs" class="tabs-bottom">
<div class="wrapper_tabs">
	<ul>
		<li><a href="catalog_browse_artist.php">ARTISTES</a></li>
		<li><a href="catalog_browse_album.php">ALBUMS</a></li>
		<li><a href="catalog_browse_playlist.php">PLAYLISTS</a></li>
		<li><a href="catalog_browse_tag.php">TAGS</a></li>
		<li><a href="catalog_browse_favorits.php">FAVORIS</a></li>
	</ul>
	<div id="footer_filter_catalog" class="loading_footer">
		<div id="sub_tab_filter"></div>
		<div id="global_alphabet"></div>
	</div>
</div>
</div>
<?php show_footer(); ?>
