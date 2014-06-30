<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 *  audiotact_info Class
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
 * audiotact_info Class
 *
 * Description here...
 *
 * @package	Ampache
 * @copyright	2001 - 2011 Ampache.org
 * @license	http://opensource.org/licenses/gpl-2.0 GPLv2
 * @link	http://www.ampache.org/
 */
class  audiotact_info {

	public $id;

	/**
	 * Constructor
	 * This pulls the shoutbox information from the database and returns
	 * a constructed object, uses user_shout table
	 */
	public function __construct($box_id) {

		// Load the data from the database
		$this->_get_infobox($box_id);

		return true;

	} // Constructor

	/**
	 * _get_info
	 * does the db call, reads from the user_shout table
	 */
	private function _get_infobox($box_id) {

		$sql = "SELECT * FROM `audiotact_info` WHERE `id`='$box_id'";
		$db_results = Dba::read($sql);

		$data = Dba::fetch_assoc($db_results);

		foreach ($data as $key=>$value) {
			$this->$key = $value;
		}

		return true;

	} // _get_info
	
	
	/**
	 * get_all
	 * This returns the top user_shouts, shoutbox objects are always shown regardless and count against the total
	 * number of objects shown
	 */
	public static function get_all() {
		$sql = "SELECT * FROM `audiotact_info`";
		$db_results = Dba::read($sql);

		while ($row = Dba::fetch_assoc($db_results)) {
			$infos[] = $row['id'];
		}

		return $infos;

	} // get_all
	
	/**
	 * get_to_update_box
	 * This returns the top user_shouts, shoutbox objects are always shown regardless and count against the total
	 * number of objects shown
	 */
	public static function get_to_update_box($box_id) {
		$sql = "SELECT * FROM `audiotact_info` WHERE `id`='$box_id'";
		$db_results = Dba::read($sql);

		while ($row = Dba::fetch_assoc($db_results)) {
			$infos[] = $row['id'];
		}

		return $infos;

	} // get_to_update_box
	
	/**
	 * update_informations
	 * This takes a key'd array of data as input and updates a shoutbox entry
	 */
	public static function update_informations($id,$content) {

		$sql = "UPDATE `audiotact_info` SET `content`='$content' WHERE `id`='$id'";
		$db_results = Dba::write($sql);

		return true;

	} // update_informations

	
	/**
	 * get_from_box
	 * This returns the top user_shouts, shoutbox objects are always shown regardless and count against the total
	 * number of objects shown
	 */
	public static function get_from_box($name) {

		$sql = "SELECT * FROM `audiotact_info` WHERE `name`='$name'";
		$db_results = Dba::read($sql);

		while ($row = Dba::fetch_assoc($db_results)) {
			$shouts[] = $row['id'];
		}

		return $shouts;

	} // get_from_box



} // audiotact_info class
?>
