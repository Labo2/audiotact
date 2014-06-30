<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * TmpPlaylist Class
 *
 *
 * LICENSE: GNU General Public License, version 2 (GPLv2)
 * Copyright (c) 2001 - 2011 Ampache.org All Rights Reserved
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License v2
 * as published by the Free Software Foundation
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
 * TempPlaylist Class
 *
 * This class handles the temporary playlists in ampache, it handles the
 * tmp_playlist and tmp_playlist_data tables, and sneaks out at night to
 * visit user_vote from time to time
 *
 * @package	Ampache
 * @copyright	2001 - 2011 Ampache.org
 * @license	http://opensource.org/licenses/gpl-2.0 GPLv2
 * @link	http://www.ampache.org/
 */
class tmpPlaylist extends database_object {

	/* Variables from the Datbase */
	public $id;
	public $session;
	public $type;
	public $object_type;
	public $base_playlist;

	/* Generated Elements */
	public $items = array();

	/**
	 * Constructor
	 * This takes a playlist_id as an optional argument and gathers the
	 * information.  If no playlist_id is passed or the requested one isn't
	 * found, return false.
	 */
	public function __construct($playlist_id='') {

		if (!$playlist_id) { return false; }

		$this->id 	= intval($playlist_id);
		$info 		= $this->_get_info();

		foreach ($info as $key=>$value) {
			$this->$key = $value;
		}

		return true;

	} // __construct

	/**
	 * _get_info
	 * This is an internal (private) function that gathers the information
	 * for this object from the playlist_id that was passed in.
	 */
	private function _get_info() {

		$sql = "SELECT * FROM `tmp_playlist` WHERE `id`='" . Dba::escape($this->id) . "'";
		$db_results = Dba::read($sql);

		$results = Dba::fetch_assoc($db_results);

		return $results;

	} // _get_info

	/**
	 * get_from_session
	 * This returns a playlist object based on the session that is passed to
	 * us.  This is used by the load_playlist on user for the most part.
	 */
	public static function get_from_session($session_id) {

		$session_id = Dba::escape($session_id);

		$sql = "SELECT `id` FROM `tmp_playlist` WHERE `session`='$session_id'";
		$db_results = Dba::read($sql);

		$results = Dba::fetch_row($db_results);

		if (!$results['0']) {
			$results['0'] = tmpPlaylist::create(array(
				'session_id'  => $session_id,
				'type'        => 'user',
				'object_type' => 'song'
			));
		}

		$playlist = new tmpPlaylist($results['0']);

		return $playlist;

	} // get_from_session

	/**
	 * get_from_userid
	 * This returns a tmp playlist object based on a userid passed
	 * this is used for the user profiles page
	 */
	public static function get_from_userid($user_id) {

		// This is a little stupid, but because we don't have the 
		// user_id in the session or in the tmp_playlist table we have 
		// to do it this way.
		$client = new User($user_id);
		$username = Dba::escape($client->username);

		$sql = "SELECT `tmp_playlist`.`id` FROM `tmp_playlist` " .
			"LEFT JOIN `session` ON " .
			"`session`.`id`=`tmp_playlist`.`session` " .
			"WHERE `session`.`username`='$username' " .
			"ORDER BY `session`.`expire` DESC";
		$db_results = Dba::read($sql);

		$data = Dba::fetch_assoc($db_results);

		return $data['id'];

	} // get_from_userid

	/**
	 * get_items
	 * Returns an array of all object_ids currently in this tmpPlaylist.
	 * This has gotten a little more complicated because of type, the values
	 * are an array (0 being ID, 1 being TYPE).
	 */
	public function get_items() {

		$id = Dba::escape($this->id);

		/* Select all objects from this playlist */
		$sql = "SELECT `object_type`, `id`, `object_id`,`track` " .
			"FROM `tmp_playlist_data` " .
			"WHERE `tmp_playlist`='$id' ORDER BY `track` ASC";
		$db_results = Dba::read($sql);

		/* Define the array */
		$items = array();

		while ($results = Dba::fetch_assoc($db_results)) {
			$key		= $results['id'];
			$items[$key] 	= array($results['object_type'],
				$results['object_id'],$results['track']);
		}

		return $items;

	} // get_items

