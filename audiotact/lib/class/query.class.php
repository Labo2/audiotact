<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Query Class
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
 * Query Class
 *
 * This handles all of the sql/filtering for the ampache database
 * this was seperated out from browse to accomodate Dynamic Playlists
 *
 * @package	Ampache
 * @copyright	2001 - 2011 Ampache.org
 * @license	http://opensource.org/licenses/gpl-2.0 GPLv2
 * @link	http://www.ampache.org/
 */
class Query {

	public $id;
	public $catalog;

	protected $_state = array();
	protected $_cache;

	private static $allowed_filters;
	private static $allowed_sorts;

	/**
	 * constructor
	 * This should be called
	 */
	public function __construct($id = null) {
		$sid = Dba::escape(session_id());
	
		if (is_null($id)) {
			$this->reset();
			$data = Dba::escape(serialize($this->_state));

			$sql = "INSERT INTO `tmp_browse` (`sid`, `data`) " .
				"VALUES('$sid', '$data')";
			$db_results = Dba::write($sql);
			$this->id = Dba::insert_id();
			
			return true;
		}

		$this->id = $id;

		$sql = "SELECT `data` FROM `tmp_browse` " .
			"WHERE `id`='$id' AND `sid`='$sid'";

		$db_results = Dba::read($sql);

		if ($results = Dba::fetch_assoc($db_results)) {
			$this->_state = unserialize($results['data']);
			return true;
		}

		Error::add('browse', _('Browse not found or expired, try reloading the page'));
		return false;
	}

	/**
	 * _auto_init
	 * Automatically called when the class is loaded.
	 * Populate static arrays if necessary
	 */
	public static function _auto_init() {
		if (is_array(self::$allowed_filters)) {
			return true;
		}

		self::$allowed_filters = array(
			'album' => array(
				'add_lt',
				'add_gt',
				'update_lt',
				'update_gt',
				'show_art',
				'starts_with',
				'exact_match',
				'alpha_match',
				'catalog'
			),
			'artist' => array(
				'add_lt',
				'add_gt',
				'update_lt',
				'update_gt',
				'exact_match',
				'alpha_match',
				'starts_with',
				'tag',
				'catalog'
			),
			'song' => array(
				'add_lt',
				'add_gt',
				'update_lt',
				'update_gt',
				'exact_match',
				'alpha_match',
				'starts_with',
				'tag',
				'catalog'
			),
			'live_stream' => array(
				'alpha_match',
				'starts_with'
			),
			'playlist' => array(
				'alpha_match',
				'starts_with',
				'genre'
			),
			'smartplaylist' => array(
				'alpha_match',
				'starts_with'
			),
			'tag' => array(
				'tag',
				'object_type',
				'exact_match',
				'alpha_match',
				'starts_with'
			),
			'video' => array(
				'starts_with',
				'exact_match',
				'alpha_match'
			)
		);

		if (Access::check('interface','50')) {
			array_push(self::$allowed_filters['playlist'], 'playlist_type');
		}

		self::$allowed_sorts = array(
			'playlist_song' => array(
				'title',
				'year',
				'track',
				'time',
				'album',
				'artist'
			),
			'song' => array(
				'title',
				'year',
				'track',
				'time',
				'album',
				'artist'
			),
			'artist' => array(
				'name',
				'album'
			),
			'tag' => array(
				'tag'
			),
			'album' => array(
				'name',
				'year',
				'artist'
			),
			'playlist' => array(
				'name',
				'user',
				'genre'
			),
			'smartplaylist' => array(
				'name',
				'user'
			),
			'shoutbox' => array(
				'date',
				'user',
				'sticky'
			),
			'live_stream' => array(
				'name',
				'call_sign',
				'frequency'
			),
			'video' => array(
				'title',
				'resolution',
				'length',
				'codec'
			),
			'user' => array(
				'fullname',
				'username',
				'last_seen',
				'create_date'
			)
		);
	}

	/**
	 * clean
	 * This cleans old data out of the table
	 */
	public static function clean() {
		$sql = "DELETE FROM `tmp_browse` USING `tmp_browse` LEFT JOIN ".
			"`session` ON `session`.`id`=`tmp_browse`.`sid` " .
		        "WHERE `session`.`id` IS NULL";
		$db_results = Dba::write($sql);
	}

	/**
	 * set_filter
	 * This saves the filter data we pass it.
	 */
	public function set_filter($key, $value) {

		switch ($key) {
			case 'tag':
				if (is_array($value)) {
					$this->_state['filter'][$key] = $value;
				}
				elseif (is_numeric($value)) {
					$this->_state['filter'][$key] = array($value);
				}
				else {
					$this->_state['filter'][$key] = array();
				}
			break;
			case 'artist':
			case 'catalog':
			case 'album':
				$this->_state['filter'][$key] = $value;
			break;
			case 'min_count':
			case 'unplayed':
			case 'rated':

			break;
			case 'add_lt':
			case 'add_gt':
			case 'update_lt':
			case 'update_gt':
				$this->_state['filter'][$key] = intval($value);
			break;
			case 'exact_match':
			case 'alpha_match':
			case 'starts_with':
				if ($this->is_static_content()) { return false; }
				$this->_state['filter'][$key] = $value;
			break;
			case 'playlist_type':
			case 'genre':
				// Must be a content manager to turn this off
				/*if ($this->_state['filter'][$key] AND Access::check('interface','50')) { unset($this->_state['filter'][$key]); }
				else { */
					//unset($this->_state['filter'][$key]);
					//$this->_state['filter'][$key] = '1'; 
					$this->_state['filter'][$key] = $value;
					
					//}
			break;
			default:
				// Rien a faire
				return false;
			break;
		} // end switch

		// If we've set a filter we need to reset the totals
		$this->reset_total();
		$this->set_start(0);

		return true;

	} // set_filter

