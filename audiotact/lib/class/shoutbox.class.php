<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * shoutBox Class
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
 * shoutBox Class
 *
 * Description here...
 *
 * @package	Ampache
 * @copyright	2001 - 2011 Ampache.org
 * @license	http://opensource.org/licenses/gpl-2.0 GPLv2
 * @link	http://www.ampache.org/
 */
class shoutBox {

	public $id;
	public $sticky;

	/**
	 * Constructor
	 * This pulls the shoutbox information from the database and returns
	 * a constructed object, uses user_shout table
	 */
	public function __construct($shout_id) {

		// Load the data from the database
		$this->_get_info($shout_id);

		return true;

	} // Constructor

	/**
	 * _get_info
	 * does the db call, reads from the user_shout table
	 */
	private function _get_info($shout_id) {

		$sticky_id = Dba::escape($shout_id);

		$sql = "SELECT * FROM `user_shout` WHERE `id`='$shout_id'";
		$db_results = Dba::read($sql);

		$data = Dba::fetch_assoc($db_results);

		foreach ($data as $key=>$value) {
			$this->$key = $value;
		}

		return true;

	} // _get_info

	/**
	 * get_top
	 * This returns the top user_shouts, shoutbox objects are always shown regardless and count against the total
	 * number of objects shown
	 */
	public static function get_top($limit) {

		$shouts = self::get_sticky();

		// If we've already got too many stop here
		if (count($shouts) > $limit) {
			$shouts = array_slice($shouts,0,$limit);
			return $shouts;
		}

		// Only get as many as we need
		$limit = intval($limit) - count($shouts);
		$sql = "SELECT * FROM `user_shout` WHERE `sticky`='0' ORDER BY `date` DESC LIMIT $limit";
		$db_results = Dba::read($sql);

		while ($row = Dba::fetch_assoc($db_results)) {
			$shouts[] = $row['id'];
		}

		return $shouts;

	} // get_top
	
	
	/**
	 * get_shoutbox_form_album
	 */
	public static function get_from_album($album_id) {
		$sql = "SELECT * FROM `user_shout` WHERE `object_id`=$album_id AND `sticky`='1' ORDER BY `date` DESC";
		$db_results = Dba::read($sql);

		while ($row = Dba::fetch_assoc($db_results)) {
			$shouts[] = $row['id'];
		}

		return $shouts;

	} // get_shoutbox_form_album


	/**
	 * get_sticky
	 * This returns all current sticky shoutbox items
	 * Item valid
	 */
	public static function get_sticky() {

		$sql = "SELECT * FROM `user_shout` WHERE `sticky`='1' ORDER BY `date` DESC";
		$db_results = Dba::read($sql);

		$results = array();

		while ($row = Dba::fetch_assoc($db_results)) {
			$results[] = $row['id'];
		}

		return $results;

	} // get_sticky
	
	/**
	 * get_sticky_false
	 * En attente de validation
	 */
	public static function get_sticky_false() {

		$sql = "SELECT * FROM `user_shout` WHERE `sticky`='0' OR `sticky`='2' OR `sticky`='3' ORDER BY `date` DESC";
		$db_results = Dba::read($sql);

		$results = array();

		while ($row = Dba::fetch_assoc($db_results)) {
			$results[] = $row['id'];
		}

		return $results;

	} // get_sticky_false


	/**
	 * get_object
	 * This takes a type and an ID and returns a created object
	 */
	public static function get_object($type,$object_id) {

		$allowed_objects = array('song','genre','album','artist','radio');

		if (!in_array($type,$allowed_objects)) {
			return false;
		}

		$object = new $type($object_id);

		return $object;

	} // get_object

	/**
	 * get_image
	 * This returns an image tag if the type of object we're currently rolling with
	 * has an image associated with it
	 */
	public function get_image() {

		switch ($this->object_type) {
			case 'album':
				$image_string = "<img class=\"shoutboximage\" height=\"75\" width=\"75\" src=\"" . Config::get('web_path') . "/image.php?id=" . $this->object_id . "&amp;thumb=1\" />";
			break;
			case 'artist':

			break;
			case 'song':
				$song = new Song($this->object_id);
				$image_string = "<img class=\"shoutboximage\" height=\"75\" width=\"75\" src=\"" . Config::get('web_path') . "/image.php?id=" . $song->album . "&amp;thumb=1\" />";
			break;
			default:
				// Rien a faire
			break;
		} // end switch

		return $image_string;

	} // get_image

