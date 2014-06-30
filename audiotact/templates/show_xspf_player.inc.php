<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show XSPF Player
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

?>
<html>
<head><title>Ampache XSPF Player</title></head>
<body style="margin:0px; padding:0px; border:0px; background-color: #000000">
<script language=JavaScript>
<!--
//Disable right mouse click Script to hide the source url for the flash player it prevents ripping music a bit.
//When used together with locked songs this will help just a bit more.
//function clickIE4(){
//if (event.button==2){
//return false;
//}
//}

//function clickNS4(e){
//if (document.layers||document.getElementById&&!document.all){
//if (e.which==2||e.which==3){
//return false;
//}
//}
//}

//if (document.layers){
//document.captureEvents(Event.MOUSEDOWN);
//document.onmousedown=clickNS4;
//}
//else if (document.all&&!document.getElementById){
//document.onmousedown=clickIE4;
//}
//document.oncontextmenu=new Function("return false")

// -->
</script>
<div id="mp3player">
<?php
if (file_exists(Config::get('prefix')."/modules/flash/xspf_jukebox.swf")) {
    $player_url = sprintf("%s/modules/flash/xspf_jukebox.swf?autoplay=true&repeat_playlist=true&crossFade=false&shuffle=false&skin_url=%s/modules/flash/Original/&playlist_url=%s",Config::get('web_path'),Config::get('web_path'),$play_url);
?>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="400" height="170" id="xspf_player" align="middle">
	<param name="pluginspage" value="http://www.macromedia.com/go/getflashplayer" />
	<param name="allowScriptAccess" value="sameDomain" />
	<param name="movie" value="<?php echo $player_url; ?>" />
	<param name="quality" value="high" />
	<param name="bgcolor" value="#ffffff" />
	<param name="type"    value="application/x-shockwave-flash" />
	<param name="width"   value="400" />
	<param name="height"  value="170" />
	<param name="name"    value="xspf_player" />
	<param name="align"   value="middle" />
	<embed src="<?php echo $player_url; ?>" quality="high" bgcolor="#ffffff" width="400" height="170" name="xspf_player" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
<?php } else { ?>
<p style="color:#ffffff;">Debian Policy requires the removal of the XSPF Flash Player, please see /usr/share/doc/ampache/README.Debian.gz for details and a work around.</p>
<?php } ?>
</div>

</body>
</html>
