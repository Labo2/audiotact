<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Search Bar
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
$web_path = Config::get('web_path'); 

if ($_REQUEST['type'] == 'album') {
	$album = new Album($_REQUEST['id']);
	$album->format();
	$name = $album->name;
	$action = Config::get('web_path')."/admin/admin_lightbox_item.php?action=update_album_name" ;	
} else if ($_REQUEST['type'] == 'artist') {
	$artist = new Artist($_REQUEST['id']);
	$artist->format();
	$name = $artist->f_full_name;
	$action = Config::get('web_path')."/admin/admin_lightbox_item.php?action=update_artist_name" ;	
} else if ($_REQUEST['type'] == 'web') {
	$artist = new Artist($_REQUEST['id']);
	$artist->format();
	$name = $artist->website;
	$action = Config::get('web_path')."/admin/admin_lightbox_item.php?action=update_artist_web" ;	
	$web = '1';
}
?>

<div id="sb_Subsearch">	
	<form id="update_name" name="search" method="post" action="<?php echo $action; ?>" enctype="multipart/form-data" style="display:inline">
        <input class="keyboard" type="text" name="name" id="name" value="<?php echo $name; ?>"/>
      	<input id="id" type="hidden" name="id" value="<?php echo $_REQUEST['id'] ; ?>" />
      	<input class="web" type="hidden" name="web" value="<?php echo $web ; ?>" />
        <input class="button ok_submit" type="submit" value="Mettre à jour" id="update_name" />
   	</form>
   	
</div>

<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function(){
	jQuery('.keyboard').keyboard({
		layout       : 'french-azerty-2',
  		customLayout : { default: ['{cancel}'] },
		position : {of : jQuery('#lightbox_search'),},
		usePreview: false,
  	});
	jQuery('.keyboard').getkeyboard().reveal();		
});
</script>
