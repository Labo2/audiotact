<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Install
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

// Set the Error level manualy... I'm to lazy to fix notices
error_reporting(E_ALL ^ E_NOTICE);

require_once 'lib/general.lib.php';
require_once 'lib/class/config.class.php';
require_once 'lib/class/error.class.php';
require_once 'lib/class/vauth.class.php';
require_once 'lib/class/database_object.abstract.php';
require_once 'lib/class/preference.class.php';
require_once 'lib/class/access.class.php';
require_once 'lib/ui.lib.php';
require_once 'lib/log.lib.php';
require_once 'modules/horde/Browser.php';
require_once 'lib/install.php';
require_once 'lib/debug.lib.php';
require_once 'lib/gettext.php';

if ($_SERVER['HTTPS'] == 'on') { $http_type = "https://"; }
else { $http_type = "http://"; }

$prefix = dirname(__FILE__);
Config::set('prefix',$prefix,'1');
$configfile = "$prefix/config/ampache.cfg.php";

set_error_handler('ampache_error_handler');

/* First things first we must be sure that they actually still need to
   install ampache
*/
if (!install_check_status($configfile)) {
	echo ('error page');
	$redirect_url = "login.php";
	require_once Config::get('prefix') . '/templates/error_page.inc.php';
	exit;
}

define('INSTALL','1');
/**
 * @ignore
 */
define('INIT_LOADED','1');

/* Clean up incomming variables */
$web_path = scrub_in($_REQUEST['web_path']);
$username = scrub_in($_REQUEST['local_username']);
$password = $_REQUEST['local_pass'];
$hostname = scrub_in($_REQUEST['local_host']);
$database = scrub_in($_REQUEST['local_db']);
if ($_SERVER['HTTPS'] == 'on') { $http_type = "https://"; }
else { $http_type = "http://"; }

// Correct potential \ or / in the dirname
$safe_dirname = rtrim(dirname($_SERVER['PHP_SELF']),"/\\"); 

define('WEB_PATH',$http_type . $_SERVER['HTTP_HOST'] . $safe_dirname . '/' . basename($_SERVER['PHP_SELF']));
define('WEB_ROOT',$http_type . $_SERVER['HTTP_HOST'] . $safe_dirname);

unset($safe_dirname); 

/* Header*/
require $prefix . '/templates/install_header.inc.php';
?>

