<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Browse
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
 * Browse Page
 * This page shows the browse menu, which allows you to browse by many different
 * fields including artist, album, and catalog.
 *
 * This page also handles the actual browse action
 */

/* Base Require */
require_once 'lib/init.php';
$web_path = Config::get('web_path');
session_start();
show_header();
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
});
</script>

<div id="logo" style="margin-top:50px;margin-bottom:53px;"><img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/logo_contrib.png" /></div>
<div id="main_tabs" >
	<ul class="main_tabs_item">
		<li><a href="<?php echo $web_path . '/templates/show_contrib_step1.inc.php' ; ?>">1. Upload</a><hr class="active_sub"></li>
		<li><a href="#ui-tabs-2">2. Informez (1/2)</a><hr class="active_sub"></li>
		<li><a href="#ui-tabs-3">3. Informez (2/2)</a><hr class="active_sub"></li>
		<li><a href="#ui-tabs-4">4. Validation</a><hr class="active_sub"></li>
	</ul>

	<div id="ui-tabs-2"></div>
  	<div id="ui-tabs-3"></div>
   	<div id="ui-tabs-4"></div>	
</div><!--main_tabs-->

<?php show_footer(); ?>
