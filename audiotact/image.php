<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Image
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

/**
 * Album Art
 * This pulls album art out of the file using the getid3 library
 * and dumps it to the browser as an image mime type.
 *
 */

// This file is a little weird it needs to allow API session
// this needs to be done a little better, but for now... eah
define('NO_SESSION','1');
require 'lib/init.php';

// Check to see if they've got an interface session or a valid API session, if not GTFO
if (!vauth::session_exists('interface',$_COOKIE[Config::get('session_name')]) AND !vauth::session_exists('api',$_REQUEST['auth']) AND !vauth::session_exists('xml-rpc',$_REQUEST['auth'])) {
	debug_event('DENIED','Image Access, Checked Cookie Session:' . $_COOKIE[Config::get('session_name')] . ' and Auth:' . $_REQUEST['auth'],'1');
	exit;
}

// If we aren't resizing just trash thumb
if (!Config::get('resize_images')) { unset($_GET['thumb']); } 

// FIXME: Legacy stuff - should be removed after a version or so
if (!isset($_GET['object_type'])) { 
	$_GET['object_type'] = 'album'; 
} 

$type = Art::validate_type($_GET['object_type']); 

/* Decide what size this image is */
switch ($_GET['thumb']) {
	case '1':
		/* This is used by the now_playing stuff */
		$size['height'] = '75';
		$size['width']	= '75';
	break;
	case '2':
		$size['height']	= '128';
		$size['width']	= '128';
	break;
	case '3':
		/* This is used by the flash player */
		$size['height']	= '80';
		$size['width']	= '80';
	break;
	default:
		$size['height'] = '275';
		$size['width']	= '275';
		if (!isset($_GET['thumb'])) { $return_raw = true; }
	break;
} // define size based on thumbnail

