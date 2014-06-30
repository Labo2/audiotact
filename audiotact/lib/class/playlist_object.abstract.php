<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/*

 Copyright (c) Ampache.org
 All rights reserved.

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License v2
 as published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

/**
 * playlist_object
 * Abstracting out functionality needed by both normal and smart playlists
 */
abstract class playlist_object extends database_object {

	// Database variables
	public $id;
	public $name;
	public $user;
	public $type;

	/**
	 * format
	 * This takes the current playlist object and gussies it up a little
	 * bit so it is presentable to the users
	 */
	public function format() {

		$this->f_name =  truncate_with_ellipsis($this->name,Config::get('ellipse_threshold_title'));
		$this->f_type = ($this->type == 'private') ? get_user_icon('lock',_('Private')) : '';

		$client = new User($this->user);

		$this->f_user = $client->fullname;

	} // format

	/**
	 * has_access
	 * This function returns true or false if the current user
	 * has access to this playlist
	 */
	public function has_access() {

		if (!Access::check('interface','25')) {
			return false;
		}
		if ($this->user == $GLOBALS['user']->id) {
			return true;
		}
		else {
			return Access::check('interface','100');
		}

		return false;

	} // has_access


} // end playlist_object