	/**
	 * reset
	 * Reset everything, this should only be called when we are starting
	 * fresh
	 */
	public function reset() {

		$this->reset_base();
		$this->reset_filters();
		$this->reset_total();
		$this->reset_join();
		$this->reset_select();
		$this->reset_having();
		$this->set_static_content(false);
		$this->set_is_simple(false);
		$this->set_start(0);
		$this->set_offset(Config::get('offset_limit') ? Config::get('offset_limit') : '6');

	} // reset

	/**
	 * reset_base
	 * this resets the base string
	 */
	public function reset_base() {

		$this->_state['base'] = NULL;

	} // reset_base

	/**
	 * reset_select
	 * This resets the select fields that we've added so far
	 */
	public function reset_select() {

		$this->_state['select'] = array();

	} // reset_select

	/**
	 * reset_having
	 * Null out the having clause
	 */
	public function reset_having() {

		unset($this->_state['having']);

	} // reset_having

	/**
	 * reset_join
	 * clears the joins if there are any
	 */
	public function reset_join() {

		unset($this->_state['join']);

	} // reset_join

	/**
	 * reset_filter
	 * This is a wrapper function that resets the filters
	 */
	public function reset_filters() {

		$this->_state['filter'] = array();

	} // reset_filters

	/**
	 * reset_total
	 * This resets the total for the browse type
	 */
	public function reset_total() {

		unset($this->_state['total']);

	} // reset_total

	/**
	 * get_filter
	 * returns the specified filter value
	 */
	public function get_filter($key) {

		// Simple enough, but if we ever move this crap
		// If we ever move this crap what?
		return isset($this->_state['filter'][$key]) 
			? $this->_state['filter'][$key]
			: false;

	} // get_filter

	/**
	 * get_start
	 * This returns the current value of the start
	 */
	public function get_start() {

		return $this->_state['start'];

	} // get_start

	/**
	 * get_offset
	 * This returns the current offset
	 */
	public function get_offset() {
		if ($this->is_static_content()) {
			return $this->get_total();
		}

		return $this->_state['offset'];
	} // get_offset

	/**
	 * set_total
	 * This sets the total number of objects
	 */
	public function set_total($total) {
		$this->_state['total'] = $total;
	}

	/**
	 * get_total
	 * This returns the total number of objects for this current sort type.
	 * If it's already cached used it. if they pass us an array then use
	 * that.
	 */
	public function get_total($objects = null) {

		// If they pass something then just return that
		if (is_array($objects) and !$this->is_simple()) {
			return count($objects);
		}

		// See if we can find it in the cache
		if (isset($this->_state['total'])) {
			return $this->_state['total'];
		}

		$db_results = Dba::read($this->get_sql(false));
		$num_rows = Dba::num_rows($db_results);

		$this->_state['total'] = $num_rows;

		return $num_rows;

	} // get_total

	/**
	 * get_allowed_filters
	 * This returns an array of the allowed filters based on the type of 
	 * object we are working with, this is used to display the 'filter' 
	 * sidebar stuff.
	 */
	public static function get_allowed_filters($type) {
		return isset(self::$allowed_filters[$type])
			? self::$allowed_filters[$type]
			: array();
	} // get_allowed_filters

	/**
 	 * set_type
	 * This sets the type of object that we want to browse by
	 * we do this here so we only have to maintain a single whitelist
	 * and if I want to change the location I only have to do it here
	 */
	public function set_type($type) {

		switch($type) {
			case 'user':
			case 'video':
			case 'playlist':
			case 'playlist_song':
			case 'smartplaylist':
			case 'song':
			case 'flagged':
			case 'catalog':
			case 'album':
			case 'album_tag':
			case 'artist':
			case 'tag':
			case 'playlist_localplay':
			case 'shoutbox':
			case 'live_stream':
			case 'democratic':
				// Set it
				$this->_state['type'] = $type;
				$this->set_base_sql(true);
			break;
			default:
				// Rien a faire
			break;
		} // end type whitelist
	} // set_type

	/**
	 * get_type
	 * This returns the type of the browse we currently are using
	 */
	public function get_type() {

		return $this->_state['type'];

	} // get_type

