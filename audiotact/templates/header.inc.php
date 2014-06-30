<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Header
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

if (INIT_LOADED != '1') { exit; }

$web_path = Config::get('web_path');
$htmllang = str_replace("_","-",Config::get('lang'));
$location = get_location();
$dir = is_rtl(Config::get('lang')) ? "rtl" : "ltr";
$themecss = Config::get('theme_path') . '/templates/';
$css = ($dir == 'rtl') ? $themecss.'default-rtl.css' : $themecss.'default.css';
$cssdir = Config::get('prefix').$themecss;
if(!is_file($cssdir.'default-rtl.css')) {
	$css = $themecss.'default.css';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $htmllang; ?>" lang="<?php echo $htmllang; ?>" dir="<?php echo $dir;?>">
<head>
	<link rel="shortcut icon" href="<?php echo $web_path; ?>/favicon.ico" />
	<link rel="search" type="application/opensearchdescription+xml" title="<?php echo scrub_out(Config::get('site_title')); ?>" href="<?php echo $web_path; ?>/search.php?action=descriptor" />
	<?php
	if (Config::get('use_rss')) { ?>
	<link rel="alternate" type="application/rss+xml" title="<?php echo _('Now Playing'); ?>" href="<?php echo $web_path; ?>/rss.php" />
	<link rel="alternate" type="application/rss+xml" title="<?php echo _('Recently Played'); ?>" href="<?php echo $web_path; ?>/rss.php?type=recently_played" />
	<?php } ?>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=<?php echo Config::get('site_charset'); ?>" />
	<title><?php echo scrub_out(Config::get('site_title')); ?> - <?php echo $location['title']; ?></title>
	
	<link rel="stylesheet" href="<?php echo $web_path; ?>/templates/base.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php echo $web_path; ?><?php echo $css; ?>" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php echo $web_path; ?><?php echo $themecss; ?>jquery.mCustomScrollbar.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php echo $web_path; ?>/templates/print.css" type="text/css" media="print" />
	<link rel="stylesheet" href="<?php echo $web_path; ?>/themes/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css" />
	
	<!-- SCRIPTS -->
	<script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/lib/javascript/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/lib/javascript/jquery-ui-1.9.2.min.js"></script>
	<script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/lib/javascript/plugins.js"></script>
	
	<!-- MODULES -->
	<!-- Jplayer -->
	<link rel="stylesheet" type="text/css" href="<?php echo Config::get('web_path'); ?>/modules/html5/skin/blue.monday/jplayer.blue.monday.css"> 
	<script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/modules/html5/jquery.jplayer.js"></script>
	<script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/modules/html5/jplayer.playlist.js"></script> 
	
	<!-- Mini Audio Player-->
	<script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/modules/html5/jquery.mb.miniPlayer.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo Config::get('web_path'); ?>/modules/html5/css/miniplayer.css">  
	
	<!-- Session timer -->
	<script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/modules/session_timer/jquery.idletimer.js"></script>
	<script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/modules/session_timer/jquery.idletimeout.js"></script>
	
	<!-- jQuery File Uploader -->
	<link href="<?php echo $web_path; ?>/modules/file_uploader/css/fileUploader.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo $web_path; ?>/modules/file_uploader/js/jquery.fileUploader.js" type="text/javascript"></script>
	
	<!-- Keyboard -->
	<link rel="stylesheet" type="text/css" href="<?php echo Config::get('web_path'); ?>/modules/keyboard/css/keyboard.css">  
	<script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/modules/keyboard/js/jquery.keyboard.js"></script>
	<script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/modules/keyboard/layouts/french.js" charset="utf-8"></script>
	
	<!--MAIN SCRIPT-->
	<script src="<?php echo $web_path; ?>/lib/javascript/script.js" language="javascript" type="text/javascript"></script>
	     
	<!-- Images background -->
	<script type="text/javascript">
	jQuery.noConflict();
	jQuery(document).ready(function(){
		<?php  $sql = "SELECT * FROM `background_image` ;"; $result = mysql_query($sql) or exit(mysql_error()); ?>
		var images = [ <?php while ($data = mysql_fetch_assoc($result)) { $img =  $data['img_path']; echo ("'".$img."',"); } ?>];
		var url = '<?php echo ($web_path.'/images_background/'); ?>';
		var img = images[Math.floor(Math.random() * images.length)] ;
		var path_img = (url+img) ;
		jQuery('body').css({'background-image': 'url("'+path_img+'")','background-repeat': 'no-repeat'});
	});
	</script>
	
</head>


<body>
	<script src="<?php echo $web_path; ?>/modules/prototype/prototype.js" language="javascript" type="text/javascript"></script>
	<script src="<?php echo $web_path; ?>/lib/javascript/base.js" language="javascript" type="text/javascript"></script>
	<script src="<?php echo $web_path; ?>/lib/javascript/ajax.js" language="javascript" type="text/javascript"></script>
	<span class="ajax_loader"></span>
	
	<!-- rfc3514 implementation -->
	<div id="rfc3514" style="display:none;">0x0</div>
	
	<!-- OPEN MAINCONTAINER -->
	<div id="maincontainer">
		<!-- HEADER -->
		<div id="header">
			<h1 id="headerlogo">
			  <a href="<?php echo Config::get('web_path'); ?>">
			    <img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/home.png" title="<?php echo Config::get('site_title'); ?>" alt="<?php echo Config::get('site_title'); ?>" />
			  </a>
			</h1>	
			<!-- player -->
			<?php if (CATALOG == '1') { ?> 
			<div id="player"><?php require_once Config::get('prefix') . '/templates/show_player.inc.php'; ?></div>
			<?php } ?>
			<!-- Box search -->
			<div id="headerbox">
				<?php if (CATALOG == '1') { ?> 
				<a href="<?php echo $web_path. '/templates/show_search_bar.inc.php'; ?>" class="search">
			    	<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/search.png" title="search" alt="search" />
			 	 </a>
				<?php } else { ?>
				
				<a href="<?php echo $web_path. '/catalog_browse.php?search=true'; ?>" class="search_home">
			    	<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/search.png" title="search" alt="search" />
			 	</a>
			 
				<?php }?>
				
			</div> <!-- End headerbox -->
		</div><!-- End header -->
		
		<div id="sidebar" style="position:fixed;z-index:100"><?php require_once Config::get('prefix') . '/templates/sidebar.inc.php'; ?></div><!-- End sidebar -->
		
		<!-- Tiny little iframe, used to cheat the system -->
		<div id="ajax-loading">Loading . . .</div>
		<iframe name="util_iframe" id="util_iframe" style="display:none;" src="<?php echo Config::get('web_path'); ?>/util.php"></iframe>
	
		<!-- OPEN CONTENT -->
		<div id="content">
			<?php if (Config::get('int_config_version') != Config::get('config_version') AND $GLOBALS['user']->has_access(100)) { ?>
			<div class="fatalerror">
				<?php echo _('Error Config File Out of Date'); ?>
				<a href="<?php echo Config::get('web_path'); ?>/admin/system.php?action=generate_config"><?php echo _('Generate New Config'); ?></a>
			</div>
			<?php } ?>
