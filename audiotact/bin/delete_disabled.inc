<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Delete Disabled
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
 * Delete Disabled
 * This WILL DELETE MUSIC!!!! it does what it's name would suggest and deletes
 * any disabled songs
 */

/* will not delete anything if debug is true */
$debug = true;

define('NO_SESSION','1');
$path = dirname(__FILE__);
$prefix = realpath($path . '/../');
require_once $prefix . '/lib/init.php';

if ($debug) { echo _("DEBUG ENABLED WILL NOT DELETE FILES!"); echo "\n"; }

/* Get a list of filenames */
$sql = "SELECT `id`,`file` FROM song WHERE enabled='0'";
$db_results = Dba::read($sql);

while ($row = Dba::fetch_assoc($db_results)) {
	if ($debug) {
		printf (_('Would Delete: %s'), $row['file']);
		echo "\n";
	}
	else {
		printf (_('Deleting: %s'), $row['file']);
		echo "\n";
		unlink($row['file']);
		$sql = "DELETE FROM `song` WHERE `id`='" . Dba::escape($row['id']) . "'";
		$del_results = Dba::write($sql);
	}
} // end while

?>
