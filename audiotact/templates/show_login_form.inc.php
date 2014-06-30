<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Login Form
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

/* Check and see if their remember me is the same or lower then local
 * if so disable the checkbox
 */
if (Config::get('session_length') >= Config::get('remember_length')) {
	$remember_disabled = 'disabled="disabled"';
}
$htmllang = str_replace("_","-",Config::get('lang'));
is_rtl(Config::get('lang')) ? $dir = 'rtl' : $dir = 'ltr';

$web_path = Config::get('web_path');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $htmllang; ?>" lang="<?php echo $htmllang; ?>" dir="<?php echo $dir; ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo Config::get('site_charset'); ?>" />
<link rel="shortcut icon" href="<?php echo Config::get('web_path'); ?>/favicon.ico" />
<link rel="stylesheet" href="<?php echo Config::get('web_path'); ?>/templates/print.css" type="text/css" media="print" />
<link rel="stylesheet" href="<?php echo Config::get('web_path'); ?><?php echo Config::get('theme_path'); ?>/templates/default.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo $web_path; ?>/themes/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css" />
<title> <?php echo scrub_out(Config::get('site_title')); ?> </title>
<script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/lib/javascript/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/lib/javascript/jquery-ui-1.9.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo Config::get('web_path'); ?>/modules/keyboard/css/keyboard.css">  
<script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/modules/keyboard/js/jquery.keyboard.js"></script>
<script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/modules/keyboard/layouts/french.js" charset="utf-8"></script>
<script type="text/javascript" language="javascript">
//function focus(){ document.login.username.focus(); }
</script>
	
	<script type="text/javascript">
	jQuery.noConflict();
	jQuery(document).ready(function(){
		<?php  $sql = "SELECT * FROM `background_image` ;"; $result = mysql_query($sql) or exit(mysql_error()); ?>
		var images = [ <?php while ($data = mysql_fetch_assoc($result)) { $img =  $data['img_path']; echo ("'".$img."',"); } ?>];
		var url = '<?php echo ($web_path.'/images_background/'); ?>';
		var img = images[Math.floor(Math.random() * images.length)] ;
		var path_img = (url+img) ;
		jQuery('body').css({'background-image': 'url("'+path_img+'")','background-repeat': 'no-repeat'});
		
		jQuery('input').keyboard({
		layout       : 'french-azerty-2',
  		customLayout : { default: ['{cancel}'] },
  		//usePreview : true,
  		position : {
 			of : null,
  			my : 'center center',
 			at : 'center center',
 			at2: 'center top' 
		}
  		});
  		
  		
  jQuery('a.reset_mdp').live('click', function() {
	var action = jQuery(this).attr('href');
	if (jQuery("#dialog-confirm").length == 0) {
      	 var confirmDialog = jQuery('<div id="dialog-confirm" style="display:hidden">Mettre à jour le mot de passe ?<br />Cette action est définitive.</div>').appendTo('body');     	
	}
	confirmDialog.dialog({
		resizable: false, height:160, width:300, modal: true, dialogClass: "lightbox_confirm",
		close: function(event, ui) { confirmDialog.remove();},
		buttons: {
			"Annuler": function() { confirmDialog.dialog( "close" ); },
			"Valider": function() { jQuery.post(action, function(data) {
				jQuery('.reset_password a').remove();
				jQuery('.reset_password').html("Un nouveau mot de passe a été généré.");
			}); confirmDialog.dialog( "close" );}
		}
	});
	return false;
});

  		
  		
	});
	</script>

</head>

<body id="loginPage" onload="">
<div id="maincontainer">
	
	<div id="header"><!-- This is the header -->
		<h1 id="headerlogo">
		  <a href="<?php echo Config::get('web_path'); ?>">
		    <img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/home.png" title="<?php echo Config::get('site_title'); ?>" alt="<?php echo Config::get('site_title'); ?>" />
		  </a>
		</h1>
		
		
		<!-- Box search -->
		<div id="headerbox">
			<?php /*show_box_top('','box box_headerbox');*/ ?>
			<a href="" class="search">
		    <img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/search.png" title="search" alt="search" />
		  </a>
		</div> <!-- End headerbox -->
	</div><!-- End header -->
	
	
	<div id="logo" style="margin-top:50px;margin-bottom:150px;"><img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/logo_admin.png" /></div>
	
	<div id="loginbox">
		<h2>Vous êtes sur le point d'entrer dans le panneau d'administration</h2>
		<form name="login" method="post" enctype="multipart/form-data" action="<?php echo Config::get('web_path'); ?>/login.php">
			<div class="loginfield" id="usernamefield">
				<input class="text_input" type="text" id="username" name="username" value="<?php echo  $_REQUEST['username']; ; ?>" placeholder="Veuillez entrer votre nom d'utilisateur"/>
      		</div>
			<div class="loginfield" id="passwordfield">
  				<input class="text_input" type="password" id="password" name="password" value="" placeholder="Mot de passe" />
     		 </div>
			<?php echo Config::get('login_message'); ?>
			
			
	    	<div class="formValidation">
	    		<input class="button" id="loginbutton" type="submit" value="<?php echo _('Login'); ?>" />
	  			<input type="hidden" name="referrer" value="<?php echo scrub_out($_SERVER['HTTP_REFERRER']); ?>" />
	  			<input type="hidden" name="action" value="login" />
		    </div>
		</form>
		<div class="reset_password"><a class="reset_mdp" id="lostpasswordbutton" href="<?php echo Config::get('web_path'); ?>/lostpassword.php">Mot de passe oublié ?</a><?php Error::display('general'); ?></div>
		
	</div><!--#loginbox-->
</div><!--#maincontainer-->

<?php if (@is_readable(Config::get('prefix') . '/config/motd.php')) {?>
	<div id="motd">
	<?php
        show_box_top(_('Message of the Day'));
        include Config::get('prefix') . '/config/motd.php';
        show_box_bottom();
	?>
	</div>
<?php } ?>

<div id="footer">
	<ul>
		<li id="usb_name"><a href="">USB</a></li>
		<li id="usb_icon"><a href="<?php echo Config::get('web_path'); ?>"><img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/eject_usbKey.png" /></a></li>
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
</body>
</html>

