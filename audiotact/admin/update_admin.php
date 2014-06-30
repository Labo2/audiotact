<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Preference
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

switch($_REQUEST['action']) {
	case 'update_mdp':
		$user_id	= scrub_in($_POST['user_id']);
		//$username 	= scrub_in($_POST['username']);
		$fullname 	= scrub_in($_POST['fullname']);
		$email 		= scrub_in($_POST['email']);
		$access 	= scrub_in($_POST['access']);
		$pass1 		= $_POST['password_1'];
		$pass2 		= $_POST['password_2'];

		/* Setup the temp user */
		$client = new User($user_id);

		/* Verify Input 
		if (empty($username)) {
			Error::add('username',_("Error Username Required"));
		}*/
		if ($pass1 !== $pass2 && !empty($pass1)) {
			Error::add('password',_("Error Passwords don't match"));
		}

		/* If we've got an error then break! */
		if (Error::occurred()) {
			$_REQUEST['action'] = 'show_edit';
			break;
		} // if we've had an oops!

		if ($access != $client->access) {
			$client->update_access($access);
		}
		if ($email != $client->email) {
			$client->update_email($email);
		}
		/*if ($username != $client->username) {
			$client->update_username($username);
		}*/
		if ($fullname != $client->fullname) {
			$client->update_fullname($fullname);
		}
		if ($pass1 == $pass2 && strlen($pass1)) {
			$client->update_password($pass1);
		}
	break;
	
	case 'update_login':
		$user_id	= scrub_in($_POST['user_id']);
		$username 	= scrub_in($_POST['username']);
		

		/* Setup the temp user */
		$client = new User($user_id);

		/* Verify Input */
		if (empty($username)) {
			Error::add('username',_("Error Username Required"));
		}
		
		/* If we've got an error then break! */
		if (Error::occurred()) {
			$_REQUEST['action'] = 'show_edit';
			break;
		} // if we've had an oops!

		
		if ($username != $client->username) {
			$client->update_username($username);
		}		
	break;

	
	case 'update_web_title':
		if ($_POST['method'] == 'admin' && !Access::check('interface','100')) {
			access_denied();
			exit;
		}

		if ($_POST['method'] == 'admin') {
			$user_id = '-1';
			$fullname = _('Server');
			$_REQUEST['action'] = 'admin';
		}
		else {
			$user_id = $GLOBALS['user']->id;
			$fullname = $GLOBALS['user']->fullname;
		}

		update_preferences($user_id);
		Preference::init();
	break;
	
	case 'update_catalog':
		if (!Access::check('interface','100')) { access_denied(); exit;}
		
		if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {
			
			$file = $_FILES['zip_update_cat']['tmp_name'];
			if ($file) { 
				$archive = new PclZip($file);
	 			$list  =  $archive->extract(PCLZIP_OPT_PATH, "../Catalogue",
	        	PCLZIP_OPT_REMOVE_PATH, "__LINUXX");
				if ($list == 0) { echo "ERROR : ".$archive->errorInfo(true);} 
				
				if ($_REQUEST['catalogs'] ) {
					foreach ($_REQUEST['catalogs'] as $catalog_id) {
						$catalog = new Catalog($catalog_id);
						$catalog->add_to_catalog_admin();
					}
				}
			} else {
				if ($_REQUEST['catalogs'] ) {
					foreach ($_REQUEST['catalogs'] as $catalog_id) {
						$catalog = new Catalog($catalog_id);
						$catalog->add_to_catalog_admin();
					}
				}
			}
		} 
	break;
	
	case 'update_infobox':
		if (!Access::check('interface','100')) { access_denied(); exit; }
		$id = $_POST['id'];
		$content = mysql_real_escape_string($_POST['content']);
		audiotact_info::update_informations($id,$content);
		echo $content ;
	break;
	
	case 'upload_bg':
		$bg = new bg_upload();
		$bg->setDestination('../images_background/');
		$bg->receive();
	break;
	
	case 'select_favorit_type':
		$value = $_POST['show_selected'];
		$sql = "UPDATE `preference` SET `value`='$value' WHERE `name`='favorits_type'";
		$db_results = Dba::write($sql);
		$selected_object = Favorits::get_selected_object();
		foreach ($selected_object as $key) {
			$selected_object_type = $key;
		}
	break;
	
	case 'update_text_comment':
		$shout_id = $_POST['shout_id'];
		$text = $_POST['shouttext'];
		$sql = "UPDATE `user_shout` SET `text`='$text' WHERE `id`='$shout_id'";
		$db_results = Dba::write($sql);
	break;
	
	case 'flip_state_delete':
		if (!Access::check('interface','100')) {
			debug_event('DENIED',$GLOBALS['user']->username . ' attempted to change the state of a song','1');
			exit;
		}
		$tag = new Tag($_REQUEST['tag_id']);
		$tag_state = $tag->tag_mod ? '0' : '2';
		$tag->to_delete_tag($tag_state,$tag->id);
		$tag-> tag_mod = $tag_state;
		$tag->format();
		$id = 'tag_flip_state_' . $tag->id;
		$state = $tag->tag_mod ? 'active_delete' : 'inactive_delete';
		echo $state;
	break;
	
	/* Contrib moderation*/
	case 'delete_submit':
		function rrmdir($dir) { 
			if (is_dir($dir)) { $objects = scandir($dir); 
			     foreach ($objects as $object) { 
			       if ($object != "." && $object != "..") { 
			         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
			       } 
			     } 
			     reset($objects); 
			     rmdir($dir); 
			 } 
		} 
		$id_submit = $_REQUEST['id']; $path = ('../tmp_user_catalog/'.$id_submit.'/');
		rrmdir ($path);
		$sql = "DELETE FROM `tmp_submit` WHERE `id` = $id_submit ";
		$db_results = Dba::write($sql);
		$sql = "DELETE FROM `tmp_submit_data` WHERE `tmp_submit` = $id_submit ";
		$db_results = Dba::write($sql);
		$sql = "SELECT  `id` FROM `tmp_submit`" ;
		$result_el = mysql_query($sql) or exit(mysql_error());
		if (!($r = Dba::fetch_assoc($result_el))) {
			$sql = "DELETE FROM `tmp_submit_artist` ";
			$db_results = Dba::write($sql);
			$sql = "DELETE FROM `tmp_submit_album`";
			$db_results = Dba::write($sql);
		} 

	break;
	
	case 'add_submit':
		$id_submit = $_REQUEST['id'];
		$path = Config::get('prefix') .'/Catalogue_Utilisateur';
	    if (!file_exists($path)){ mkdir("$path", 0755);}	
	    
		/* RECHERCHE ARTIST */
		$sql = "SELECT artist_id, artist 
		FROM `tmp_submit_data` 
		INNER JOIN `tmp_submit_artist` ON `tmp_submit_data`.artist_id = tmp_submit_artist.id
		WHERE `tmp_submit`='$id_submit'
		GROUP BY artist ;";
		$result_submit_artist = mysql_query($sql) or exit(mysql_error());		
		while ($data_submit_artist = mysql_fetch_assoc($result_submit_artist)) {
			$artist = $data_submit_artist['artist'];
			$artist_id = $data_submit_artist['artist_id'];
			
			$artist_path = Config::get('prefix') .'/Catalogue_Utilisateur/'.$artist ;
			if (!file_exists($artist_path)){ mkdir("$artist_path", 0755);}
			
			/* RECHERCHE Album associés */
			$sql = "SELECT album, album_id 
			FROM `tmp_submit_data` 
			INNER JOIN `tmp_submit_album` ON `tmp_submit_data`.album_id = tmp_submit_album.id
			WHERE `tmp_submit`='$id_submit' AND `artist_id`='$artist_id'
			GROUP BY album";
			$result_submit_album = mysql_query($sql) or exit(mysql_error());		
			while ($data_submit_album = mysql_fetch_assoc($result_submit_album)) {
				$album = $data_submit_album['album'];
				$album_id = $data_submit_album['album_id'];
				$album_path = Config::get('prefix') .'/Catalogue_Utilisateur/'.$artist.'/'.$album ;
				if (!file_exists($album_path)){ mkdir("$album_path", 0755);}	
					
					/* RECHERCHE des chansons associées */
					$sql = "SELECT file, title, fileformat 
					FROM `tmp_submit_data` 
					WHERE `tmp_submit`='$id_submit' AND `album_id`='$album_id' AND `artist_id`='$artist_id'";
					$result_submit_song = mysql_query($sql) or exit(mysql_error());		
					while ($data_submit_song = mysql_fetch_assoc($result_submit_song)) {
						$song_path = $data_submit_song['file'];
						$titleb = $data_submit_song['title'];
						$title = str_replace('/','_',$titleb);
						$ext = $data_submit_song['fileformat'];
						$path = Config::get('prefix') .'/Catalogue_Utilisateur/'.$artist.'/'.$album.'/'.$title.'.'.$ext;
						copy($song_path, $path);
					}	
			}
		}
		
		/* ADD TO USER CATALOG */
		toggle_visible('ajax-loading');
		ob_end_flush();
		$catalog = new Catalog("2");
		$catalog->add_to_catalog();
	
		$url 	= Config::get('web_path') . '/admin/catalog.php';
		$title 	= _('Catalog Updated');
		$body	= '';
		show_confirmation($title,$body,$url);
		toggle_visible('ajax-loading');
				
		/* RECHERCHE ARTIST */
		$sql = "SELECT artist_id, artist, bio, web, picture  
		FROM `tmp_submit_data` 
		INNER JOIN `tmp_submit_artist` ON `tmp_submit_data`.artist_id = tmp_submit_artist.id
		WHERE `tmp_submit`='$id_submit'
		GROUP BY artist ;";
		$result_submit_artist = mysql_query($sql) or exit(mysql_error());
		while ($data_submit_artist = mysql_fetch_assoc($result_submit_artist)) {
			$id_artist = $data_submit_artist['artist_id'];
			$artist = $data_submit_artist['artist'];
			$bio = $data_submit_artist['bio'];
			$web = $data_submit_artist['web'];
			$picture = $data_submit_artist['picture'];
			
			$sql = "UPDATE `artist` SET `artist_picture` = '$picture', `biography` = '$bio', `website` = '$web' WHERE  `name` = '$artist' ";
			$db_results = Dba::write($sql);
		}
		
		/* RECHERCHE ALBUM POUR TAGS */
		$sql = "SELECT album, tag  
		FROM `tmp_submit_data` 
		INNER JOIN `tmp_submit_album` ON `tmp_submit_data`.album_id = tmp_submit_album.id
		WHERE `tmp_submit`='$id_submit'
		GROUP BY album ;";
		$result_submit_album = mysql_query($sql) or exit(mysql_error());
		while ($data_submit_album = mysql_fetch_assoc($result_submit_album)) {
			$album = $data_submit_album['album'];
			$tag = $data_submit_album['tag'];
			
			$sql = "SELECT id FROM `album` WHERE `name`='$album'";
			$result_id_album = mysql_query($sql) or exit(mysql_error());
			while ($data_id_album = mysql_fetch_assoc($result_id_album)) {	
				$id_album = $data_id_album['id'];
				$add_album_tag = explode(";", $tag);		
				Tag::add_album_tag($id_album,$add_album_tag);
			}
		}
		
		
		function rrmdir($dir) { 
			if (is_dir($dir)) { 
			     $objects = scandir($dir); 
			     foreach ($objects as $object) { 
			       if ($object != "." && $object != "..") { 
			         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
			       } 
			     } 
			 reset($objects); 
			 rmdir($dir); 
			 } 
		} 
		$id_submit = $_REQUEST['id'];
		$path = ('../tmp_user_catalog/'.$id_submit.'/');
		rrmdir ($path);
		
		$sql = "DELETE FROM `tmp_submit` WHERE `id` = $id_submit ";
		$db_results = Dba::write($sql);
		$sql = "DELETE FROM `tmp_submit_data` WHERE `tmp_submit` = $id_submit ";
		$db_results = Dba::write($sql);
		
		$sql = "SELECT  `id` FROM `tmp_submit`" ;
		$result_el = mysql_query($sql) or exit(mysql_error());
		if (!($r = Dba::fetch_assoc($result_el))) {
			$sql = "DELETE FROM `tmp_submit_artist` ";
			$db_results = Dba::write($sql);
			$sql = "DELETE FROM `tmp_submit_album`";
			$db_results = Dba::write($sql);
		} 
	break;

	default:
	break;
} ?>