<div id="main_tabs" >
	<ul class="main_tabs_item">
		<li><a href="#ui-tabs-1">Étape 1</a><hr class="active_sub"></li>
		<li><a href="#ui-tabs-2">Étape 2</a><hr class="active_sub"></li>
		<li><a href="#ui-tabs-3">Étape 3</a><hr class="active_sub"></li>
		<li><a href="#ui-tabs-4">Validation</a><hr class="active_sub"></li>
	</ul>

		<?php
		/* Catch the Current Action */
		switch ($_REQUEST['action']) {
			
		   case 'init': /* Second step of installation */
				// Get the language
				$htmllang = $_POST['htmllang'];
				// Set the lang in the conf array
				Config::set('lang',$htmllang,'1');
				// We need the charset for the different languages
				$charsets = array(
						  'ar_SA' => 'UTF-8',
						  'de_DE' => 'ISO-8859-15',
						  'en_US' => 'iso-8859-1',
						  'cs_CZ' => 'UTF-8',
						  'ja_JP' => 'UTF-8',
						  'en_GB' => 'UTF-8',
						  'es_ES' => 'iso-8859-1',
						  'fr_FR' => 'iso-8859-1',
						  'it_IT' => 'UTF-8',
						  'nl_NL' => 'ISO-8859-15',
						  'tr_TR' => 'iso-8859-9',
						  'zh_CN' => 'GBK');
				$charset = $charsets[$_POST['htmllang']];
		 	    Config::set('site_charset',$charsets[$_POST['htmllang']],'1');
				load_gettext(); ?>
					
					<script type="text/javascript">
					jQuery.noConflict();
					jQuery(document).ready(function(){
						jQuery("#main_tabs").tabs( "enable" );
						jQuery("#main_tabs").tabs( 'select', 1);
						jQuery("#main_tabs").tabs("option", "disabled", [0,2,3] );
					});
					</script>
					<div id="ui-tabs-2"><?php require_once 'templates/show_install_step_2.inc.php'; ?></div>
					<?php
			break;
			
			case 'create_db':
				/* Get the variables for the language */
				$htmllang = $_REQUEST['htmllang'];
				$charset  = $_REQUEST['charset'];
		
				// Set the lang in the conf array
				Config::set('lang', $htmllang,'1');
				Config::set('site_charset', $charset, '1');
				load_gettext();
		
				if (!install_insert_db($username,$password,$hostname,$database)) {
					//require_once 'templates/show_install.inc.php';
					?>
					<script type="text/javascript">
					jQuery.noConflict();
					jQuery(document).ready(function(){
						jQuery("#main_tabs").tabs( "enable" );
						jQuery("#main_tabs").tabs( 'select', 1);
						jQuery("#main_tabs").tabs("option", "disabled", [0,2,3] );
					});
					</script>
					<div id="ui-tabs-2"><?php require_once 'templates/show_install_step_2.inc.php'; ?></div>
					<?php
					break;
				}
		
				// Now that it's inserted save the lang preference
				Preference::update('lang','-1',$htmllang);
		
		        /* Attempt to Guess the Web_path */
				$web_path = dirname($_SERVER['PHP_SELF']);
				$web_path = rtrim($web_path,"\/");
		
				/* Get the variables for the language */
				$htmllang = $_REQUEST['htmllang'];
				$charset  = $_REQUEST['charset'];
		
				// Set the lang in the conf array
				Config::set('lang',$htmllang,'1');
		
				// We need the charset for the different languages
				$charsets = array(
						  'ar_SA' => 'UTF-8',
						  'de_DE' => 'ISO-8859-15',
						  'en_US' => 'iso-8859-1',
						  'en_GB' => 'UTF-8',
						  'ja_JP' => 'UTF-8',
						  'es_ES' => 'iso-8859-1',
						  'fr_FR' => 'iso-8859-1',
						  'el_GR' => 'el_GR.utf-8',
						  'it_IT' => 'UTF-8',
						  'nl_NL' => 'ISO-8859-15',
						  'tr_TR' => 'iso-8859-9',
						  'zh_CN' => 'GBK');
				$charset = $charsets[$_REQUEST['htmllang']];
				Config::set('site_charset',$charsets[$_REQUEST['htmllang']],'1');
				load_gettext(); ?>
		      		
		      		<script type="text/javascript">
					jQuery.noConflict();
					jQuery(document).ready(function(){
						jQuery("#main_tabs").tabs( "enable" );
						jQuery("#main_tabs").tabs( 'select', 2);
						jQuery("#main_tabs").tabs("option", "disabled", [0,1,3] );
					});
					</script>
					<div id="ui-tabs-3"><?php require_once 'templates/show_install_step_3.inc.php'; ?></div>
					<?php
			break;
			   
			case 'create_config':
		 
				$htmllang = $_REQUEST['htmllang'];
				$charset  = $_REQUEST['charset'];
				// Test and make sure that the values they give us actually work
				if (!check_database($hostname,$username,$password)) {
					Error::add('config',_('Error: Unable to make Database Connection') . mysql_error());
				}
		
				if (!Error::occurred()) {
					$created_config = install_create_config($web_path,$username,$password,$hostname,$database);
				}
				
				// set admin username and password
				if ($created_config) {
					$admin_user = $_POST['admin_username'];
					$password = $_POST['admin_pass'];
					$password2 = $_POST['admin_pass2'];
					install_update_account($admin_user,$password,$password2);
					
					// CATALOGS
					$path  = $_SERVER['SCRIPT_FILENAME'];
					$path = explode("/", $path);
					
					// admin catalog
					$admin_catalog_path = $_SERVER['DOCUMENT_ROOT'].'/'.$path[3].'/Catalogue';
					$dir = Config::get('prefix') .'/Catalogue';
		      		if (!file_exists($dir)){ mkdir("$dir", 0755);}
		      		$array_cat_admin = array('id'=>'1', 'name'=> 'Admin catalog', 'path'=>$admin_catalog_path, 'type'=>'local', 'rename_pattern' => '%a - %T - %t', 'sort_pattern' => '%a/%A' );
		      		
		      		if (Catalog::get_from_path($array_cat_admin['path'])) { Error::add('general',_('Error: Defined Path is inside an existing catalog'));}
		      		if (!Error::occurred()) {
		      			$catalog_id = Catalog::Create_first($array_cat_admin);
		      		}
		      		
		      		// user catalog
					$user_catalog_path = $_SERVER['DOCUMENT_ROOT'].'/'.$path[3].'/Catalogue_Utilisateur'; /* $path[3] */ 
					
					$dir_user = Config::get('prefix') .'/Catalogue_Utilisateur';
		      		if (!file_exists($dir_user)){ mkdir("$dir_user", 0755);}
		 			$array_cat_user = array('id'=>'2', 'name'=> 'User catalog', 'path'=>$user_catalog_path, 'type'=>'local', 'rename_pattern' => '%a - %T - %t', 'sort_pattern' => '%a/%A' );
				
					if (Catalog::get_from_path($array_cat_user['path'])) { Error::add('general',_('Error: Defined Path is inside an existing catalog'));}
		      		if (!Error::occurred()) {
		      			$user_catalog_id = Catalog::Create_first($array_cat_user);
		      		}?>
		      		
		      		<script type="text/javascript">
					jQuery.noConflict();
					jQuery(document).ready(function(){
						jQuery("#main_tabs").tabs( "enable" );
						jQuery("#main_tabs").tabs( 'select', 3);
						jQuery("#main_tabs").tabs("option", "disabled", [0,1,2] );
					});
					</script>
					<div id="ui-tabs-4"><?php require_once 'templates/show_install_step_4.inc.php'; ?></div>
				<?php } /* end if created_config*/
			break;
			
		    default:  /* First step of installation */ ?>
				<div id="ui-tabs-1"><?php require_once 'templates/show_install_step_1.inc.php'; ?></div>
				<?php
			break;
		} // end action switch
		?>
		</div><!--main_tabs-->
	</div> <!-- #content-->
</div><!--maincontainer-->
<div id="footer"><p>Ampache Installation. For the love of Music.</p></div>
</body>
</html>