	/**
	 * set_sort
	 * This sets the current sort(s)
	 */
	public function set_sort($sort,$order='') {

		// If it's not in our list, smeg off!
		if (!in_array($sort, self::$allowed_sorts[$this->get_type()])) {
			return false;
		}

		if ($order) {
			$order = ($order == 'DESC') ? 'DESC' : 'ASC';
			$this->_state['sort'] = array();
			$this->_state['sort'][$sort] = $order;
		}
		elseif ($this->_state['sort'][$sort] == 'DESC') {
			// Reset it till I can figure out how to interface the hotness
			$this->_state['sort'] = array();
			$this->_state['sort'][$sort] = 'ASC';
		}
		else {
			// Reset it till I can figure out how to interface the hotness
			$this->_state['sort'] = array();
			$this->_state['sort'][$sort] = 'DESC';
		}

		$this->resort_objects();

	} // set_sort

	/**
	 * set_offset
	 * This sets the current offset of this query
	 */
	public function set_offset($offset) {

		$this->_state['offset'] = abs($offset);

	} // set_offset

        public function set_catalog( $catalog_number ) {
                $this->catalog = $catalog_number;
		debug_event("Catalog", "set catalog id: " . $this->catalog, "5");
        }

	/**
	 * set_select
	 * This appends more information to the select part of the SQL 
	 * statement, we're going to move to the %%SELECT%% style queries, as I
	 * think it's the only way to do this...
	 */
	public function set_select($field) {

		$this->_state['select'][] = $field;

	} // set_select

	/**
	 * set_join
	 * This sets the joins for the current browse object
	 */
	public function set_join($type, $table, $source, $dest, $priority) {

		$this->_state['join'][$priority][$table] = strtoupper($type) . ' JOIN ' . $table . ' ON ' . $source . '=' . $dest;

	} // set_join

	/**
	 * set_having
	 * This sets the "HAVING" part of the query, we can only have one..
	 * god this is ugly
	 */
	public function set_having($condition) {

		$this->_state['having'] = $condition;

	} // set_having

	/**
	 * set_start
	 * This sets the start point for our show functions
	 * We need to store this in the session so that it can be pulled
	 * back, if they hit the back button
	 */
	public function set_start($start) {


		$start = intval($start);

		if (!$this->is_static_content()) {
			$this->_state['start'] = $start;
		}

	} // set_start

	/**
	 * set_is_simple
	 * This sets the current browse object to a 'simple' browse method
	 * which means use the base query provided and expand from there
	 */
	public function set_is_simple($value) {

		$value = make_bool($value);
		$this->_state['simple'] = $value;

	} // set_is_simple

	/**
	 * set_static_content
	 * This sets true/false if the content of this browse
	 * should be static, if they are then content filtering/altering
	 * methods will be skipped
	 */
	public function set_static_content($value) {

		$value = make_bool($value);

		// We want to start at 0 if it's static
		if ($value) {
			$this->set_start('0');
		}

		$this->_state['static'] = $value;

	} // set_static_content

	public function is_static_content() {
		return $this->_state['static'];
	}

	/**
	 * is_simple
	 * This returns whether or not the current browse type is set to static.
	 */
	public function is_simple() {

		return $this->_state['simple'];

	} // is_simple

	/**
	 * get_saved
	 * This looks in the session for the saved stuff and returns what it 
	 * finds.
	 */
	public function get_saved() {

		// See if we have it in the local cache first
		if (is_array($this->_cache)) {
			return $this->_cache;
		}

		if (!$this->is_simple()) {
			$sid = Dba::escape(session_id());
			$id = Dba::escape($this->id);
			$sql = "SELECT `object_data` FROM `tmp_browse` WHERE `sid`='$sid' AND `id`='$id'";
			$db_results = Dba::read($sql);

			$row = Dba::fetch_assoc($db_results);

			$this->_cache = unserialize($row['object_data']);
			return $this->_cache;
		}
		else {
			$objects = $this->get_objects();
		}

		return $objects;

	} // get_saved

	/**
	 * get_objects
	 * This gets an array of the ids of the objects that we are
	 * currently browsing by it applies the sql and logic based
	 * filters
	 */
	public function get_objects() {

		// First we need to get the SQL statement we are going to run
		// This has to run against any possible filters (dependent on type)
		$sql = $this->get_sql(true);
		$db_results = Dba::read($sql);

		$results = array();
		while ($data = Dba::fetch_assoc($db_results)) {
			$results[] = $data;
		}

		$results = $this->post_process($results);
		$filtered = array();
		foreach ($results as $data) {
			// Make sure that this object passes the logic filter
			if ($this->logic_filter($data['id'])) {
				$filtered[] = $data['id'];
			}
		} // end while

		// Save what we've found and then return it
		$this->save_objects($filtered);

		return $filtered;

	} // get_objects

