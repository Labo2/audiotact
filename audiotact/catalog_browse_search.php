<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Search
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
 * Browse Page
 * This page shows the browse menu, which allows you to browse by many different
 * fields including artist, album, and catalog.
 *
 * This page also handles the actual browse action
 */

/* Base Require */
require_once 'lib/init.php';
$web_path = Config::get('web_path');
$ajax_url = Config::get('ajax_url');
session_start();

switch ($_REQUEST['action']) {
	case 'search':
	show_box_top('',$class);
	$_REQUEST['rule_1'] = 'album';
		$browse = new Browse();
		$results = Search::run($_REQUEST);
		$albums=array();
		foreach ($results as $song) {
			$song = new Song($song);
			$song->format();
			$albums[]=$song->album;
			}
		$albums = array_unique($albums);
		$browse->set_type('album');
		$browse->set_offset('4');
		$browse->show_search_objects($albums);
		$browse->store();

	$_REQUEST['rule_1'] = 'title';
		$browse2 = new Browse();
		$results = Search::run($_REQUEST);
		$browse2->set_type('song');
		$browse2->set_offset('9');
		$browse2-> show_search_objects($results);
		$browse2->store();

	$_REQUEST['rule_1'] = 'artist';
		$browse = new Browse();
		$results = Search::run($_REQUEST);
		$albums=array();
		foreach ($results as $song) {
			$song = new Song($song);
			$song->format();
			$artists[]=$song->artist;
			}
		$artists = array_unique($artists);
		$browse->set_type('artist');
		$browse->set_offset('4');
		$browse-> show_search_objects($artists);
		$browse->store();
	
	$_REQUEST['rule_1'] = 'playlist';
		$browse = new Browse();
		$results = Search::run($_REQUEST);
		$browse->set_type('playlist');
		$browse->set_offset('4');
		$browse-> show_search_objects($results);
		$browse->store();?>

		<!--<div id="footer_filter_catalog">
			<div id="sub_tab_filter"></div>
			<div id="global_alphabet"></div>		
		</div>-->
		<?php show_box_bottom();	
	break;
	default:
		require_once Config::get('prefix') . '/templates/show_search.inc.php';
	break;
} /* end switch*/ ?>




