<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Install Header
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
$prefix = realpath(dirname(__FILE__). "/../");
$lang = "fr_FR";
$htmllang = $lang;
$charset = "UTF-8";
Config::set('lang',$htmllang,'1');
Config::set('site_charset', $charset, '1');
load_gettext();
$prefix = realpath(dirname(__FILE__). "");
?>
<?php if (!defined('INSTALL')) { exit; } ?>
<?php $results = 0; ?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $htmllang; ?>" lang="<?php echo $htmllang; ?>">
<head>
	<title>Audiotact :: Installation</title>
	<link rel="stylesheet" href="templates/install.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="themes/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; Charset=<?php echo $charset; ?>" />
	<script type="text/javascript" src="lib/javascript/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="lib/javascript/jquery-ui-1.9.2.min.js"></script>
	
	<!-- KEYBOARD -->
	<link rel="stylesheet" type="text/css" href="modules/keyboard/css/keyboard.css">  
	<script type="text/javascript" src="modules/keyboard/js/jquery.keyboard.js"></script>
	<script type="text/javascript" src="modules/keyboard/layouts/french.js" charset="utf-8"></script>
	
	<script type="text/javascript">
	jQuery.noConflict();
	jQuery(document).ready(function(){
		var maintabs = jQuery( "#main_tabs" ).tabs({
						create: function( event, ui ) {},
						load: function( event, ui ) {},
						ajaxOptions: { error: function( xhr, status, index, anchor ) { jQuery( anchor.hash ).html("Erreur lors du chargement de la page" );}}
		});
		jQuery("#main_tabs").tabs("option", "disabled", [1,2,3] );
	
		/* Ajax index catalog */
		jQuery('.index_cat').live('click', function(){
			if (jQuery("#lightbox_catalog").length == 0) {
	      		var ajaxDialog = jQuery('<div id="lightbox_catalog" style="display:hidden" title="Indexation du catalogue"></div>').appendTo('body');     	
			}
		    ajaxDialog.dialog({
		      	autoOpen: false, width:860,height:240,dialogClass: "background_box",modal:true,resizable: false,draggable: false, 
		      	show: {effect: "clip", easing:"swing", duration:750 },
				hide: {effect: "clip"},
				close: function(event, ui) { ajaxDialog.remove();}
		   });
		   ajaxDialog.dialog("open");
		   jQuery('.loader').show();
		   var action = jQuery(this).attr('href');
		   jQuery.post(action, function(data) {
				jQuery('#lightbox_catalog').html(data);
				jQuery('.loader').hide();
			});		
			return false;
		});
	});
	</script>
</head>

<body>
	<script src="modules/prototype/prototype.js" language="javascript" type="text/javascript"></script>
	<script src="lib/javascript/base.js" language="javascript" type="text/javascript"></script>
	<script src="lib/javascript/ajax.js" language="javascript" type="text/javascript"></script>
	<span class="loader"></span>
	
	<div id="maincontainer">
		<div id="header"></div>
			<div id="content">
				<div id="logo-home"><img src="images/logo_home.png" /></div><!--<h1><?php echo ('Audiotact :: Installation'); ?></h1>-->
		
				
		