	/**
	 * set_base_sql
	 * This saves the base sql statement we are going to use.
	 */
	private function set_base_sql($force = false) {
		
		// Only allow it to be set once
		if (strlen($this->_state['base']) && !$force) { return true; }

		switch ($this->get_type()) {
			case 'album':
				$this->set_select("DISTINCT(`album`.`id`)");
				$sql = "SELECT %%SELECT%% FROM `album` ";
			break;
			case 'album_tag':
				$this->set_select("DISTINCT(`album`.`id`)");
				$sql = "SELECT %%SELECT%% FROM `album` ";
			break;
			case 'artist':
				$this->set_select("`artist`.`id`");
				$sql = "SELECT %%SELECT%% FROM `artist` ";
			break;
			case 'catalog':
				$this->set_select("`artist`.`name`");
				$sql = "SELECT %%SELECT%% FROM `artist` ";
			break;
			case 'user':
				$this->set_select("`user`.`id`");
				$sql = "SELECT %%SELECT%% FROM `user` ";
			break;
			case 'live_stream':
				$this->set_select("`live_stream`.`id`");
				$sql = "SELECT %%SELECT%% FROM `live_stream` ";
			break;
			case 'playlist':
				$this->set_select("`playlist`.`id`");
				$sql = "SELECT %%SELECT%% FROM `playlist`";
			break;
			case 'smartplaylist':
				self::set_select('`search`.`id`');
				$sql = "SELECT %%SELECT%% FROM `search` ";
			break;
			case 'flagged':
				$this->set_select("`flagged`.`id`");
				$sql = "SELECT %%SELECT%% FROM `flagged` ";
			break;
			case 'shoutbox':
				$this->set_select("`user_shout`.`id`");
				$sql = "SELECT %%SELECT%% FROM `user_shout` ";
			break;
			case 'video':
				$this->set_select("`video`.`id`");
				$sql = "SELECT %%SELECT%% FROM `video` ";
			break;
			case 'tag':
				$this->set_select("DISTINCT(`tag`.`id`)");
				$this->set_join('left', 'tag_map', '`tag_map`.`tag_id`', '`tag`.`id`', 1);
				$sql = "SELECT %%SELECT%% FROM `tag` ";
			break;
			case 'playlist_song':
			case 'song':
			default:
				$this->set_select("DISTINCT(`song`.`id`)");
				$sql = "SELECT %%SELECT%% FROM `song` ";
			break;
		} // end base sql

		$this->_state['base'] = $sql;

	} // set_base_sql

	/**
	 * get_select
	 * This returns the selects in a format that is friendly for a sql
	 * statement.
	 */
	private function get_select() {

		$select_string = implode($this->_state['select'], ", ");
		return $select_string;

	} // get_select

	/**
	 * get_base_sql
	 * This returns the base sql statement all parsed up, this should be
	 * called after all set operations.
	 */
	private function get_base_sql() {

		$sql = str_replace("%%SELECT%%", $this->get_select(), $this->_state['base']);
		return $sql;

	} // get_base_sql

	/**
	 * get_filter_sql
	 * This returns the filter part of the sql statement
	 */
	private function get_filter_sql() {

		if (!is_array($this->_state['filter'])) {
			return '';
		}
		
		$sql = "WHERE 1=1 AND ";

		foreach ($this->_state['filter'] 
			as $key => $value) {

			$sql .= $this->sql_filter($key, $value);
		}

		$sql = rtrim($sql,'AND ') . ' ';

		return $sql;

	} // get_filter_sql

	/**
	 * get_sort_sql
	 * Returns the sort sql part
	 */
	private function get_sort_sql() {

		if (!is_array($this->_state['sort'])) {
			return '';
		}

		$sql = 'ORDER BY ';

		foreach ($this->_state['sort']
			as $key => $value) {
			$sql .= $this->sql_sort($key, $value);
		}

		$sql = rtrim($sql,'ORDER BY ');
		$sql = rtrim($sql,',');

		return $sql;

	} // get_sort_sql

	/**
	 * get_limit_sql
	 * This returns the limit part of the sql statement
	 */
	private function get_limit_sql() {

		if (!$this->is_simple()) { return ''; }

		$sql = ' LIMIT ' . intval($this->get_start()) . ',' . intval($this->get_offset());

		return $sql;

	} // get_limit_sql

	/**
	 * get_join_sql
	 * This returns the joins that this browse may need to work correctly
	 */
	private function get_join_sql() {

		if (!is_array($this->_state['join'])) {
			return '';
		}

		$sql = '';

		foreach ($this->_state['join'] as $joins) {
			foreach ($joins as $join) {
				$sql .= $join . ' ';
			} // end foreach joins at this level
		} // end foreach of this level of joins

		return $sql;

	} // get_join_sql

	/**
	 * get_having_sql
	 * this returns the having sql stuff, if we've got anything
	 */
	public function get_having_sql() {

		$sql = $this->_state['having'];

		return $sql;

	} // get_having_sql

	/**
	 * get_sql
	 * This returns the sql statement we are going to use this has to be run
	 * every time we get the objects because it depends on the filters and
	 * the type of object we are currently browsing.
	 */
	public function get_sql($limit = true) {

		$sql = $this->get_base_sql();

		$filter_sql = $this->get_filter_sql();
		$join_sql = $this->get_join_sql();
		$having_sql = $this->get_having_sql();
		$order_sql = $this->get_sort_sql();

		$limit_sql = $limit ? $this->get_limit_sql() : '';
	
		$final_sql = $sql . $join_sql . $filter_sql . $having_sql;
	
		if( $this->get_type() == 'artist' ) {
			 $final_sql .= " GROUP BY `" . $this->get_type() . "`.`name` ";
		}
		$final_sql .= $order_sql . $limit_sql;
		debug_event("Catalog", "catalog sql: " . $final_sql, "6");
		return $final_sql;

	} // get_sql