	/**
	 * get_next_object
	 * This returns the next object in the tmp_playlist.  Most of the time
	 * this will just be the top entry, but if there is a base_playlist and
	 * no items in the playlist then it returns a random entry from the
	 * base_playlist
	 */
	public function get_next_object() {

		$id = Dba::escape($this->id);

		$sql = "SELECT `object_id` FROM `tmp_playlist_data` " .
			"WHERE `tmp_playlist`='$id' ORDER BY `id` LIMIT 1";
		$db_results = Dba::read($sql);

		$results = Dba::fetch_assoc($db_results);

		return $results['object_id'];

	} // get_next_object

	/**
	 * count_items
	 * This returns a count of the total number of tracks that are in this
	 * tmp playlist
	 */
	public function count_items() {

		$id = Dba::escape($this->id);

		$sql = "SELECT COUNT(`id`) FROM `tmp_playlist_data` WHERE " .
			"`tmp_playlist`='$id'";
		$db_results = Dba::read($sql);

		$results = Dba::fetch_row($db_results);

		return $results['0'];

	} // count_items

	/**
 	 * clear
	 * This clears all the objects out of a single playlist
	 */
	public function clear() {

		$id = Dba::escape($this->id);

		$sql = "SELECT `object_id` FROM `tmp_playlist_data` WHERE `tmp_playlist`='$id'";
		$db_results = Dba::read($sql);
		
		$items = array();
		while ($results = Dba::fetch_assoc($db_results)) {$items[] = $results['object_id'];}

		foreach($items as $song) {
			$sql_song = "UPDATE `song` SET `selected`='0' WHERE `song`.`id`='$song'";
			$db_results = Dba::write($sql_song);
		}
		
		$sql = "DELETE FROM `tmp_playlist_data` WHERE " .
			"`tmp_playlist`='$id'";
		$db_results = Dba::write($sql);

		return true;

	} // clear
	
	/**
 	 * clear_tmp
	 * This clears all the objects out of a single playlist
	 */
	public function clear_tmp() {

		$id = Dba::escape($this->id);
		
		$sql = "DELETE FROM `tmp_playlist_data` WHERE " .
			"`tmp_playlist`='$id'";
		$db_results = Dba::write($sql);

		return true;

	} // clear


	/**
	 * create
	 * This function initializes a new tmpPlaylist. It is associated with
	 * the current session rather than a user, as you could have the same 
	 * user logged in from multiple locations.
	 */
	public static function create($data) {

		$sessid 	= Dba::escape($data['session_id']);
		$type		= Dba::escape($data['type']);
		$object_type	= Dba::escape($data['object_type']);

		$sql = "INSERT INTO `tmp_playlist` " .
			"(`session`,`type`,`object_type`) " .
			" VALUES ('$sessid','$type','$object_type')";
		$db_results = Dba::write($sql);

		$id = Dba::insert_id();

		/* Clean any other playlists associated with this session */
		self::session_clean($sessid, $id);

		return $id;

	} // create

	/**
	 * update_playlist
	 * This updates the base_playlist on this tmp_playlist
	 */
	public function update_playlist($playlist_id) {

		$playlist_id 	= Dba::escape($playlist_id);
		$id		= Dba::escape($this->id);

		$sql = "UPDATE `tmp_playlist` SET " .
			"`base_playlist`='$playlist_id' WHERE `id`='$id'";
		$db_results = Dba::write($sql);

		return true;

	} // update_playlist

	/**
	 * add_info_playlist
	 * This updates the base_playlist on this tmp_playlist
	 */
	public function add_info_playlist($id,$name,$genre) {

		$sql = "UPDATE `tmp_playlist` SET `playlist_name`='$name', `playlist_genre`='$genre' WHERE `session`='$id'";
		$db_results = Dba::write($sql);

		return true;

	} // update_playlist
	