switch ($_GET['type']) {
	case 'popup':
		require_once Config::get('prefix') . '/templates/show_big_art.inc.php';
	break;
	// If we need to pull the data out of the session
	case 'session':
		vauth::check_session();
		$key = scrub_in($_REQUEST['image_index']);
		$image = Art::get_from_source($_SESSION['form']['images'][$key]);
		$mime = $_SESSION['form']['images'][$key]['mime'];
		$data = explode("/",$mime);
		$extension = $data['1'];
		// Send the headers and output the image
		header("Expires: Sun, 19 Nov 1978 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Pragma: no-cache");
		
		header("Content-type: $mime");
		header("Content-Disposition: filename=" . $key . "." . $extension);
		echo $image;
	break;
	
	case 'cover': /* show cover for contrib form*/
		$filename = $_REQUEST['file'];
		$getID3 = new getID3;
		#$getID3->option_tag_id3v2 = true; # Don't know what this does yet
		$getID3->analyze($filename);
		
		if (isset($getID3->info['id3v2']['APIC'][0]['data'])) {$cover = $getID3->info['id3v2']['APIC'][0]['data'];} 
		elseif (isset($getID3->info['id3v2']['PIC'][0]['data'])) {$cover = $getID3->info['id3v2']['PIC'][0]['data'];} 
		else {$cover = null;}
		
		if (isset($getID3->info['id3v2']['APIC'][0]['image_mime'])) {$mimetype = $getID3->info['id3v2']['APIC'][0]['image_mime'];} 
		else { $mimetype = 'image/jpeg';}
		
		if (!is_null($cover)) {
			header("Content-Type: " . $mimetype);
			if (isset($getID3->info['id3v2']['APIC'][0]['image_bytes'])) { header("Content-Length: " . $getID3->info['id3v2']['APIC'][0]['image_bytes']); }
			header("Expires: Tue, 27 Mar 1984 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Pragma: no-cache");
			/*header("Content-type: $mimetype");
			header("Content-Type: " . $mimetype);
			header("Content-Disposition: filename=" . scrub_out($media->name) . "." . $extension);*/
			echo($cover);
		}
	break;
	case 'playlist' : /* Playlist cover - multiple albums*/
		$id_album = $_GET['id'] ;
		$var = unserialize(urldecode($id_album));
		
		if ((count($var)) > 4 ) {
			$srcImagePaths = array();
			foreach ($var as $id){
					$media = new $type($id); 
					$art = new Art($media->id,$type); 
					$art->get_db(); 
					$thumb_data = $art->get_thumb($size);	
					$srcImagePaths[] = $thumb_data['thumb'];
			} 
			$tileWidth = $tileHeight = $size['height'];
			$numberOfTiles = 2;
			$pxBetweenTiles = 0;
			$leftOffSet = $topOffSet = 0;
			 
			$mapWidth = $mapHeight = ($tileWidth + $pxBetweenTiles) * $numberOfTiles;
			 
			$mapImage = imagecreatetruecolor($mapWidth, $mapHeight);
			$bgColor = imagecolorallocate($mapImage, 101, 161, 27);
			imagefill($mapImage, 0, 0, $bgColor);
			
			function indexToCoords($index)
			{
				 global $tileWidth, $pxBetweenTiles, $leftOffSet, $topOffSet, $numberOfTiles;		 
				 $x = ($index % 2) * ($tileWidth + $pxBetweenTiles) + $leftOffSet;
				 $y = floor($index / 2) * ($tileWidth + $pxBetweenTiles) + $topOffSet;
				 return Array($x, $y);
			}
			 
			foreach ($srcImagePaths as $index => $srcImagePath)
			{
				 list ($x, $y) = indexToCoords($index);
				 $tileImg = ImageCreateFromString($srcImagePath);
				 imagecopy($mapImage, $tileImg, $x, $y, 0, 0, $tileWidth, $tileHeight);
				 imagedestroy($tileImg);
			}
			
			$thumbSize = $size['height'];
			$thumbImage = imagecreatetruecolor($thumbSize, $thumbSize);
			imagecopyresampled($thumbImage, $mapImage, 0, 0, 0, 0, $thumbSize, $thumbSize, $mapWidth, $mapWidth);
			 
			header ("Content-type: image/jpeg");
			imagejpeg($thumbImage);
		
		} else {
			$rand_keys = array_rand($var, 1);
			$id = $var[$rand_keys] ;
			$media = new $type($id); 
			$art = new Art($media->id,$type); 
			$art->get_db(); 
			$thumb_data = $art->get_thumb($size);	
			$mime = $thumb_data ? $thumb_data['thumb_mime'] : $art->raw_mime; 	
			$source = $thumb_data ? $thumb_data['thumb'] : $art->raw; 
			if (!$source) { 
			header('Content-type: image/jpeg');
			readfile(Config::get('prefix') . Config::get('theme_path') . '/images/blankalbum.jpg');
			break;
		} // else no image
		
			$extension = Art::extension($mime); 
			
			// Send the headers and output the image
			header("Expires: Tue, 27 Mar 1984 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Pragma: no-cache");
			header("Content-type: $mime");
			header("Content-Disposition: filename=" . scrub_out($media->name) . "." . $extension);
			echo $source;
		}
	break;

	default:
		$media = new $type($_GET['id']); 
		$art = new Art($media->id,$type); 
		$art->get_db();  
		
		if (!$art->raw_mime) { 
			header('Content-type: image/jpeg');
			readfile(Config::get('prefix') . Config::get('theme_path') . '/images/blankalbum.jpg');
			break;
		} // else no image
		
		if ($_GET['thumb']) {
			$thumb_data = $art->get_thumb($size);
		}
				
		$mime = $thumb_data ? $thumb_data['thumb_mime'] : $art->raw_mime; 	
		$source = $thumb_data ? $thumb_data['thumb'] : $art->raw; 
		$extension = Art::extension($mime); 
		
		// Send the headers and output the image
		header("Expires: Tue, 27 Mar 1984 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-type: $mime");
		header("Content-Disposition: filename=" . scrub_out($media->name) . "." . $extension);
		echo $source;

	break;
} // end switch type

?>