	/**
  	 * post_process
	 * This does some additional work on the results that we've received
	 * before returning them.
	 */
	private function post_process($data) {

		$tags = $this->_state['filter']['tag'];

		if (!is_array($tags) || sizeof($tags) < 2) {
			return $data;
		}

		$tag_count = sizeof($tags);
		$count = array();

		foreach($data as $row) {
			$count[$row['id']]++;
		}

		$results = array();

		foreach($count as $key => $value) {
			if ($value >= $tag_count) {
				$results[] = array('id' => $key);
			}
		} // end foreach

		return $results;

	} // post_process

	/**
	 * sql_filter
	 * This takes a filter name and value and if it is possible
	 * to filter by this name on this type returns the appropriate sql
	 * if not returns nothing
	 */
	private function sql_filter($filter, $value) {

		$filter_sql = '';

		switch ($this->get_type()) {

		case 'song':
			switch($filter) {
				case 'tag':
					$this->set_join('left', '`tag_map`', '`tag_map`.`object_id`', '`song`.`id`', 100);
					$filter_sql = " `tag_map`.`object_type`='song' AND (";

					foreach ($value as $tag_id) {
						$filter_sql .= "  `tag_map`.`tag_id`='" . Dba::escape($tag_id) . "' AND";
					}
					$filter_sql = rtrim($filter_sql,'AND') . ') AND ';
				break;
				case 'exact_match':
					$filter_sql = " `song`.`title` = '" . Dba::escape($value) . "' AND ";
				break;
				case 'alpha_match':
					$filter_sql = " `song`.`title` LIKE '%" . Dba::escape($value) . "%' AND ";
				break;
				case 'starts_with':
					$filter_sql = " `song`.`title` LIKE '" . Dba::escape($value) . "%' AND ";
					if( $this->catalog != 0 ) {
						$filter_sql .= " `song`.`catalog` = '" . $this->catalog . "' AND ";
					}
				break;
				case 'unplayed':
					$filter_sql = " `song`.`played`='0' AND ";
				break;
				case 'album':
					$filter_sql = " `song`.`album` = '". Dba::escape($value) . "' AND ";
				break;
				case 'album_tag':
					$filter_sql = " `song`.`album` = '". Dba::escape($value) . "' AND ";
				break;
				case 'artist':
					$filter_sql = " `song`.`artist` = '". Dba::escape($value) . "' AND ";
				break;
				case 'add_gt':
					$filter_sql = " `song`.`addition_time` >= '" . Dba::escape($value) . "' AND ";
				break;
				case 'add_lt':
					$filter_sql = " `song`.`addition_time` <= '" . Dba::escape($value) . "' AND ";
				break;
				case 'update_gt':
					$filter_sql = " `song`.`update_time` >= '" . Dba::escape($value) . "' AND ";
				break;
				case 'update_lt':
					$filter_sql = " `song`.`update_time` <= '" . Dba::escape($value) . "' AND ";
				break;
				case 'catalog':
					if($value != 0) {
						$filter_sql = " `song`.`catalog` = '$value' AND ";
					}
				break;
				default:
					// Rien a faire
				break;
			} // end list of sqlable filters
		break;
		case 'album':
			switch($filter) {
				case 'tag':
					$this->set_join('left', '`tag_map`', '`tag_map`.`object_id`', '`album`.`id`', 100);
					$filter_sql = " `tag_map`.`object_type`='album' AND (";

					foreach ($value as $tag_id) {
						$filter_sql .= "  `tag_map`.`tag_id`='" . Dba::escape($tag_id) . "' AND";
					}
					$filter_sql = rtrim($filter_sql,'AND') . ') AND ';
				break;
				case 'exact_match':
					$filter_sql = " `album`.`name` = '" . Dba::escape($value) . "' AND ";
				break;
				case 'alpha_match':
					$filter_sql = " `album`.`name` LIKE '%" . Dba::escape($value) . "%' AND ";
				break;
				case 'starts_with':
					$this->set_join('left', '`song`', '`album`.`id`', '`song`.`album`', 100);
					$filter_sql = " `album`.`name` LIKE '" . Dba::escape($value) . "%' AND ";
					if( $this->catalog != 0 ) {
						$filter_sql .= "`song`.`catalog` = '" . $this->catalog . "' AND ";
					}
				break;
				case 'artist':
					$filter_sql = " `artist`.`id` = '". Dba::escape($value) . "' AND ";
				break;
				case 'add_lt':
					$this->set_join('left', '`song`', '`song`.`album`', '`album`.`id`', 100);
					$filter_sql = " `song`.`addition_time` <= '" . Dba::escape($value) . "' AND ";
				break;
				case 'add_gt':
					$this->set_join('left', '`song`', '`song`.`album`', '`album`.`id`', 100);
					$filter_sql = " `song`.`addition_time` >= '" . Dba::escape($value) . "' AND ";
				break;
				case 'catalog':
					if($value != 0) {
						$this->set_join('left','`song`','`album`.`id`','`song`.`album`', 100);
						$this->set_join('left','`catalog`','`song`.`catalog`','`catalog`.`id`', 100);
                                                $filter_sql = " (`song`.`catalog` = '$value') AND ";
                                        }
				break;
				case 'update_lt':
					$this->set_join('left', '`song`', '`song`.`album`', '`album`.`id`', 100);
					$filter_sql = " `song`.`update_time` <= '" . Dba::escape($value) . "' AND ";
				break;
				case 'update_gt':
					$this->set_join('left', '`song`', '`song`.`album`', '`album`.`id`', 100);
					$filter_sql = " `song`.`update_time` >= '" . Dba::escape($value) . "' AND ";
				break;
				default:
					// Rien a faire
				break;
			}
		break;
		case 'album_tag':
			switch($filter) {
				case 'tag':
					$this->set_join('left', '`tag_map`', '`tag_map`.`object_id`', '`album`.`id`', 100);
					$filter_sql = " `tag_map`.`object_type`='album' AND (";

					foreach ($value as $tag_id) {
						$filter_sql .= "  `tag_map`.`tag_id`='" . Dba::escape($tag_id) . "' AND";
					}
					$filter_sql = rtrim($filter_sql,'AND') . ') AND ';
				break;
				case 'exact_match':
					$filter_sql = " `album`.`name` = '" . Dba::escape($value) . "' AND ";
				break;
				case 'alpha_match':
					$filter_sql = " `album`.`name` LIKE '%" . Dba::escape($value) . "%' AND ";
				break;
				case 'starts_with':
					$this->set_join('left', '`song`', '`album`.`id`', '`song`.`album`', 100);
					$filter_sql = " `album`.`name` LIKE '" . Dba::escape($value) . "%' AND ";
					if( $this->catalog != 0 ) {
						$filter_sql .= "`song`.`catalog` = '" . $this->catalog . "' AND ";
					}
				break;
				case 'artist':
					$filter_sql = " `artist`.`id` = '". Dba::escape($value) . "' AND ";
				break;
				case 'add_lt':
					$this->set_join('left', '`song`', '`song`.`album`', '`album`.`id`', 100);
					$filter_sql = " `song`.`addition_time` <= '" . Dba::escape($value) . "' AND ";
				break;
				case 'add_gt':
					$this->set_join('left', '`song`', '`song`.`album`', '`album`.`id`', 100);
					$filter_sql = " `song`.`addition_time` >= '" . Dba::escape($value) . "' AND ";
				break;
				case 'catalog':
					if($value != 0) {
						$this->set_join('left','`song`','`album`.`id`','`song`.`album`', 100);
						$this->set_join('left','`catalog`','`song`.`catalog`','`catalog`.`id`', 100);
                                                $filter_sql = " (`song`.`catalog` = '$value') AND ";
                                        }
				break;
				case 'update_lt':
					$this->set_join('left', '`song`', '`song`.`album`', '`album`.`id`', 100);
					$filter_sql = " `song`.`update_time` <= '" . Dba::escape($value) . "' AND ";
				break;
				case 'update_gt':
					$this->set_join('left', '`song`', '`song`.`album`', '`album`.`id`', 100);
					$filter_sql = " `song`.`update_time` >= '" . Dba::escape($value) . "' AND ";
				break;
				default:
					// Rien a faire
				break;
			}
		break;

		case 'artist':
			switch($filter) {
				case 'catalog':
				if($value != 0) {
					$this->set_join('left','`song`','`artist`.`id`','`song`.`artist`', 100);
					$this->set_join('left','`catalog`','`song`.`catalog`','`catalog`.`id`', 100);
					$filter_sql = "  (`catalog`.`id` = '$value') AND ";
				}
				break;
				case 'exact_match':
					$filter_sql = " `artist`.`name` = '" . Dba::escape($value) . "' AND ";
				break;
				case 'alpha_match':
					$filter_sql = " `artist`.`name` LIKE '%" . Dba::escape($value) . "%' AND ";
				break;
				case 'starts_with':
					$this->set_join('left', '`song`', '`artist`.`id`', '`song`.`artist`', 100);
					$filter_sql = " `artist`.`name` LIKE '" . Dba::escape($value) . "%' AND ";
					if( $this->catalog != 0 ) {
						$filter_sql .= "`song`.`catalog` = '" . $this->catalog . "' AND ";
					}
				break;
				case 'add_lt':
					$this->set_join('left', '`song`', '`song`.`artist`', '`artist`.`id`', 100);
					$filter_sql = " `song`.`addition_time` <= '" . Dba::escape($value) . "' AND ";
				break;
				case 'add_gt':
					$this->set_join('left', '`song`', '`song`.`artist`', '`artist`.`id`', 100);
					$filter_sql = " `song`.`addition_time` >= '" . Dba::escape($value) . "' AND ";
				break;
				case 'update_lt':
					$this->set_join('left', '`song`', '`song`.`artist`', '`artist`.`id`', 100);
					$filter_sql = " `song`.`update_time` <= '" . Dba::escape($value) . "' AND ";
				break;
				case 'update_gt':
					$this->set_join('left', '`song`', '`song`.`artist`', '`artist`.`id`', 100);
					$filter_sql = " `song`.`update_time` >= '" . Dba::escape($value) . "' AND ";
				break;
				default:
					// Rien a faire
				break;
			} // end filter
		break;
		case 'live_stream':
			switch ($filter) {
				case 'alpha_match':
					$filter_sql = " `live_stream`.`name` LIKE '%" . Dba::escape($value) . "%' AND ";
				break;
				case 'starts_with':
					$filter_sql = " `live_stream`.`name` LIKE '" . Dba::escape($value) . "%' AND ";
				break;
				default:
					// Rien a faire
				break;
			} // end filter
		break;
		case 'playlist':
			switch ($filter) {
				case 'alpha_match':
					$filter_sql = " `playlist`.`name` LIKE '%" . Dba::escape($value) . "%' AND ";
				break;
				case 'starts_with':
					$filter_sql = " `playlist`.`name` LIKE '" . Dba::escape($value) . "%' AND ";
				break;
				case 'playlist_type':
					$user_id = intval($GLOBALS['user']->id);
					//$filter_sql = " (`playlist`.`type` = 'private' OR `playlist`.`user`='$user_id') AND ";
					$filter_sql = " (`playlist`.`type` = '$value') AND ";
				break;
				case 'genre':
					$filter_sql = " `playlist`.`genre` = '" . Dba::escape($value) . "' AND ";
				break;
				default;
					// Rien a faire
				break;
			} // end filter
		break;
		case 'smartplaylist':
			switch ($filter) {
				case 'alpha_match':
					$filter_sql = " `search`.`name` LIKE '%" . Dba::escape($value) . "%' AND ";
				break;
				case 'starts_with':
					$filter_sql = " `search`.`name` LIKE '" . Dba::escape($value) . "%' AND ";
				break;
				case 'playlist_type':
					$user_id = intval($GLOBALS['user']->id);
					$filter_sql = " (`search`.`type` = 'public' OR `search`.`user`='$user_id') AND ";
				break;
				
			} // end switch on $filter
		break;
		case 'tag':
			switch ($filter) {
				case 'alpha_match':
					$filter_sql = " `tag`.`name` LIKE '%" . Dba::escape($value) . "%' AND ";
				break;
				case 'exact_match':
					$filter_sql = " `tag`.`name` = '" . Dba::escape($value) . "' AND ";
				break;
				case 'tag':
					$filter_sql = " `tag`.`id` = '" . Dba::escape($value) . "' AND ";
				break;
				case 'starts_with':
					$filter_sql = " `tag`.`name` LIKE '" . Dba::escape($value) . "%' AND ";
				break;
				default:
					// Rien a faire
				break;
			} // end filter
		break;
		case 'video':
			switch ($filter) {
				case 'alpha_match':
					$filter_sql = " `video`.`title` LIKE '%" . Dba::escape($value) . "%' AND ";
				break;
				case 'starts_with':
					$filter_sql = " `video`.`title` LIKE '" . Dba::escape($value) . "%' AND ";
				break;
				default:
					// Rien a faire
				break;
			} // end filter
		break;
		} // end switch on type

		return $filter_sql;

	} // sql_filter

