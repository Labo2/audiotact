<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Albums
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

require_once '../lib/init.php';
session_start();
$web_path = Config::get('web_path');

if (!Access::check('interface','100')) {
	access_denied();
	exit;
}


/* Switch on Action */
switch ($_REQUEST['action']) {	
	case 'update_artist_picture':
		if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {
		$id_artist = $_POST['id_artist'];
		$artist_name = $_POST['name_artist'];
		$photo_artist = $_FILES['photo_artist']['name'];
		$tmp_file = $_FILES['photo_artist']['tmp_name'];
	
		$extensions = array('.png','.jpg', '.JPG', '.jpeg'); 
		$extension = strrchr($photo_artist, '.');
		if(!in_array($extension, $extensions)) {$erreur ="Vous devez uploader un fichier de type png, gif, jpg, jpeg, ...";}
		$artist_name = str_replace(array('/', '\''), '_', $artist_name);
		$photo_artist = $artist_name.$extension;

		$filename="../images_artist";
		if (!file_exists($filename)){ mkdir("$filename", 0755);}

		if ($_FILES['photo_artist']['error'] > 0) { exit("Erreur"); }
		if( !is_uploaded_file($tmp_file) ) { exit("Le fichier est introuvable"); }//si le fichier temporaire n'existe pas

		if (!isset($erreur)) {	
		 	move_uploaded_file($tmp_file, "$filename/$photo_artist");
		 	$thumb = PhpThumbFactory::create("$filename/$photo_artist");  
			$thumb-> adaptiveResize(150, 150)->save("$filename/$photo_artist");  
		 	echo '<img width="150px" height="150px" src="'.$web_path.'/images_artist/'.$photo_artist.'">';	
		} 
		else  { echo ('Erreur');}
		Artist::update_artist_picture($id_artist,$photo_artist);
		}	
	break;
	
	case 'delete_artist_picture':
		$id = $_REQUEST['id_artist'];
		$art = new Artist($id);
		$photo = $art-> artist_picture;
		$image_to_delete = '../images_artist/'.$photo ;
		unlink($image_to_delete);
		Artist::delete_artist_picture($id);
		echo '<img width="150px" height="150px" src="'.$web_path . Config::get('theme_path').'/images/artist.png">';
	break;
	
	case 'update_artist_bio':
		$id = $_POST['artist_id'];
		$bio = mysql_real_escape_string($_POST['artist_bio']);
		Artist:: update_artist_bio($id,$bio);
	break;
	
	case 'update_artist_web':
		$id = $_POST['id'];
		$web = mysql_real_escape_string($_POST['name']);
		Artist:: update_artist_web($id,$web);
		echo $web;
	break;
	
	case 'update_artist_name':
		$id = $_POST['id'];
		$name = mysql_real_escape_string($_POST['name']);
		Artist:: update_artist_name($id,$name);
		echo $name;
	break;
	
	case 'update_album_name':
		$id = $_POST['id'];
		$name =  mysql_real_escape_string($_POST['name']);
		Album:: update_album_name($id,$name);
		echo $name;
	break;
	
	case 'clear_art':
		if (!$GLOBALS['user']->has_access('75')) { access_denied(); }
		$art = new Art($_GET['album_id'],'album'); 
		$art->reset();
		echo '<img width="150px" height="150px" src="'.$web_path . Config::get('theme_path').'/images/blankalbum.jpg">';
	break;
	
	case 'find_art':
		if (!Access::check('interface','100')) { access_denied(); exit; }
	    $album = new Album($_GET['album_id']);
		$album->format();
		$art = new Art($album->id,'album'); 
		$images = array();
		$cover_url = array();

		if (isset($_REQUEST['artist_name'])) { $artist = scrub_in($_REQUEST['artist_name']);}
		elseif ($album->artist_count == '1') { $artist = $album->f_artist_name;}
		if (isset($_REQUEST['album_name'])) { $album_name = scrub_in($_REQUEST['album_name']);}
		else { $album_name = $album->full_name;}

		$options['artist'] 	= $artist;
		$options['album_name']	= $album_name;
		$options['keyword']	= $artist . " " . $album_name;

		// Attempt to find the art.
		$images = $art->gather($options,'8');

		if (!empty($_REQUEST['cover'])) {
			$path_info = pathinfo($_REQUEST['cover']);
			$cover_url[0]['url'] 	= scrub_in($_REQUEST['cover']);
			$cover_url[0]['mime'] 	= 'image/' . $path_info['extension'];
		}
		$images = array_merge($cover_url,$images);

		foreach ($images as $index=>$image) {if ($image['raw']) { unset($images[$index]['raw']); }} 
		$_SESSION['form']['images'] = $images;

		$albumname = $album->name;
		$artistname = $artist;

		if (!empty($_REQUEST['album_name'])) {   $albumname = scrub_in($_REQUEST['album_name']); }
		if (!empty($_REQUEST['artist_name'])) {  $artistname = scrub_in($_REQUEST['artist_name']); }

		require_once Config::get('prefix') . '/admin/show_get_albumart.inc.php';
	break;
	
	case 'upload_album_art':
		if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {
			$art = new Art($_POST['album_id'],'album'); 
			$path_info = pathinfo($_FILES['file']['name']);
			$upload['file'] = $_FILES['file']['tmp_name'];
			$upload['mime'] = 'image/' . $path_info['extension'];
			$image_data = $art->get_from_source($upload);
	
			if ($image_data) {
				$art->insert($image_data,$upload['0']['mime'],$_POST['album_id']);
				echo '<img height="150" width="150" alt="'.$name.'" title="'.$name.'" src="'. Config::get('web_path').'/images/waiting.png" />';
				/*echo '<img height="150" width="150" alt="'.$name.'" title="'.$name.'" src="'. Config::get('web_path').'/image.php?id='.$_POST['album_id'].'&amp;thumb=1" />';*/
			} 
		}
	break;
	
	case 'select_art':
		$image_id = $_REQUEST['image'];
		$album_id = $_REQUEST['album_id'];
		$art = new Art($album_id,'album'); 
		$image 	= $art->get_from_source($_SESSION['form']['images'][$image_id]);
		$mime	= $_SESSION['form']['images'][$image_id]['mime'];
		$art->insert($image,$mime);
		/*echo '<img height="150" width="150" alt="'.$name.'" title="'.$name.'" src="'. Config::get('web_path').'/image.php?id='.$album_id.'&amp;thumb=1" />';*/
		echo '<img height="150" width="150" alt="'.$name.'" title="'.$name.'" src="'. Config::get('web_path').'/images/waiting.png" />';
	break;
	
	case 'delete_artist':
		$id = $_REQUEST['artist_id'];
		$artist = new Artist($id);
		$artist_albums = $artist->get_albums();
		/* delete album or no */
		 foreach ($artist_albums as $album) {
		 	$album = new Album($album);
		 	$album->format();  
		 	if ($album->artist_count == '1') {
		 		$media = new Album($album->id); 
				$art = new Art($media->id,'album'); 
				$art->get_db();  
				if ($art->raw_mime) { Art::delete_art($album->id);}
				Album::delete_album($album->id);
		 	}
		 }
		/* delete songs */
		$artist_songs = $artist->get_songs();
		foreach ($artist_songs as $song) {
			$song = new Song($song);
			$song->format();
			unlink($song->file); 
			Song::delete_song($song->id);
		}
		Artist::delete_artist($id);
	break;
	
	case 'delete_album':
		$id = $_REQUEST['album_id'];
		$album = new Album($id);
		$art = new Art($album->id,'album'); 
		$art->get_db();  
		if ($art->raw_mime) { Art::delete_art($album->id);}
		Album::delete_album($id);		
			
		/* delete songs */
		$album_songs = $album->get_songs();
		foreach ($album_songs as $song) {
			$song = new Song($song);
			$song->format();
			Song::delete_song($song->id);
		}			
	break;
	
	case 'delete_duplicated_song':
		$id = $_REQUEST['song_id'];
		Song::delete_song($id);
	break;
	
	
	case 'delete_playlist':
		$id = $_REQUEST['playlist_id'];
		Playlist::admin_delete_playlist($id);
	break;
	case 'delete_shout':
		$id = $_REQUEST['id'];
		Shoutbox::delete_shout($id);
	break;
	case 'delete_tag':
		$id = $_REQUEST['id'];
		$album_id = $_REQUEST['album_id'];
		$tag = new Tag($id);
		$tag->remove_tag('album',$album_id);
	break;
}?>
