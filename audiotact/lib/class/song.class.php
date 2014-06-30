<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Song Class
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
 */

/**
 * Song Class
 *
 * Description here...
 *
 * @package	Ampache
 * @copyright	2001 - 2011 Ampache.org
 * @license	http://opensource.org/licenses/gpl-2.0 GPLv2
 * @link	http://www.ampache.org/
 */
class Song extends database_object implements media {

	/* Variables from DB */
	public $id;
	public $file;
	public $album; // album.id (Int)
	public $artist; // artist.id (Int)
	public $title;
	public $year;
	public $bitrate;
	public $rate;
	public $mode;
	public $size;
	public $time;
	public $track;
	public $album_mbid;
	public $artist_mbid;
	public $type;
	public $mime;
	public $played;
	public $enabled;
	public $addition_time;
	public $update_time;
	public $mbid; // MusicBrainz ID
	public $selected;

	/* Setting Variables */
	public $_transcoded = false;
	public $resampled = false;	
	public $_fake = false; // If this is a 'construct_from_array' object
	public $transcoded_from;
	private $_transcode_cmd;

	/**
	 * Constructor
	 * Song class, for modifing a song.
	 */
	public function __construct($id='') {

		if (!$id) { return false; }

		/* Assign id for use in get_info() */
		$this->id = intval($id);

		/* Get the information from the db */
		if ($info = $this->_get_info()) {

			foreach ($info as $key=>$value) {
				$this->$key = $value;
			}
			// Format the Type of the song
			$this->format_type();
		}

		return true;

	} // constructor

	/**
	 * build_cache
	 * This attempts to reduce # of queries by asking for everything in the browse
	 * all at once and storing it in the cache, this can help if the db connection
	 * is the slow point
	 */
	public static function build_cache($song_ids) {

		if (!is_array($song_ids) OR !count($song_ids)) { return false; }

		$idlist = '(' . implode(',', $song_ids) . ')';

		// Song data cache
		$sql = "SELECT song.id,file,catalog,album,year,artist,".
				"title,bitrate,rate,mode,size,time,track,played,song.enabled,selected,update_time,tag_map.tag_id,".
				"mbid,".
				"addition_time FROM `song` " .
				"LEFT JOIN `tag_map` ON `tag_map`.`object_id`=`song`.`id` AND `tag_map`.`object_type`='song' " .
				"WHERE `song`.`id` IN $idlist";
		$db_results = Dba::read($sql);

		while ($row = Dba::fetch_assoc($db_results)) {
			parent::add_to_cache('song',$row['id'],$row);
			$artists[$row['artist']]	= $row['artist'];
			$albums[$row['album']]		= $row['album'];
			if ($row['tag_id']) {
				$tags[$row['tag_id']]		= $row['tag_id'];
			}
		}

		Artist::build_cache($artists);
		Album::build_cache($albums);
		Tag::build_cache($tags);
		Tag::build_map_cache('song',$song_ids);

		// If we're rating this then cache them as well
		if (Config::get('ratings')) {
			Rating::build_cache('song',$song_ids);
		}

		// Build a cache for the song's extended table
		$sql = "SELECT * FROM `song_data` WHERE `song_id` IN $idlist";
		$db_results = Dba::read($sql);

		while ($row = Dba::fetch_assoc($db_results)) {
			parent::add_to_cache('song_data',$row['song_id'],$row);
		}

		return true;

	} // build_cache

	/**
	 * _get_info
	 * get's the vars for $this out of the database
	 * Taken from the object
	 */
	private function _get_info() {

		$id = intval($this->id);

		if (parent::is_cached('song',$id)) {
			return parent::get_from_cache('song',$id);
		}

		/* Grab the basic information from the catalog and return it */
		$sql = "SELECT song.id,file,catalog,album,year,artist,".
			"title,bitrate,rate,mode,size,time,track,played,song.enabled,update_time,selected,".
			"mbid,".
			"addition_time FROM `song` WHERE `song`.`id` = '$id'";
		$db_results = Dba::read($sql);

		$results = Dba::fetch_assoc($db_results);

		parent::add_to_cache('song',$id,$results);

		return $results;

	} // _get_info