	/**
	 * logic_filter
	 * This runs the filters that we can't easily apply
	 * to the sql so they have to be done after the fact
	 * these should be limited as they are often intensive and
	 * require additional queries per object... :(
	 */
	private function logic_filter($object_id) {

		return true;

	} // logic_filter

	/**
	 * sql_sort
	 * This builds any order bys we need to do
	 * to sort the results as best we can, there is also
	 * a logic based sort that will come later as that's
	 * a lot more complicated
	 */
	private function sql_sort($field, $order) {

		if ($order != 'DESC') { $order == 'ASC'; }

		// Depending on the type of browsing we are doing we can apply
		// different filters that apply to different fields
		switch ($this->get_type()) {
			case 'song':
				switch($field) {
					case 'title';
						$sql = "`song`.`title`";
					break;
					case 'year':
						$sql = "`song`.`year`";
					break;
					case 'time':
						$sql = "`song`.`time`";
					break;
					case 'track':
						$sql = "`song`.`track`";
					break;
					case 'album':
						$sql = '`album`.`name`';
						$this->set_join('left', '`album`', '`album`.`id`', '`song`.`album`', 100);
					break;
					case 'artist':
						$sql = '`artist`.`name`';
						$this->set_join('left', '`artist`', '`artist`.`id`', '`song`.`artist`', 100);
					break;
					default:
						// Rien a faire
					break;
				} // end switch
			break;
			case 'album':
				switch($field) {
					case 'name':
						$sql = "`album`.`name` $order, `album`.`disk`";
					break;
					case 'artist':
						$sql = "`artist`.`name`";
						$this->set_join('left', '`song`', '`song`.`album`', '`album`.`id`', 100);
						$this->set_join('left', '`artist`', '`song`.`artist`', '`artist`.`id`', 100);
					break;
					case 'year':
						$sql = "`album`.`year`";
					break;
				} // end switch
			break;
			case 'album_tag':
				switch($field) {
					case 'name':
						$sql = "`album`.`name` $order, `album`.`disk`";
					break;
					case 'artist':
						$sql = "`artist`.`name`";
						$this->set_join('left', '`song`', '`song`.`album`', '`album`.`id`', 100);
						$this->set_join('left', '`artist`', '`song`.`artist`', '`artist`.`id`', 100);
					break;
					case 'year':
						$sql = "`album`.`year`";
					break;
				} // end switch
			break;
			case 'artist':
				switch ($field) {
					case 'name':
						$sql = "`artist`.`name`";
					break;
				} // end switch
			break;
			case 'playlist':
				switch ($field) {
					case 'type':
						$sql = "`playlist`.`type`";
					break;
					case 'name':
						$sql = "`playlist`.`name`";
					break;
					case 'user':
						$sql = "`playlist`.`user`";
					break;
					case 'genre':
						$sql = "`playlist`.`genre`";
					break;
				} // end switch
			break;
			case 'smartplaylist':
				switch ($field) {
					case 'type':
						$sql = "`search`.`type`";
					break;
					case 'name':
						$sql = "`search`.`name`";
					break;
					case 'user':
						$sql = "`search`.`user`";
					break;
				} // end switch on $field
			break;
			case 'live_stream':
				switch ($field) {
					case 'name':
						$sql = "`live_stream`.`name`";
					break;
					case 'call_sign':
						$sql = "`live_stream`.`call_sign`";
					break;
					case 'frequency':
						$sql = "`live_stream`.`frequency`";
					break;
				} // end switch
			break;
			case 'genre':
				switch ($field) {
					case 'name':
						$sql = "`genre`.`name`";
					break;
				} // end switch
			break;
			case 'user':
				switch ($field) {
					case 'username':
						$sql = "`user`.`username`";
					break;
					case 'fullname':
						$sql = "`user`.`fullname`";
					break;
					case 'last_seen':
						$sql = "`user`.`last_seen`";
					break;
					case 'create_date':
						$sql = "`user`.`create_date`";
					break;
				} // end switch
			break;
			case 'video':
				switch ($field) {
					case 'title':
						$sql = "`video`.`title`";
					break;
					case 'resolution':
						$sql = "`video`.`resolution_x`";
					break;
					case 'length':
						$sql = "`video`.`time`";
					break;
					case 'codec':
						$sql = "`video`.`video_codec`";
					break;
				} // end switch on field
			break;
			default:
				// Rien a faire
			break;
		} // end switch

		if ($sql) { $sql_sort = "$sql $order,"; }

		return $sql_sort;

	} // sql_sort

