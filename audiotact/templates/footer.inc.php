<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Footer
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
 
$web_path = Config::get('web_path'); ?>
		
		<div style="clear:both;"></div>
		<?php if (isset($_SESSION['userdata']['password'])) {?><span class="fatalerror"><?php echo _('Using Old Password Encryption, Please Reset your Password'); ?></span><?php } ?>
	</div> <!-- CLOSE CONTENT-->
</div> <!-- CLOSE MAINCONTAINER -->


<div id="footer">
	<ul>
		<li id="usb_name"><a href="">USB</a></li>
		<li id="usb_icon"><a href="<?php echo Config::get('web_path').'?action=umount'; ?>"><img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/eject_usbKey.png" /></a></li>
		<?php if (CATALOG == '1') { ?><li id="footer_name"><a href="<?php echo Config::get('web_path'); ?>">AUDIOTACT</a></li><?php } 
		else {?><li id="footer_name" style="border-right:none;"><a href="<?php echo Config::get('web_path'); ?>">AUDIOTACT</a></li><?php } ?>
 
		<li id="sound">
		<?php if (CATALOG == '1') { ?> 
			<img id="volume_on" class="active" src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/volume_button.png" />
			<img id="volume_off" style="display:none" src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/volume_mute.png" />
		<?php } ?>	
		</li>
		<li id="loginInfo">
			<?php if (($GLOBALS['user']->id)=="-1") { ?>
				<a href="<?php echo Config::get('web_path'); ?>/logout.php">
					<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/lock_button.png" />
				</a>
			<?php } else { ?>
				<a href="<?php echo Config::get('web_path'); ?>/logout.php">
					<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/unlock_button.png" />
				</a>
			<?php }?>
		</li>
		<li id="credits_link">
			<a href="<?php echo Config::get('web_path'); ?>/audiotact_info.php?tab=3">CRÉDITS</a>
		</li>
	</ul>
</div><!-- FOOTER -->	


<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function(){
	/* Jplayer */
	var myPlaylist = new jPlayerPlaylist({ jPlayer: "#jquery_jplayer_N", cssSelectorAncestor: "#jp_container_N"}, [], { 
			playlistOptions: { enableRemoveControls: true, autoPlay: true },
			swfPath: "<?php echo Config::get('web_path'); ?>/modules/html5/Jplayer.swf",
    		supplied: "mp3",
		    wmode:"window",
		    solution: "flash"
	});
	jQuery(".play").live('click',function(){
			var urlmp3 = (jQuery(this).find("span.item_to_play").text());	
			eval (urlmp3);
			return false;
	});
	jQuery("li#sound").live('click', function(){
		if (jQuery(this).children('img#volume_on').hasClass('active')) {
			jQuery("#jquery_jplayer_N").jPlayer("mute");
			jQuery(this).children('img#volume_on').removeClass('active').hide();
			jQuery(this).children('img#volume_off').addClass('active').show();
		} else if (jQuery(this).children('img#volume_off').hasClass('active')) {
			jQuery("#jquery_jplayer_N").jPlayer("unmute");
			jQuery(this).children('img#volume_off').removeClass('active').hide();
			jQuery(this).children('img#volume_on').addClass('active').show();
		}  
	});
	
	jQuery('#slider_volume').slider({
	    value : 80,
	    max: 100,
	    range: 'min',
	    animate: true,
	    orientation: "horizontal",
	    slide: function(event, ui) {
	        var volume = ui.value / 100;
	        jQuery("#jquery_jplayer_N").jPlayer("volume", volume);
	    }
	});
	
	


	/* Contrib mod player */
	jQuery(".audio").mb_miniPlayer({ swfPath:"<?php echo Config::get('web_path'); ?>/modules/html5/Jplayer.swf"});
	jQuery('.flash_player').height('0').width('0');
});
</script>

</body>
</html>
