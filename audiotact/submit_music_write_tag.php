<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at http://getid3.sourceforge.net                 //
//            or http://www.getid3.org                         //
/////////////////////////////////////////////////////////////////
//                                                             //
// /demo/demo.simple.php - part of getID3()                    //
// Sample script for scanning a single directory and           //
// displaying a few pieces of information for each file        //
// See readme.txt for more details                             //
//                                                            ///
/////////////////////////////////////////////////////////////////
//Audiotact is an Ampache-based project developped by Oudeis (www.oudeis.fr) with the support of Labo2 (www.bibliotheque.nimes.fr)
require_once 'lib/init.php';
session_start();

// Init id3
$getID3 = new getID3;
$getID3->setOption(array('encoding'=>$TaggingFormat));
getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'write.php', __FILE__, true);
	
switch ($_REQUEST['action']) {
	/* CREATE CONTRIB ID*/
	case 'create':
		$sql = "INSERT INTO `tmp_submit` (`id`, `path`) VALUES (NULL, NULL);";
	 	$db_results = Dba::write($sql);
	 
	 	$sql = "SELECT id FROM `tmp_submit` ORDER BY id DESC LIMIT 1 OFFSET 0;";
	 	$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error()); 
      	$data = mysql_fetch_array($req); 
      	$data_id = $data['id'];
	 	
	 	$data_id_path = Config::get('prefix') .'/tmp_user_catalog/'.$data_id ;
	 	$sql = "UPDATE `tmp_submit` SET  `path` = '$data_id_path' WHERE  `tmp_submit`.`id` =$data_id LIMIT 1 ;";
	 	$db_results = Dba::write($sql);
	 	echo $data_id;
	break;
	
	// SÉLÉCTION-VALIDATION DES FICHIERS CHOISIS
	case 'upload_music_file':
		// Création du répertoire de la proposition
		$sql = "SELECT id FROM `tmp_submit` ORDER BY id DESC LIMIT 1 OFFSET 0;";
	 	$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error()); 
     	$data = mysql_fetch_array($req); 
      	$data_id = $data['id'];	 
      	$path = Config::get('prefix') .'/tmp_user_catalog';
      	if (!file_exists($path)){ mkdir("$path", 0755);}	 
		$data_id_path = Config::get('prefix') .'/tmp_user_catalog/'.$data_id ;
		if (!file_exists($data_id_path)){ mkdir("$data_id_path", 0755);}
	
		// Lancement de l'upload
		$upload = new file_upload();
		$upload->upload_dir = $data_id_path.'/';
		$upload->extensions = array('.mp3', '.jpg', '.zip'); // specify the allowed extensions here
		$upload->rename_file = false;
		
		function analyze_file($filename, $file_title,$data_id) {
					$getID3 = new getID3;
					$newfile = $getID3->analyze($filename);
					getid3_lib::CopyTagsToComments($newfile);
				
					// Variables à analyser
					$file = $newfile['filenamepath'];

					//$album_art = $newfile['comments']['picture'][0]['data'];
					$genre = mysql_real_escape_string($newfile['comments']['genre'][0]);
					$year = $newfile['comments_html']['year'][0];
					if ($year == "0") {
					$year = '-';	
					}
					
					if (!empty ($newfile['comments_html']['title'][0])) { 
					$title = mysql_real_escape_string($newfile['comments_html']['title'][0]); } 
					else { $title = $file_title ; }
					
					$artist = mysql_real_escape_string($newfile['comments_html']['artist'][0]); 
					$size = $newfile['filesize']; 
					$time = $newfile['playtime_seconds']; 
					$track = $newfile['comments_html']['track_number'][0];
					$fileformat = $newfile['fileformat'];
					$album = mysql_real_escape_string($newfile['comments_html']['album'][0]);
				
				/* INSERT ALBUM */
				$sql = "SELECT id FROM `tmp_submit_album` WHERE album='$album'";
				$db_results = Dba::read($sql);
				if ($r = Dba::fetch_assoc($db_results)) {
					$album_id = $r['id'];
				} else {
					$sql = "INSERT INTO `tmp_submit_album` (`album`, `year`, `genre`) " .
					"VALUES ('$album','$year','$genre')";
					$db_results = Dba::write($sql);
					
					$sql = "SELECT id FROM `tmp_submit_album` ORDER BY id DESC LIMIT 1 OFFSET 0;";
				 	$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error()); 
			     	$data = mysql_fetch_array($req); 
			      	$album_id = $data['id'];	
				}
				
				/* INSERT ARTIST */
				$sql = "SELECT id FROM `tmp_submit_artist` WHERE artist='$artist'";
				$db_results = Dba::read($sql);
				if ($r = Dba::fetch_assoc($db_results)) {
				$artist_id = $r['id'];
				} else {
				$sql = "INSERT INTO `tmp_submit_artist` (`artist`) " .
				"VALUES ('$artist')";
				$db_results = Dba::write($sql);
				
				$sql = "SELECT id FROM `tmp_submit_artist` ORDER BY id DESC LIMIT 1 OFFSET 0;";
			 	$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error()); 
		     	$data = mysql_fetch_array($req); 
		      	$artist_id = $data['id'];	
				}

	 			/* INSERT BDD SONGS */
				$sql = "INSERT INTO `tmp_submit_data` (`id`,`tmp_submit`,`file`,`fileformat`, `album_id`,`artist_id`, `title`, `size`, `track`, `time`) VALUES (NULL , '$data_id', '$file','$fileformat', '$album_id', '$artist_id', '$title', '$size', '$track', '$time')";
				$db_results = Dba::write($sql);
			
			
			} /* end Analyse file */


		if(!empty($_FILES)) {
			$upload->the_temp_file = $_FILES['userfile']['tmp_name'];
			$upload->the_file = $_FILES['userfile']['name'];
			$upload->http_error = $_FILES['userfile']['error'];
			$upload->do_filename_check = 'y'; // use this boolean to check for a valid filename
	
			if ($upload->upload()){
				echo '<div id="status">success</div>';
				echo '<div id="message">'. $upload->file_copy .' Successfully Uploaded</br></div>';
				echo '<div id="id_prop">'. $data_id .'</br></div>';
			    	
				// INIT GETID3
				$ext = strtolower(strrchr($upload->file_copy,'.'));
				// ZIP
				if ($ext=='.zip') {
					unlink($data_id_path.'/'.$upload->file_copy);
					if($dossier = opendir($data_id_path)) {
						while(false !== ($fichier = readdir($dossier))) {
							$file_ext = strtolower(strrchr($fichier,'.'));
							if ($file_ext == '.mp3') {
								$filename = $data_id_path.'/'.$fichier ;
								analyze_file($filename,$fichier,$data_id);
							} 
						}
						closedir($dossier);
					}
				} 
				else {
					$filename = $data_id_path.'/'.$upload->file_copy ;
					$file_title = $upload->file_copy;
					analyze_file($filename,$file_title,$data_id);
				}

			} else {
				echo '<div id="status">failed</div>';
				echo '<div id="message">'. $upload->show_error_string() .'</div>';	
			}
			
	}
	break;
	
	// RÉÉCRITURE DES TAGS SUR CHAQUE CHANSON
	case 'write_song_tag':
		foreach ($_POST['submit'] as $key) {
			$id = $key['id'];
			$title = mysql_real_escape_string($key['title']);
			$artist = mysql_real_escape_string($key['artist']);
			$artist_id = mysql_real_escape_string($key['artist_id']);
			$album = mysql_real_escape_string($key['album']); 
			$album_id = mysql_real_escape_string($key['album_id']); 
			$licence = mysql_real_escape_string($key['licence']); 
			$year = $key['year']; 
			$fileformat = $key['fileformat']; 
			$Filename = $key['filename']; 
			
			switch ($fileformat) {
				case 'mp3':
				case 'mp2':
				case 'mp1':
					$ValidTagTypes = array('id3v1', 'id3v2.3'/*, 'ape'*/);
					break;
				case 'mpc':
					$ValidTagTypes = array('ape');
					break;	
				/*case 'ogg':
					if (!empty($OldThisFileInfo['audio']['dataformat']) && ($OldThisFileInfo['audio']['dataformat'] == 'flac')) {
						//$ValidTagTypes = array('metaflac');
						// metaflac doesn't (yet) work with OggFLAC files
						$ValidTagTypes = array();
					} else {
						$ValidTagTypes = array('vorbiscomment');
					}
					break;*/	
				case 'flac':
					$ValidTagTypes = array('metaflac');
					break;	
				case 'real':
					$ValidTagTypes = array('real');
					break;	
				default:
					$ValidTagTypes = array();
					break;
			} 
	
		$TagFormatsToWrite = $ValidTagTypes;
		
		if (!empty($TagFormatsToWrite)) {
			//echo 'starting to write tag(s)<BR>';
			$tagwriter = new getid3_writetags;
			$tagwriter->filename       = $Filename;
			$tagwriter->tagformats     = $TagFormatsToWrite;
			$tagwriter->tag_encoding   = $TaggingFormat;
			$tagwriter->overwrite_tags = true;
			
			if (!empty($_POST['remove_other_tags'])) {
				$tagwriter->remove_other_tags = true;
			}

			$commonkeysarray = array('title', 'artist', 'album', 'year');
			foreach ($commonkeysarray as $keyz) {
				if (!empty($key[$keyz])) {
					//echo ($key[$keyz]);
					$TagData[strtolower($keyz)] = array($key[$keyz]);
					
				}
			}
		
			if (!empty($key['genre'])) {
				$TagData['genre'] = array($key['genre']);
				$genre = $key['genre'];
			} else {
				$TagData['genre'] = '';
				$genre = $key['genre'];
				}
			
			if (!empty($key['track'])) {
				$TagData['track'] = array($key['track'].'');
				$track = $key['track']; 
			}
				
			/*
			if (!empty($_POST['GenreOther'])) {
				$TagData['genre'][] = $_POST['GenreOther'];
			}
			if (!empty($_POST['Track'])) {
				$TagData['track'][] = $_POST['Track'].(!empty($_POST['TracksTotal']) ? '/'.$_POST['TracksTotal'] : '');
			}*/

			/* IMAGES */
			if (!empty($key['cover'])) {
				$getID3 = new getID3;
				$fileinfo = $getID3->analyze($Filename);
				//echo ('analyse '.$Filename);
				//getid3_lib::CopyTagsToComments($fileinfo);
						if ((isset($fileinfo['id3v2']['APIC'][0]['data']))||(isset($fileinfo['id3v2']['PIC'][0]['data']))){
							//echo ('il y a une jaquette');
							if (isset($fileinfo['id3v2']['APIC'][0]['data'])) {
								//$cover = $getID3->info['id3v2']['APIC'][0]['data'];
								$cover = $fileinfo['id3v2']['APIC'][0]['data'] ;
								$type = $fileinfo['id3v2']['APIC'][0]['picturetype'] ;
								$description = $fileinfo['id3v2']['APIC'][0]['description'] ;
				
							} elseif (isset($fileinfo['id3v2']['PIC'][0]['data'])) {
								$cover = $fileinfo['id3v2']['PIC'][0]['data'];
								$type = $fileinfo['id3v2']['PIC'][0]['picturetype'] ;
								$description = $fileinfo['id3v2']['PIC'][0]['description'] ;
							} 
							
							if (isset($fileinfo['id3v2']['APIC'][0]['image_mime'])) {
								$mimetype = $fileinfo['id3v2']['APIC'][0]['image_mime'];
							} else {
								$mimetype = 'image/jpeg'; // or null; depends on your needs
							}
							$TagData['attached_picture'][0]['data']          = $cover ;
							$TagData['attached_picture'][0]['picturetypeid'] = $type;
							$TagData['attached_picture'][0]['description']   = $description;
							$TagData['attached_picture'][0]['mime']          = 'image/'.$mimetype;
							//continue;
						}
			} else {
				//return false ;
				$TagData['attached_picture'] = '';
				}

			$tagwriter->tag_data = $TagData;
			if ($tagwriter->WriteTags()) {
			//	echo 'Successfully wrote tags<BR>';
			//	echo ($title);
				if (!empty($tagwriter->warnings)) {
				//	echo 'There were some warnings:<BLOCKQUOTE STYLE="background-color:#FFCC33; padding: 10px;">'.implode('<BR><BR>', $tagwriter->warnings).'</BLOCKQUOTE>';
				}


				/* SONG */
				$sql = "UPDATE `tmp_submit_data` SET `title` = '$title', `track` = '$track', `licence` = '$licence' WHERE  `tmp_submit_data`.`id` = $id ";
				$db_results = Dba::write($sql);

				/* ALBUM */
				if (empty($album)) {
					//echo ('empty');
					$album = "Inconnu";
					
					// Si Inconnu existe déjà
					$sql = "SELECT id FROM `tmp_submit_album` WHERE album='$album'";
					$db_results = Dba::read($sql);
					
					// s'il existe - update de la chanson
					if ($r = Dba::fetch_assoc($db_results)) {
						$album_idn = $r['id'];
						$sql = "UPDATE `tmp_submit_data` SET `album_id` = '$album_idn' WHERE  `id` = $id ";
						$db_results = Dba::write($sql);
						
						$sql = "UPDATE `tmp_submit_album` SET `year` = '$year',`genre` = '$genre' WHERE `id`='$album_idn'";
						$db_results = Dba::write($sql);
					} else { // sinon, on update le champ vide
					
					$sql = "SELECT id FROM `tmp_submit_album` WHERE `album`=''";
				 	$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error()); 
			     	$data = mysql_fetch_array($req); 
			      	$album_idn = $data['id'];
					
					$sql = "UPDATE `tmp_submit_album` SET `album` = '$album', `year` = '$year',`genre` = '$genre' WHERE `id`='$album_idn'";
					$db_results = Dba::write($sql);

			      	$sql = "UPDATE `tmp_submit_data` SET `album_id` = '$album_idn' WHERE  `id` = $id ";
					$db_results = Dba::write($sql);
					}
				} else {
					$sql = "SELECT id FROM `tmp_submit_album` WHERE album='$album'";
					$db_results = Dba::read($sql);
					// si l'album n'est pas vide, on voit s'il existe déjà
					if ($r = Dba::fetch_assoc($db_results)) {
						$album_idn = $r['id'];
						$sql = "UPDATE `tmp_submit_data` SET `album_id` = '$album_idn' WHERE  `album_id` = $album_id ";
						$db_results = Dba::write($sql);
					} else { // sinon on le crée
					$sql = "INSERT INTO `tmp_submit_album` (`album`,`year`,`genre`) VALUES ('$album','$year','$genre')";
					$db_results = Dba::write($sql);
					
					$sql = "SELECT id FROM `tmp_submit_album` ORDER BY id DESC LIMIT 1 OFFSET 0;";
				 	$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error()); 
			     	$data = mysql_fetch_array($req); 
			      	$album_idn = $data['id'];
			      	
			      	$sql = "UPDATE `tmp_submit_data` SET `album_id` = '$album_idn' WHERE  `id` = $id ";
					$db_results = Dba::write($sql);
					}
				}

				/* ARTIST */
				//$sql3 = "UPDATE `tmp_submit_artist` SET `artist` = '$artist' WHERE  `tmp_submit_artist`.`id` = $artist_id ";
				//$db_results3 = Dba::write($sql3);
				
				/* ALBUM */
				if (empty($artist)) {
					//echo ('empty');
					$artist = "Inconnu";
					
					// Si Inconnu existe déjà
					$sql = "SELECT id FROM `tmp_submit_artist` WHERE artist='$artist'";
					$db_results = Dba::read($sql);
					
					// s'il existe - update de la chanson
					if ($r = Dba::fetch_assoc($db_results)) {
						$artist_idn = $r['id'];
						$sql = "UPDATE `tmp_submit_data` SET `artist_id` = '$artist_idn' WHERE  `id` = $id ";
						$db_results = Dba::write($sql);
					} else { // sinon, on update le champ vide
					
					$sql = "SELECT id FROM `tmp_submit_artist` WHERE `artist`=''";
				 	$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error()); 
			     	$data = mysql_fetch_array($req); 
			      	$artist_idn = $data['id'];
					
					$sql = "UPDATE `tmp_submit_artist` SET `artist` = '$artist' WHERE `id`='$artist_idn'";
					$db_results = Dba::write($sql);

			      	$sql = "UPDATE `tmp_submit_data` SET `artist_id` = '$artist_idn' WHERE  `id` = $id ";
					$db_results = Dba::write($sql);
					}
				} else {
					$sql = "SELECT id FROM `tmp_submit_artist` WHERE artist='$artist'";
					$db_results = Dba::read($sql);
					// si l'album n'est pas vide, on voit s'il existe déjà
					if ($r = Dba::fetch_assoc($db_results)) {
						$artist_idn = $r['id'];
						$sql = "UPDATE `tmp_submit_data` SET `artist_id` = '$artist_idn' WHERE  `artist_id` = $artist_id ";
						$db_results = Dba::write($sql);
					} else { // sinon on le crée
					$sql = "INSERT INTO `tmp_submit_artist` (`artist`) VALUES ('$artist')";
					$db_results = Dba::write($sql);
					
					$sql = "SELECT id FROM `tmp_submit_artist` ORDER BY id DESC LIMIT 1 OFFSET 0;";
				 	$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error()); 
			     	$data = mysql_fetch_array($req); 
			      	$artist_idn = $data['id'];
			      	
			      	$sql = "UPDATE `tmp_submit_data` SET `artist_id` = '$artist_idn' WHERE  `id` = $id ";
					$db_results = Dba::write($sql);
					}
				}	
			} else {
				//echo 'Failed to write tags!<BLOCKQUOTE STYLE="background-color:#FF9999; padding: 10px;">'.implode('<BR><BR>', $tagwriter->errors).'</BLOCKQUOTE>';
			}
		} else {
			//echo 'WARNING: no tag formats selected for writing - nothing written';
		}		
}
		