	/**
 	 * _get_ext_info
	 * This function gathers information from the song_ext_info table and adds it to the
	 * current object
	 */
	public function _get_ext_info() {

		$id = intval($this->id);

		if (parent::is_cached('song_data',$id)) {
			return parent::get_from_cache('song_data',$id);
		}

		$sql = "SELECT * FROM song_data WHERE `song_id`='$id'";
		$db_results = Dba::read($sql);

		$results = Dba::fetch_assoc($db_results);

		parent::add_to_cache('song_data',$id,$results);

		return $results;

	} // _get_ext_info

	/**
 	 * fill_ext_info
	 * This calls the _get_ext_info and then sets the correct vars
	 */
	public function fill_ext_info() {

		$info = $this->_get_ext_info();

		foreach ($info as $key=>$value) {
			if ($key != 'song_id') {
				$this->$key = $value;
			}
		} // end foreach

	} // fill_ext_info

	/**
	 * format_type
	 * gets the type of song we are trying to
	 * play, used to set mime headers and to trick
	 * players into playing them correctly
	 */
	public function format_type($override='') {

		// If we pass an override for downsampling or whatever then use it
		if (!empty($override)) {
			$this->type = $override;
		}
		else {
			$data = pathinfo($this->file);
			$this->type = strtolower($data['extension']);
		}

		switch ($this->type) {
			case 'spx':
			case 'ogg':
				$this->mime = "application/ogg";
			break;
			case 'wma':
			case 'asf':
				$this->mime = "audio/x-ms-wma";
			break;
			case 'mp3':
			case 'mpeg3':
				$this->mime = "audio/mpeg";
			break;
			case 'rm':
			case 'ra':
				$this->mime = "audio/x-realaudio";
			break;
			case 'flac';
				$this->mime = "audio/x-flac";
			break;
			case 'wv':
				$this->mime = 'audio/x-wavpack';
			break;
			case 'aac':
			case 'mp4':
			case 'm4a':
				$this->mime = "audio/mp4";
			break;
			case 'mpc':
				$this->mime = "audio/x-musepack";
			break;
			default:
				$this->mime = "audio/mpeg";
			break;
		}

		return true;

	} // format_type

	/**
	 * get_album_name
	 * gets the name of $this->album, allows passing of id
	 */
	public function get_album_name($album_id=0) {
		if (!$album_id) { $album_id = $this->album; }
	  	$album = new Album($album_id);
		if ($album->prefix)
		  return $album->prefix . " " . $album->name;
		else
		  return $album->name;
	} // get_album_name

	/**
	 * get_artist_name
	 * gets the name of $this->artist, allows passing of id
	 */
	public function get_artist_name($artist_id=0) {

		if (!$artist_id) { $artist_id = $this->artist; }
		$artist = new Artist($artist_id);
		if ($artist->prefix)
		  return $artist->prefix . " " . $artist->name;
		else
		  return $artist->name;

	} // get_album_name

	/**
	 * has_flag
	 * This just returns true or false depending on if this song is flagged for something
	 * We don't care what so we limit the SELECT to 1
	 */
	public function has_flag() {

		$sql = "SELECT `id` FROM `flagged` WHERE `object_type`='song' AND `object_id`='$this->id' LIMIT 1";
		$db_results = Dba::read($sql);

		if (Dba::fetch_assoc($db_results)) {
			return true;
		}

		return false;

	} // has_flag

	/**
	 * set_played
	 * this checks to see if the current object has been played
	 * if not then it sets it to played
	 */
	public function set_played() {

		if ($this->played) {
			return true;
		}

		/* If it hasn't been played, set it! */
		self::update_played('1',$this->id);

		return true;

	} // set_played

	/**
	 * compare_song_information
	 * this compares the new ID3 tags of a file against
	 * the ones in the database to see if they have changed
	 * it returns false if nothing has changes, or the true
	 * if they have. Static because it doesn't need this
	 */
	public static function compare_song_information($song,$new_song) {

		// Remove some stuff we don't care about
		unset($song->catalog,$song->played,$song->enabled,$song->addition_time,$song->update_time,$song->type);

		$string_array = array('title','comment','lyrics');
		$skip_array = array('id','tag_id','mime','mb_artistid','mbid');

		// Pull out all the currently set vars
		$fields = get_object_vars($song);

		// Foreach them
		foreach ($fields as $key=>$value) {
			if (in_array($key,$skip_array)) { continue; }
			// If it's a stringie thing
			if (in_array($key,$string_array)) {
				if (trim(stripslashes($song->$key)) != trim(stripslashes($new_song->$key))) {
					$array['change'] = true;
					$array['element'][$key] = 'OLD: ' . $song->$key . ' --> ' . $new_song->$key;
				}
			} // in array of stringies
			else {
				if ($song->$key != $new_song->$key) {
					$array['change'] = true;
					$array['element'][$key] = 'OLD:' . $song->$key . ' --> ' . $new_song->$key;
				}
			} // end else

		} // end foreach

		if ($array['change']) {
			debug_event('song-diff',print_r($array['element'],1),'5','ampache-catalog');
		}

		return $array;

	} // compare_song_information


