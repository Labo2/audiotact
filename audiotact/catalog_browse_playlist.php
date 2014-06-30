<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Browse
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
session_start();

$browse = new Browse();
$browse->set_simple_browse(true);
$browse->set_type('playlist');
$browse->set_offset('6');
$browse->set_filter('playlist_type','public');

//$browse->set_filter('catalog',$_SESSION['catalog']);
$browse->set_sort('name','ASC');


$browse->store();

$browse2 = new Browse();
$browse2->set_type('playlist');
$browse2->set_offset('6');
$browse2->store();

$browse3 = new Browse();
$browse3->set_type('playlist');
$browse3->set_offset('6');
$browse3->store();

$browse4 = new Browse();
$browse4->set_type('playlist');
$browse4->set_offset('6');
$browse->set_filter('playlist_type','public');
$browse4->store();

require_once Config::get('prefix') . '/templates/show_playlist_genre.inc.php';
require_once Config::get('prefix') . '/templates/show_playlist_filter.inc.php';
require_once Config::get('prefix') . '/templates/browse_content_playlist.inc.php';
?>