	/**
	 * resort_objects
	 * This takes the existing objects, looks at the current
	 * sort method and then re-sorts them This is internally
	 * called by the set_sort() function
	 */
	private function resort_objects() {

		// There are two ways to do this.. the easy way...
		// and the vollmer way, hopefully we don't have to
		// do it the vollmer way
		if ($this->is_simple()) {
			$sql = $this->get_sql(true);
		}
		else {
			// First pull the objects
			$objects = $this->get_saved();

			// If there's nothing there don't do anything
			if (!count($objects) or !is_array($objects)) {
				return false;
			}
			$type = $this->get_type();
			$where_sql = "WHERE `$type`.`id` IN (";

			foreach ($objects as $object_id) {
				$object_id = Dba::escape($object_id);
				$where_sql .= "'$object_id',";
			}
			$where_sql = rtrim($where_sql,',');

			$where_sql .= ")";

			$sql = $this->get_base_sql();

			$order_sql = " ORDER BY ";

			foreach ($this->_state['sort'] as $key => $value) {
				$order_sql .= $this->sql_sort($key, $value);
			}
			// Clean her up
			$order_sql = rtrim($order_sql,"ORDER BY ");
			$order_sql = rtrim($order_sql,",");

			$sql = $sql . $this->get_join_sql() . $where_sql . $order_sql;
		} // if not simple

		$db_results = Dba::read($sql);

		while ($row = Dba::fetch_assoc($db_results)) {
			$results[] = $row['id'];
		}

		$this->save_objects($results);

		return true;

	} // resort_objects