	/**
	 * update
	 * This takes a key'd array of data does any cleaning it needs to
	 * do and then calls the helper functions as needed. This will also
	 * cause the song to be flagged
	 */
	public function update($data) {

		foreach ($data as $key=>$value) {
			switch ($key) {
				case 'artist':
					// Don't do anything if we've negative one'd this baby
					if ($value == '-1') {
						$value = Catalog::check_artist($data['artist_name'], $data['mb_artistid']);
					}
				case 'album':
					if ($value == '-1') {
						$value = Catalog::check_album($data['album_name'], $data['year'], $data['disk'], $data['mb_albumid']);
					}
				case 'title':
				case 'track':
					// Check to see if it needs to be updated
					if ($value != $this->$key) {
						$function = 'update_' . $key;
						self::$function($value,$this->id);
						$this->$key = $value;
						$updated = 1;
					}
				break;
				default:
					// Rien a faire
				break;
			} // end whitelist
		} // end foreach

		// If a field was changed then we need to flag this mofo
		if ($updated) {
			Flag::add($this->id,'song','retag','Interface Update');
		}

		return true;

	} // update
	
	/**
	 * delete_song
	 * This cleans up art that no longer has a corresponding object
	 */
	public function delete_song($song_id) {	
			$song = new Song($song_id);
			$song->format();
			
			$artist_id = $song->artist;
			$artist = new Artist($artist_id);
			Artist::is_delete_artist($artist_id);
			
			$song_path = $song->file;
			unlink ($song_path);
			
			$sql = "DELETE FROM `song` WHERE `id`= '$song_id'";
			$db_results = Dba::write($sql);
			
			$sql = "DELETE FROM `song_data` WHERE `song_id`= '$song_id'";
			$db_results = Dba::write($sql);
			
			Song::is_dir_to_delete($song_path);
	} // delete_song	
	
	/* Delete directory */
	public static function is_dir_to_delete($dir){ 
		$filename = substr(strrchr($dir, "/"), 1);
		$dir = str_replace($filename, '', $dir);
		
		$objects = scandir($dir);
		$oth_ext = array();
		foreach ($objects as $object) { 
			$ext = pathinfo($object, PATHINFO_EXTENSION);
			if ($ext != "mp3") { $oth_ext[] = $ext; }
		} 
		
		$no_mp3 = count($oth_ext);		
		if ((count($objects)) <= $no_mp3) {
			Song::delete_dir($dir);
		}	   
	} 	
	