	/**
	 * create
	 * This takes a key'd array of data as input and inserts a new shoutbox entry, it returns the auto_inc id
	 */
	public static function create($data) {
		
		$user 		= Dba::escape(strip_tags($data['pseudonyme']));
		$text 		= Dba::escape(strip_tags($data['comment']));
		$date 		= time();
		$sticky 	= make_bool($data['sticky']);
		$object_id 	= Dba::escape($data['object_id']);
		$object_type	= Dba::escape($data['object_type']);

		$sql = "INSERT INTO `user_shout` (`user`,`date`,`text`,`sticky`,`object_id`,`object_type`) " .
			"VALUES ('$user','$date','$text','$sticky','$object_id','$object_type')";
		$db_results = Dba::write($sql);

		$insert_id = Dba::insert_id();

		return $insert_id;

	} // create

	/**
	 * update
	 * This takes a key'd array of data as input and updates a shoutbox entry
	 */
	public static function update($data) {

		$id		= Dba::escape($data['shout_id']);
		$text 		= Dba::escape(strip_tags($data['comment']));
		$sticky 	= make_bool($data['sticky']);

		$sql = "UPDATE `user_shout` SET `text`='$text', `sticky`='$sticky' WHERE `id`='$id'";
		$db_results = Dba::write($sql);

		return true;

	} // update
	
	

	/**
	 * format
	 * this function takes the object and reformats some values
	 */

	public function format() {
		$this->sticky = ($this->sticky == "0") ? 'No' : 'Yes';
		$this->date = date("m\/d\/Y - H:i", $this->date);
		return true;

	} //format

	/**
	 * update_comment_text
	 * This takes a key'd array of data as input and updates a shoutbox entry
	 */
	public static function update_comment_text($text, $shout_id) {

		/*$id		= Dba::escape($data['shout_id']);
		$text 		= Dba::escape(strip_tags($data['comment']));
		$sticky 	= make_bool($data['sticky']);*/
echo $text;
echo $shout_id;
		$sql = "UPDATE `user_shout` SET `text`='$text' WHERE `id`='$shout_id'";
		$db_results = Dba::write($sql);

		return true;

	} // update_comment_text
	
	
	/**
	 * validate comment
	 * This takes a key'd array of data as input and updates a shoutbox entry
	 */
	public static function validate_comment() {


		$sql = "UPDATE `user_shout` SET `sticky`='1' WHERE `sticky`='2'";
		$db_results = Dba::write($sql);

		return true;

	} // validate comment


	/**
	 * delete
	 * this function deletes a specific shoutbox entry
	 */

	public function delete_comment() {

		// Delete the shoutbox post
		$shout_id = Dba::escape($shout_id);
		$sql = "DELETE FROM `user_shout` WHERE `sticky`='3'";
		$db_results = Dba::write($sql);

	} // delete
	
	/**
	 * live delete shout
	 * this function deletes a specific shoutbox entry
	 */

	public function delete_shout($shout_id) {
		$sql = "DELETE FROM `user_shout` WHERE `id`='$shout_id'";
		$db_results = Dba::write($sql);

	} // delete
	
	
	/**
	 * update_shout_state
	 * sets the enabled selected
	 */
	public static function update_shout_state($new_state,$shout_id) {

		self::_update_shout_state('sticky',$new_state,$shout_id,'100');

	} // update_enabled

	/**
	 * _update_item
	 * This is a private function that should only be called from within the song class.
	 * It takes a field, value song id and level. first and foremost it checks the level
	 * against $GLOBALS['user'] to make sure they are allowed to update this record
	 * it then updates it and sets $this->{$field} to the new value
	 */
	private static function _update_shout_state($field,$value,$shout_id,$level) {

		/* Check them Rights! */
		if (!Access::check('interface',$level)) { return false; }

		$value = Dba::escape($value);

		$sql = "UPDATE `user_shout` SET `$field`='$value' WHERE `id`='$shout_id'";
		$db_results = Dba::write($sql);

		return true;

	} // _update_item



} // shoutBox class
?>