$sql = "DELETE FROM `tmp_submit_album` WHERE `album`=''";
$db_results = Dba::write($sql);
$sql = "DELETE FROM `tmp_submit_artist` WHERE `artist`=''";
$db_results = Dba::write($sql);
$id_submit = $_REQUEST["id_submit"];
echo $id_submit ;
//echo ('<span id="id_submit">'. $id_submit .'</span>');
break;

case 'write_album_artist_tag': 

	$getID3 = new getID3;
	$getID3->setOption(array('encoding'=>$TaggingFormat));
	getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'write.php', __FILE__, true);
	
	$id_submit = $_REQUEST['id'];

	
	/* RÉCUPÉRATION DU POST */	
	foreach ($_POST['submit'] as $key) {	
			$album = $key['album'];
			$album_id = mysql_real_escape_string($key['album_id']);
			$year = mysql_real_escape_string($key['year']);
			if (!empty($key['GenreOther'])) {$genre = mysql_real_escape_string($key['GenreOther']);} 
			else {$genre = mysql_real_escape_string($key['genre']);}
			$tag = mysql_real_escape_string($key['tag']);

			
			
			$sql = "UPDATE `tmp_submit_album` SET `year` = '$year', `genre` = '$genre', `tag` = '$tag' WHERE  `tmp_submit_album`.`id` = $album_id ";
			$db_results = Dba::write($sql);
			
			/* post artist */
			$artist = $key['artist'];
			$bio = $key['bio'];
			$web = $key['web'];
			$id_artist = $key['artist_id'];

			/* RÉCUP IMAGE */
			if(isset($_FILES['submit'])){ $tmp_name = $_FILES['submit']['tmp_name'][$album]['picture']; }
			

			$test = array ('id_album' => $album_id, 'year' => $year, 'album' => $album, 'genre' => $genre, 'image' => $tmp_name);
			$test2[]= $test;
		}
	
	
	/* POST ARTIST */
	foreach ($_POST['update'] as $key) {	
		
			$artist = $key['artist'];
			$bio = $key['bio'];
			$web = $key['web'];
			$id_artist = $key['artist_id'];

			/* RÉCUP IMAGE */
			if(isset($_FILES['update'])){
				$tmp_name_artist = $_FILES['update']['tmp_name'][$artist]['picture_artist'];
				$picture_name_artist = $_FILES['update']['name'][$artist]['picture_artist'];
				$artist_error = $_FILES['update']['error'][$artist]['picture_artist'];
		
				if (!empty($tmp_name_artist)) {
					$extensions = array('.png', '.gif', '.jpg', '.JPG', '.jpeg'); 
					$extension = strrchr($picture_name_artist, '.');
					if(!in_array($extension, $extensions)) {$erreur ="Vous devez uploader un fichier de type png, gif, jpg, jpeg, ...";}
					$artist_name = str_replace(" ", "_", $artist);
					$photo_artist = $artist_name.$extension;

					$filename="images_artist";
					if (!file_exists($filename)){ mkdir("$filename", 0755);}
					if( !is_uploaded_file($tmp_name_artist) )   { echo ("Le fichier est introuvable"); }
					else { 
						move_uploaded_file($tmp_name_artist, "$filename/$photo_artist"); 
						$thumb = PhpThumbFactory::create("$filename/$photo_artist");  
						$thumb-> adaptiveResize(150, 150)->save("$filename/$photo_artist");  
					}
				
					$sql = "UPDATE `tmp_submit_artist` SET `picture` = '$photo_artist' WHERE  `tmp_submit_artist`.`id` = $id_artist ";
					$db_results = Dba::write($sql);	
				}
			}
			
			$sql = "UPDATE `tmp_submit_artist` SET `bio` = '$bio', `web` = '$web' WHERE  `tmp_submit_artist`.`id` = $id_artist ";
			$db_results = Dba::write($sql);
	} // FIN ARTIST
	
	
		
	/* POST ET BDD => ÉCRITURE DES TAGS */
	foreach ($test2 as $keyy) {
			$album_id = $keyy['id_album'];
			$sql = "SELECT file, title, fileformat, album, track, artist, `tmp_submit_album`.id 
			FROM `tmp_submit_album` 
			INNER JOIN `tmp_submit_data` ON `tmp_submit_album`.id = tmp_submit_data.album_id 
			INNER JOIN `tmp_submit_artist` ON `tmp_submit_artist`.id = tmp_submit_data.artist_id 
			WHERE `tmp_submit_album`.id='$album_id' AND `tmp_submit` = $id_submit ;"; 
			
			$result = mysql_query($sql) or exit(mysql_error());
			
			while ($data = mysql_fetch_assoc($result)) {
				$album = mysql_real_escape_string($keyy['album']);
				$year = $keyy['year'];
				$genre = $keyy['genre'];
				
				$image = $keyy['image'];
				$album = $data['album'];
				$title = $data['title'];
				

				$file = $data['file'];
		
				$fileformat = $data['fileformat'];
				$Filename = $data['file'];
				
				
				switch ($fileformat) {
					case 'mp3':
					case 'mp2':
					case 'mp1':
						$ValidTagTypes = array('id3v1', 'id3v2.3');
						break;
					case 'mpc':
						$ValidTagTypes = array('ape');
						break;		
					case 'flac':
						$ValidTagTypes = array('metaflac');
						break;	
					case 'real':
						$ValidTagTypes = array('real');
						break;	
					default:
						$ValidTagTypes = array();
						break;
				} 
				$TagFormatsToWrite = $ValidTagTypes;
		
			if (!empty($TagFormatsToWrite)) {	
				$tagwriter = new getid3_writetags;
				$tagwriter->filename       = $Filename;
				$tagwriter->tagformats     = $TagFormatsToWrite;
				$tagwriter->tag_encoding   = $TaggingFormat;
				$tagwriter->overwrite_tags = true;
				
				
				/* Infos générales 
				$commonkeysarray = array('album', 'year');
				foreach ($commonkeysarray as $keyz) {
					if (!empty($keyy[$keyz])) {
						$TagData[strtolower($keyz)] = array($keyy[$keyz]);
					}  else {
						$TagData[strtolower($keyzz)] = '';
						}
				}*/
				
				if (!empty($album)) {
					$TagData['album'] = array($album);
				} else {
					$TagData['album'] = '';
					}
				
				//if ($year != "0") {
				if (!empty($year)) {
					$TagData['year'] = array($year);
				} else {
					$TagData['year'] = '';
					}
				
				$datainfo = array('title', 'artist');
				foreach ($datainfo as $keyzz) {
					if (!empty($data[$keyzz])) {
						$TagData[strtolower($keyzz)] = array($data[$keyzz]);
					} else {
						$TagData[strtolower($keyzz)] = '';
						}
				}
				//print_r ($TagData['artist']);
				
				/* Genre */
				/*if (!empty($genre_other)) {
				$TagData['genre'] = $genre_other;
				$genre = $key['genre_other'];
				} else {*/
					if (!empty($genre)) {
					$TagData['genre'] = array($genre);
					} 
					else {
					$TagData['genre'] = '';
					$genre = $key['genre'];
					}
				//}
				/*if (!empty($_POST['GenreOther'])) {
			$TagData['genre'][] = $_POST['GenreOther'];
		}	*/			
				/* N° de piste */
				if (!empty($data['track'])) {
					$TagData['track'] = array($data['track'].'');
				}
				
				/* PICTURE */
				if (!empty($image)) {
					if( !is_uploaded_file($image) )   { echo ("Le fichier est introuvable"); }
					else { 
						move_uploaded_file($image, "tmp_user_catalog/$id_submit/$album"); 
						$thumb = PhpThumbFactory::create("tmp_user_catalog/$id_submit/$album");  
						$thumb-> adaptiveResize(150, 150)->save("tmp_user_catalog/$id_submit/$album"); 
						$album_thumb = "tmp_user_catalog/$id_submit/$album";
					}
					

					if (in_array('id3v2.4', $tagwriter->tagformats) || in_array('id3v2.3', $tagwriter->tagformats) || in_array('id3v2.2', $tagwriter->tagformats)) {
						//if (is_uploaded_file($image)) {
							ob_start();
							if ($fd = fopen($album_thumb, 'rb')) {
								ob_end_clean();
								$APICdata = fread($fd, filesize($album_thumb));
								fclose ($fd);
		
								list($APIC_width, $APIC_height, $APIC_imageTypeID) = GetImageSize($album_thumb);
								$imagetypes = array(1=>'gif', 2=>'jpeg', 3=>'png');
								if (isset($imagetypes[$APIC_imageTypeID])) {
		
									$TagData['attached_picture'][0]['data']          = $APICdata;
									$TagData['attached_picture'][0]['picturetypeid'] = "0";
									$TagData['attached_picture'][0]['description']   = $data['album'];
									$TagData['attached_picture'][0]['mime']          = 'image/'.$imagetypes[$APIC_imageTypeID];
									unlink($album_thumb);
								} else { echo '<b>invalid image format (only GIF, JPEG, PNG)</b><br>';}
							} else { $errormessage = ob_get_contents(); ob_end_clean(); echo '<b>cannot open '.$image.'</b><br>'; }
						//} else { echo '<b>!is_uploaded_file('.$image.')</b><br>'; }
					} else { echo '<b>WARNING:</b> Can only embed images for ID3v2<br>'; }
					
				// RECHERCHE ID3
				} else {
					$getID3 = new getID3;
					$fileinfo = $getID3->analyze($Filename);
					
					if ((isset($getID3->info['id3v2']['APIC'][0]['data']))||(isset($getID3->info['id3v2']['PIC'][0]['data'])) ){
						if (isset($getID3->info['id3v2']['APIC'][0]['data'])) {
							$cover = $fileinfo['id3v2']['APIC'][0]['data'] ;
							$type = $fileinfo['id3v2']['APIC'][0]['picturetype'] ;
							$description = $fileinfo['id3v2']['APIC'][0]['description'] ;
						} elseif (isset($getID3->info['id3v2']['PIC'][0]['data'])) {
							$cover = $fileinfo['id3v2']['PIC'][0]['data'];
							$type = $fileinfo['id3v2']['PIC'][0]['picturetype'] ;
							$description = $fileinfo['id3v2']['PIC'][0]['description'] ;
						} 
						if (isset($fileinfo->info['id3v2']['APIC'][0]['image_mime'])) {
							$mimetype = $fileinfo->info['id3v2']['APIC'][0]['image_mime'];
						} else {
							$mimetype = 'image/jpeg'; // or null; depends on your needs
						}
						$TagData['attached_picture'][0]['data']          = $cover ;
						$TagData['attached_picture'][0]['picturetypeid'] = $type;
						$TagData['attached_picture'][0]['description']   = $description;
						$TagData['attached_picture'][0]['mime']          = 'image/'.$mimetype;
					} else { // IF EMPTY
						$TagData['attached_picture'] = '';
					}
				} // PICTURE END
			
			/* Lancement de l'écriture */
			$tagwriter->tag_data = $TagData;
			if ($tagwriter->WriteTags()) {
				$ok = 'Successfully wrote tags<BR>Successfully wrote tags<BR>';

				if (!empty($tagwriter->warnings)) { echo 'There were some warnings:<BLOCKQUOTE STYLE="background-color:#FFCC33; padding: 10px;">'.implode('<BR><BR>', $tagwriter->warnings).'</BLOCKQUOTE>'; }				
			} else { echo 'Failed to write tags!<BLOCKQUOTE STYLE="background-color:#FF9999; padding: 10px;">'.implode('<BR><BR>', $tagwriter->errors).'</BLOCKQUOTE>';
			}
		} else { // ERREUR 
			echo 'WARNING: no tag formats selected for writing - nothing written';
		}
	
	} /* END MYSQL SEARCH */
} /* FIN POST ET BDD => ÉCRITURE DES TAGS */ 

 require Config::get('prefix') . '/templates/show_contrib_step4.inc.php';
?>

<?php
		
break ;



	
	
	
	
	
	
	
	
	



}
?>