	/* Delete directory */	
	public function delete_dir($dir) {
		if (is_dir($dir)) { 
			$objects = scandir($dir); 
			foreach ($objects as $object) { 
				if ($object != "." && $object != "..") { 
			    	if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); 
			    	else unlink($dir."/".$object); 
			     } 
			 } 
			 reset($objects); 
			 rmdir($dir); 
		} 
	} // delete link
	


	
	/**
	 * update_song
	 * this is the main updater for a song it actually
	 * calls a whole bunch of mini functions to update
	 * each little part of the song... lastly it updates
	 * the "update_time" of the song
	 */
	public static function update_song($song_id, $new_song) {

		$title 		= Dba::escape($new_song->title);
		$bitrate	= Dba::escape($new_song->bitrate);
		$rate		= Dba::escape($new_song->rate);
		$mode		= Dba::escape($new_song->mode);
		$size		= Dba::escape($new_song->size);
		$time		= Dba::escape($new_song->time);
		$track		= Dba::escape($new_song->track);
		$mbid		= Dba::escape($new_song->mbid);
		$artist		= Dba::escape($new_song->artist);
		$album		= Dba::escape($new_song->album);
		$year		= Dba::escape($new_song->year);
		$song_id	= Dba::escape($song_id);
		$update_time	= time();


		$sql = "UPDATE `song` SET `album`='$album', `year`='$year', `artist`='$artist', " .
			"`title`='$title', `bitrate`='$bitrate', `rate`='$rate', `mode`='$mode', " .
			"`size`='$size', `time`='$time', `track`='$track', " .
			"`mbid`='$mbid', " .
			"`update_time`='$update_time' WHERE `id`='$song_id'";
		$db_results = Dba::write($sql);


		$comment 	= Dba::escape($new_song->comment);
		$language	= Dba::escape($new_song->language);
		$lyrics		= Dba::escape($new_song->lyrics);

		$sql = "UPDATE `song_data` SET `lyrics`='$lyrics', `language`='$language', `comment`='$comment' " .
			"WHERE `song_id`='$song_id'";
		$db_results = Dba::write($sql);

	} // update_song

	/**
	 * update_year
	 * update the year tag
	 */
	public static function update_year($new_year,$song_id) {

		self::_update_item('year',$new_year,$song_id,'50');

	} // update_year

	/**
	 * update_language
	 * This updates the language tag of the song
	 */
	public static function update_language($new_lang,$song_id) {

		self::_update_ext_item('language',$new_lang,$song_id,'50');

	} // update_language

	/**
	 * update_comment
	 * updates the comment field
	 */
	public static function update_comment($new_comment,$song_id) {

		self::_update_ext_item('comment',$new_comment,$song_id,'50');

	} // update_comment

	/**
 	 * update_lyrics
	 * updates the lyrics field
	 */
	public static function update_lyrics($new_lyrics,$song_id) {

		self::_update_ext_item('lyrics',$new_lyrics,$song_id,'50');

	} // update_lyrics

	/**
	 * update_title
	 * updates the title field
	 */
	public static function update_title($new_title,$song_id) {

		self::_update_item('title',$new_title,$song_id,'50');

	} // update_title

	/**
	 * update_bitrate
	 * updates the bitrate field
	 */
	public static function update_bitrate($new_bitrate,$song_id) {

		self::_update_item('bitrate',$new_bitrate,$song_id,'50');

	} // update_bitrate

	/**
	 * update_rate
	 * updates the rate field
	 */
	public static function update_rate($new_rate,$song_id) {

		self::_update_item('rate',$new_rate,$song_id,'50');

	} // update_rate

	/**
	 * update_mode
	 * updates the mode field
	 */
	public static function update_mode($new_mode,$song_id) {

		self::_update_item('mode',$new_mode,$song_id,'50');

	} // update_mode

	/**
	 * update_size
	 * updates the size field
	 */
	public static function update_size($new_size,$song_id) {

		self::_update_item('size',$new_size,$song_id,'50');

	} // update_size

	/**
	 * update_time
	 * updates the time field
	 */
	public static function update_time($new_time,$song_id) {

		self::_update_item('time',$new_time,$song_id,'50');

	} // update_time

	/**
	 * update_track
	 * this updates the track field
	 */
	public static function update_track($new_track,$song_id) {

		self::_update_item('track',$new_track,$song_id,'50');

	} // update_track

	public static function update_mbid($new_mbid,$song_id) {

		self::_update_item('mbid',$new_mbid,$song_id,'50');

	} // update_mbid

	/**
	 * update_artist
	 * updates the artist field
	 */
	public static function update_artist($new_artist,$song_id) {

		self::_update_item('artist',$new_artist,$song_id,'50');

	} // update_artist

	/**
	 * update_album
	 * updates the album field
	 */
	public static function update_album($new_album,$song_id) {

		self::_update_item('album',$new_album,$song_id,'50');

	} // update_album

	/**
	 * update_utime
	 * sets a new update time
	 */
	public static function update_utime($song_id,$time=0) {

		if (!$time) { $time = time(); }

		self::_update_item('update_time',$time,$song_id,'75');

	} // update_utime

	/**
	 * update_played
	 * sets the played flag
	 */
	public static function update_played($new_played,$song_id) {

		self::_update_item('played',$new_played,$song_id,'25');

	} // update_played

	/**
	 * update_enabled
	 * sets the enabled flag
	 */
	public static function update_enabled($new_enabled,$song_id) {

		self::_update_item('enabled',$new_enabled,$song_id,'75');

	} // update_enabled

	/**
	 * _update_item
	 * This is a private function that should only be called from within the song class.
	 * It takes a field, value song id and level. first and foremost it checks the level
	 * against $GLOBALS['user'] to make sure they are allowed to update this record
	 * it then updates it and sets $this->{$field} to the new value
	 */
	private static function _update_item($field,$value,$song_id,$level) {

		/* Check them Rights! */
		if (!Access::check('interface',$level)) { return false; }

		/* Can't update to blank */
		if (!strlen(trim($value)) && $field != 'comment') { return false; }

		$value = Dba::escape($value);

		$sql = "UPDATE `song` SET `$field`='$value' WHERE `id`='$song_id'";
		$db_results = Dba::write($sql);

		return true;

	} // _update_item

	/**
	 * _update_ext_item
	 * This updates a song record that is housed in the song_ext_info table
	 * These are items that aren't used normally, and often large/informational only
	 */
	private static function _update_ext_item($field,$value,$song_id,$level) {

		/* Check them rights boy! */
		if (!Access::check('interface',$level)) { return false; }

		$value = Dba::escape($value);

		$sql = "UPDATE `song_data` SET `$field`='$value' WHERE `song_id`='$song_id'";
		$db_results = Dba::write($sql);

		return true;

	} // _update_ext_item

	/**
	 * format
	 * This takes the current song object
	 * and does a ton of formating on it creating f_??? variables on the current
	 * object
	 */
	public function format() {

		$this->fill_ext_info();

		// Format the filename
		preg_match("/^.*\/(.*?)$/",$this->file, $short);
		$this->f_file = htmlspecialchars($short[1]);

		// Format the album name
		$this->f_album_full = $this->get_album_name();
		$this->f_album = truncate_with_ellipsis($this->f_album_full,Config::get('ellipse_threshold_album'));

		// Format the artist name
		$this->f_artist_full = $this->get_artist_name();
		$this->f_artist = truncate_with_ellipsis($this->f_artist_full,Config::get('ellipse_threshold_artist'));

		// Format the title
		$this->f_title = truncate_with_ellipsis($this->title,Config::get('ellipse_threshold_title'));

		// Create Links for the different objects
		$this->link = Config::get('web_path') . "/song.php?action=show_song&song_id=" . $this->id;
		$this->f_link = "<a href=\"" . scrub_out($this->link) . "\" title=\"" . scrub_out($this->title) . "\"> " . scrub_out($this->f_title) . "</a>";
		$this->f_album_link = "<a href=\"" . Config::get('web_path') . "/albums.php?action=show&amp;album=" . $this->album . "\" title=\"" . scrub_out($this->f_album_full) . "\"> " . scrub_out($this->f_album) . "</a>";
		$this->f_artist_link = "<a href=\"" . Config::get('web_path') . "/artists.php?action=show&amp;artist=" . $this->artist . "\" title=\"" . scrub_out($this->f_artist_full) . "\"> " . scrub_out($this->f_artist) . "</a>";

		// Format the Bitrate
		$this->f_bitrate = intval($this->bitrate/1000) . "-" . strtoupper($this->mode);

		// Format the Time
		$min = floor($this->time/60);
		$sec = sprintf("%02d", ($this->time%60) );
		$this->f_time = $min . ":" . $sec;

		// Format the track (there isn't really anything to do here)
		$this->f_track = $this->track;

		// Get the top tags
		$tags = Tag::get_top_tags('song',$this->id);
		$this->tags = $tags;

		$this->f_tags = Tag::get_display($tags,$this->id,'song');

		// Format the size
		$this->f_size = format_bytes($this->size);

		return true;

	} // format

	/**
	 * format_pattern
	 * This reformats the song information based on the catalog
	 * rename patterns
	 */
	public function format_pattern() {

		$extension = ltrim(substr($this->file,strlen($this->file)-4,4),".");

		$catalog = new Catalog($this->catalog);

		// If we don't have a rename pattern then just return it
		if (!trim($catalog->rename_pattern)) {
			$this->f_pattern	= $this->title;
			$this->f_file		= $this->title . '.' . $extension;
			return;
		}

		/* Create the filename that this file should have */
		$album  = $this->f_album_full;
		$artist = $this->f_artist_full;
		$track  = sprintf('%02d', $this->track);
		$title  = $this->title;
		$year   = $this->year;

		/* Start replacing stuff */
		$replace_array = array('%a','%A','%t','%T','%y','/','\\');
		$content_array = array($artist,$album,$title,$track,$year,'-','-');

		$rename_pattern = str_replace($replace_array,$content_array,$catalog->rename_pattern);

		$rename_pattern = preg_replace("[\-\:\!]","_",$rename_pattern);

		$this->f_pattern	= $rename_pattern;
		$this->f_file 		= $rename_pattern . "." . $extension;

	} // format_pattern

	/**
	 * get_fields
	 * This returns all of the 'data' fields for this object, we need to filter out some that we don't
	 * want to present to a user, and add some that don't exist directly on the object but are related
	 */
	public static function get_fields() {

		$fields = get_class_vars('Song');

		unset($fields['id'],$fields['_transcoded'],$fields['_fake'],$fields['cache_hit'],$fields['mime'],$fields['type']);

		// Some additional fields
		$fields['tag'] = true;
		$fields['catalog'] = true;
//FIXME: These are here to keep the ideas, don't want to have to worry about them for now
//		$fields['rating'] = true;
//		$fields['recently Played'] = true;

		return $fields;

	} // get_fields

	/**
	 * get_from_path
	 * This returns all of the songs that exist under the specified path
	 */
	public static function get_from_path($path) {

		$path = Dba::escape($path);

		$sql = "SELECT * FROM `song` WHERE `file` LIKE '$path%'";
		$db_results = Dba::read($sql);

		$songs = array();

		while ($row = Dba::fetch_assoc($db_results)) {
			$songs[] = $row['id'];
		}

		return $songs;

	} // get_from_path

	/**
	 *	@function	get_rel_path
	 *	@discussion	returns the path of the song file stripped of the catalog path
	 *			used for mpd playback
	 */
	public function get_rel_path($file_path=0,$catalog_id=0) {

		if (!$file_path) {
			$info = $this->_get_info();
			$file_path = $info->file;
		}
		if (!$catalog_id) {
			$catalog_id = $info->catalog;
		}
		$catalog = new Catalog( $catalog_id );
		$info = $catalog->_get_info();
		$catalog_path = $info->path;
		$catalog_path = rtrim($catalog_path, "/");
		return( str_replace( $catalog_path . "/", "", $file_path ) );

	} // get_rel_path


	/*!
		@function fill_info
		@discussion this takes the $results from getid3 and attempts to fill
			as much information as possible from the file name using the
			pattern set in the current catalog
	*/
	function fill_info($results,$pattern,$catalog_id,$key) {

		$filename = $this->get_rel_path($results['file'],$catalog_id);

		if (!strlen($results[$key]['title'])) {
			$results[$key]['title']		= $this->get_info_from_filename($filename,$pattern,"%t");
		}
		if (!strlen($results[$key]['track'])) {
			$results[$key]['track']		= $this->get_info_from_filename($filename,$pattern,"%T");
		}
		if (!strlen($results[$key]['year'])) {
			$results[$key]['year']		= $this->get_info_from_filename($filename,$pattern,"%y");
		}
		if (!strlen($results[$key]['album'])) {
			$results[$key]['album']		= $this->get_info_from_filename($filename,$pattern,"%A");
		}
		if (!strlen($results[$key]['artist'])) {
			$results[$key]['artist']	= $this->get_info_from_filename($filename,$pattern,"%a");
		}

		return $results;

	} // fill_info

	/*!
		@function get_info_from_filename
		@discussion get information from a filename based on pattern
	*/
	function get_info_from_filename($file,$pattern,$tag) {

		$preg_pattern = str_replace("$tag","(.+)",$pattern);
		$preg_pattern = preg_replace("/\%\w/",".+",$preg_pattern);
		$preg_pattern = "/" . str_replace("/","\/",$preg_pattern) . "\..+/";

		preg_match($preg_pattern,$file,$matches);

		return stripslashes($matches[1]);

	} // get_info_from_filename

	/**
	 * play_url
	 * This function takes all the song information and correctly formats a
	 * a stream URL taking into account the downsmapling mojo and everything
	 * else, this is the true function
	 */
	public static function play_url($oid) {

		$song = new Song($oid);
		$user_id 	= $GLOBALS['user']->id ? scrub_out($GLOBALS['user']->id) : '-1';
		$type		= $song->type;

		// Required for some versions of winamp that won't work if the
		// stream doesn't end in .ogg This will not break any properly
		// working player, don't report this as a bug!
		if ($song->type == 'flac') { $type = 'ogg'; }

		$song->format();

		$song_name = rawurlencode($song->f_artist_full . " - " . $song->title . "." . $type);

		$url = Stream::get_base_url() . "oid=$song->id&uid=$user_id&name=/$song_name";

		return $url;

	} // play_url

	/**
	 * parse_song_url
	 * Takes a URL from this ampache install and returns the song that the url represents
	 * used by the API, and used to parse out stream urls for localplay
	 * right now just gets song id might do more later, hence the complexity
	 */
	public static function parse_song_url($url) {

		// We only care about the question mark stuff
		$query = parse_url($url,PHP_URL_QUERY);

		$elements = explode("&",$query);

		foreach ($elements as $items) {
			list($key,$value) = explode("=",$items);
			if ($key == 'oid') {
				return $value;
			}
		} // end foreach

		return false;

	} // parse_song_url

	/**
	 * get_recently_played
	 * This function returns the last X songs that have been played
	 * it uses the popular threshold to figure out how many to pull
	 * it will only return unique object
	 */
	public static function get_recently_played($user_id='') {

		$user_id = Dba::escape($user_id);

		$sql = "SELECT `object_id`, `user`, `object_type`, `date` " .
			"FROM `object_count` WHERE `object_type`='song' ";
		if ($user_id) {
			$sql .= "AND `user`='$user_id' ";
		}
		$sql .= "ORDER BY `date` DESC ";
		$db_results = Dba::read($sql);

		$results = array();

		while ($row = Dba::fetch_assoc($db_results)) {
			if (isset($results[$row['object_id']])) { continue; }
			$results[$row['object_id']] = $row;
			if (count($results) >= Config::get('popular_threshold')) { break; }
		}

		return $results;

	} // get_recently_played

	/**
	 * native_stream
	 * This returns true/false if this can be natively streamed
	 */
	public function native_stream() {

		if ($this->_transcoded) { return false; }

		$conf_var 	= 'transcode_' . $this->type;

		if (Config::get($conf_var)) {
			$this->set_transcode();
			return false;
		}

		return true;

	} // end native_stream

	/**
	 * set_transcode
	 *
	 * We want to transcode, set up the variables correctly
	 */
	public function set_transcode($override='') {
		if ($this->_transcoded) { return; }

		$conf_type      = empty($override) ? Config::get('transcode_' . $this->type . '_target') : $override;
		$conf_cmd       = 'transcode_cmd_' . $this->type . (empty($override) ? '' : '_'.$override);

		debug_event('transcode', 'Search for cmd ' . $conf_cmd, 5);

		$this->_transcoded = true;
		$this->transcoded_from = $this->type;
		$this->_transcode_cmd = Config::get($conf_cmd);
		$this->format_type($conf_type);
		if ($this->type == $this->transcoded_from) {
			$this->_resampled = true;
		}

		debug_event('transcode', 'Transcoding from ' . 
			$this->transcoded_from . ' to ' . $this->type . ' using command ' . $this->_transcode_cmd, 5);
	}

	/**
	 * stream_cmd
	 *
	 * test if the song type streams natively and
	 * if not returns a transcoding command from the config
	 */
	public function stream_cmd() {

		if ($this->native_stream()) {
			return null;
		}
		
		return $this->_transcode_cmd;

	} // end stream_cmd
	
	
	/**
	 * update_selected
	 * sets the enabled selected
	 */
	public static function update_selected_song($new_selected,$song_id) {

		self::_update_item_song('selected',$new_selected,$song_id,'25');

	} // update_enabled

	/**
	 * _update_item
	 * This is a private function that should only be called from within the song class.
	 * It takes a field, value song id and level. first and foremost it checks the level
	 * against $GLOBALS['user'] to make sure they are allowed to update this record
	 * it then updates it and sets $this->{$field} to the new value
	 */
	private static function _update_item_song($field,$value,$song_id,$level) {

		/* Check them Rights! */
		if (!Access::check('interface',$level)) { return false; }

		$value = Dba::escape($value);

		$sql = "UPDATE `song` SET `$field`='$value' WHERE `id`='$song_id'";
		$db_results = Dba::write($sql);

		return true;

	} // _update_item


} // end of song class
?>