	/**
	 * update_order
	 * This deletes the current playlist and all associated data
	 */
	public function update_order($order,$song_id,$playlist_id) {
		$sql = "UPDATE `tmp_playlist_data` SET `track`='$order' WHERE `object_id`='$song_id' AND `tmp_playlist`='$playlist_id'";
		$db_results = Dba::write($sql);
		return true;
	} // delete

	
	/**
	 * session_clean
	 * This deletes any other tmp_playlists associated with this
	 * session
	 */
	public static function session_clean($sessid, $id) {

		$sessid = Dba::escape($sessid);
		$id	= Dba::escape($id);

		$sql = "DELETE FROM `tmp_playlist` WHERE `session`='$sessid' " .
			"AND `id` != '$id'";
		$db_results = Dba::write($sql);

		/* Remove associated tracks */
		self::prune_tracks();

		return true;

	} // session_clean

	/**
	 * clean
	 * This cleans up old data
	 */
	public static function clean() {
		self::prune_playlists();
		self::prune_tracks();
	}

	/**
	 * prune_playlists
	 * This deletes any playlists that don't have an associated session
	 */
	public static function prune_playlists() {

		/* Just delete if no matching session row */
		$sql = "DELETE FROM `tmp_playlist` USING `tmp_playlist` " .
			"LEFT JOIN `session` " .
			"ON `session`.`id`=`tmp_playlist`.`session` " .
			"WHERE `session`.`id` IS NULL " .
			"AND `tmp_playlist`.`type` != 'vote'";
		$db_results = Dba::write($sql);

		return true;

	} // prune_playlists

	/**
	 * prune_tracks
	 * This prunes tracks that don't have playlists or don't have votes
	 */
	public static function prune_tracks() {

		// This prune is always run and clears data for playlists that 
		// don't exist anymore
		$sql = "DELETE FROM `tmp_playlist_data` USING " .
			"`tmp_playlist_data` LEFT JOIN `tmp_playlist` ON " .
			"`tmp_playlist_data`.`tmp_playlist`=`tmp_playlist`.`id` " .
			"WHERE `tmp_playlist`.`id` IS NULL";
		$db_results = Dba::write($sql);

	} // prune_tracks

	/**
	 * add_object
	 * This adds the object of $this->object_type to this tmp playlist
	 * it takes an optional type, default is song
	 */
	public function add_object($object_id,$object_type) {

		$object_id 	= Dba::escape($object_id);
		$playlist_id 	= Dba::escape($this->id);
		$object_type	= $object_type ? Dba::escape($object_type) : 'song';

		$sql_song = "UPDATE `song` SET `selected`='1' WHERE `song`.`id`='$object_id'";
		$db_results = Dba::write($sql_song);
		
		$sql = "INSERT INTO `tmp_playlist_data` " .
			"(`object_id`,`tmp_playlist`,`object_type`) " .
			" VALUES ('$object_id','$playlist_id','$object_type')";
		$db_results = Dba::write($sql);
		
		

		return true;

	} // add_object

	/**
	 * vote_active
	 * This checks to see if this playlist is a voting playlist
	 * and if it is active
	 */
	public function vote_active() {

		/* Going to do a little more here later */
		if ($this->type == 'vote') { return true; }

		return false;

	} // vote_active

	/**
	 * delete_track
	 * This deletes a track from the tmpplaylist
	 */
	public function delete_track($id) {

		$id 	= Dba::escape($id);
		
		$sql = "SELECT `object_id` FROM `tmp_playlist_data` WHERE `id`='$id'";
		$db_results = Dba::read($sql);
		$results = Dba::fetch_assoc($db_results);
		$object_id = $results['object_id'];
		
		$sql_song = "UPDATE `song` SET `selected`='0' WHERE `song`.`id`='$object_id'";
		$db_results = Dba::write($sql_song);

		/* delete the track its self */
		$sql = "DELETE FROM `tmp_playlist_data` WHERE `id`='$id'";
		$db_results = Dba::write($sql);

		return true;

	} // delete_track

} // class tmpPlaylist
