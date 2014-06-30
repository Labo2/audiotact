<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Flag Class
 *
 *
 * LICENSE: GNU General Public License, version 2 (GPLv2)
 * Copyright (c) 2001 - 2011 Ampache.org All Rights Reserved
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; version 2
 * of the License.
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
 * Flag Class
 *
 * This handles flagging of songs, albums and artists
 *
 * @package	Ampache
 * @copyright	2001 - 2011 Ampache.org
 * @license	http://opensource.org/licenses/gpl-2.0 GPLv2
 * @link	http://www.ampache.org/
 */
class Favorits extends database_object {

	public $id;
	public $user;
	public $object_id;
	public $object_type;
	public $comment;
	public $flag;
	public $date;
	public $approved=0;

	/* Generated Values */
	public $name; // Blank
	public $title; // Blank

	/**
	 * Constructor
	 * This takes a flagged.id and then pulls in the information for said flag entry
	 */
	public function __construct($flag_id) {

		$info = $this->get_info($flag_id,'flagged');

		foreach ($info as $key=>$value) {
			$this->$key = $value;
		}

		return true;

	} // Constructor


	
	/**
	 * get_selected_object
	 * This returns all of the songs that have been disabled, this is
	 * a form of being flagged
	 */
	public static function get_selected_object() {

		$sql = "SELECT `value` FROM `preference` WHERE `name`='favorits_type'";
		$db_results = Dba::read($sql);

		$results = array();

		while ($row = Dba::fetch_assoc($db_results)) {
			$results[] = $row['value'];
		}

		return $results;

	} // get_disabled

	
	/**
	 * get_selected_favorits
	 * This returns all of the songs that have been disabled, this is
	 * a form of being flagged
	 */
	public static function get_selected($value) {
		$sql = "SELECT `id` FROM `$value` WHERE `selected`='1'";
		$db_results = Dba::read($sql);
		
		while ($row = Dba::fetch_assoc($db_results)) {
				$results[] = $row['id'];
		} 
		return $results;

		/*if ($value == "album") {
			while ($row = Dba::fetch_assoc($db_results)) {
				$art = new Art($row['id'], 'album');
				$art->get_db();
				if ($art->raw) {
					$results[] = $row['id'];
				}
			}
			print_r ($results);
			return $results;
		} elseif ($value == "artist") {
			while ($row = Dba::fetch_assoc($db_results)) {
				$results[] = $row['id'];
			} return $results;
		} elseif ($value == "playlist") {
			while ($row = Dba::fetch_assoc($db_results)) {
				$results[] = $row['id'];
			} return $results;
		}*/
	} // get_selected

	/**
	 * get_selected_album
	 * This returns all of the songs that have been disabled, this is
	 * a form of being flagged
	 */
	public static function get_selected_album() {

		$sql = "SELECT `id` FROM `album` WHERE `selected`='1'";
		$db_results = Dba::read($sql);

		$results = array();

		while ($row = Dba::fetch_assoc($db_results)) {
			$results[] = $row['id'];
		}

		return $results;

	} // get_disabled

	/**
	 * get_selected_artist
	 * This returns all of the songs that have been disabled, this is
	 * a form of being flagged
	 */
	public static function get_selected_artist() {

		$sql = "SELECT `id` FROM `artist` WHERE `selected`='1'";
		$db_results = Dba::read($sql);

		$results = array();

		while ($row = Dba::fetch_assoc($db_results)) {
			$results[] = $row['id'];
		}

		return $results;

	} // get_disabled

	/**
	 * get_selected_playlist
	 * This returns all of the songs that have been disabled, this is
	 * a form of being flagged
	 */
	public static function get_selected_playlist() {

		$sql = "SELECT `id` FROM `playlist` WHERE `selected`='1'";
		$db_results = Dba::read($sql);

		$results = array();

		while ($row = Dba::fetch_assoc($db_results)) {
			$results[] = $row['id'];
		}

		return $results;

	} // get_disabled

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	




} //end of flag class

?>