	/**
	 * store
	 * This saves the current state to the database
	 */
	public function store() {
		$sid = Dba::escape(session_id());
		$id = Dba::escape($this->id);
		$data = Dba::escape(serialize($this->_state));

		$sql = "UPDATE `tmp_browse` SET `data`='$data' " .
			"WHERE `sid`='$sid' AND `id`='$id'";
		$db_results = Dba::write($sql);
	}

	/**
	 * save_objects
	 * This takes the full array of object ids, often passed into show and 
	 * if necessary it saves them
	 */
	public function save_objects($object_ids) {

		// Saving these objects has two operations, one holds it in
		// a local variable and then second holds it in a row in the 
		// tmp_browse table

		// Only do this if it's a not a simple browse
		if (!$this->is_simple()) {
			$this->_cache = $object_ids;
			$this->set_total(count($object_ids));
			$sid = Dba::escape(session_id());
			$id = Dba::escape($this->id);
			$data = Dba::escape(serialize($this->_cache));

			$sql = "UPDATE `tmp_browse` SET `object_data`='$data' " .
				"WHERE `sid`='$sid' AND `id`='$id'";
			$db_results = Dba::write($sql);
		} // save it

		return true;

	} // save_objects

	/**
	 * get_state
	 * This is a debug only function
	 */
	public function get_state() {

		return $this->_state;

	} // get_state

} // query
