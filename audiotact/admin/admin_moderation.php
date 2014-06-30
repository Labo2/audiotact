<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Modération - ADMIN
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

require '../lib/init.php';
if (!Access::check('interface','100')) { access_denied();exit; }
show_header();
$web_path = Config::get('web_path');
$currentUrl = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; $session_id = session_id();?>

<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function() {
	/* TIMER */
	var idleTime = 270000; /* Tps de la session - miliseconds*/ var redirectAfter = 30; /* + Tps avant la redirection */ var running = false;  var timer; 		
	if (jQuery("#dialog-session").length == 0) { var confirmDialog = jQuery('<div id="dialog-session" style="display:none">Votre session va expirer dans <span id="counter"></span> secondes.<br />Voulez-vous continuer ?</div>').appendTo('body');}
	confirmDialog.dialog({ title: 'Êtes-vous toujours là ?', autoOpen: false, closeOnEscape: false, draggable: false, resizable: false, height:160, width:500, modal: true, dialogClass: "lightbox_session", buttons: { "Oui, continuer": function() { confirmDialog.dialog( "close" );}}, close: function(event, ui) { clearInterval(timer); running = false; jQuery.ajax({ url: <?php echo ("'".$currentUrl."'") ;?>, async: false }); },}); 	
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
	
	//jQuery('.shout_columns').columnize({ columns: 2 });
	//jQuery('.contrib').mCustomScrollbar({scrollButtons:{enable:true},advanced:{updateOnContentResize: true}});
	contribScroll = new iScroll('contribScroll', { hScrollbar: false, vScrollbar: false});	
}); /* end */
</script>

<div id="logo" style="margin-top:27px;margin-bottom:37px;"><img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/logo_moderation.png" /></div>

<div id="main_tabs">
	<ul class="main_tabs_item">
		<li><a href="#ui-tabs-0">Albums et morceaux</a><hr class="active_sub"></li>
		<li><a href="show_shout_moderation.php">Commentaires</a><hr class="active_sub"></li>
		<li><a href="show_tag_moderation.php">Tags</a><hr class="active_sub"></li>
		<li><a href="show_playlist_moderation.php">Playlists</a><hr class="active_sub"></li>		
	</ul>
	
	<div id="ui-tabs-0">
		<div id="main_tabs_content">
			<div id="contrib_mod_wrap">	
				
			<?php require_once Config::get('prefix') . '/admin/show_contrib_moderation.php'; ?>
				
			</div>
		</div>
	</div>
</div>
<?php show_footer(); ?>
