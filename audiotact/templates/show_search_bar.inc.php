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
$web_path = Config::get('web_path');?>

<div id="sb_Subsearch">
	<form id="search_form" name="search" method="post" action="<?php echo $web_path; ?>/catalog_browse_search.php?type=song" enctype="multipart/form-data" style="Display:inline">
        <input class="keyboard" placeholder="CHERCHER" type="text" name="rule_1_input" id="searchString"/>
        <input type="hidden" name="action" value="search" />
		<input type="hidden" name="rule_1_operator" value="0" />
        <input type="hidden" name="object_type" value="song" />
        <input class="button ok_submit" type="submit" value="OK" id="searchBtn" />
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
