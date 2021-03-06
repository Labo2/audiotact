<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Migrate Config
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

define('NO_SESSION','1'); 

$unmigratable = array('auth_methods'=>'mysql',
	'tag_order'=>'id3v2,id3v1,vorbiscomment,quicktime,ape,asf',
	'album_art_order'=>'db,id3,folder,lastfm,amazon',
	'amazon_base_urls'=>'http://webservices.amazon.com'); 

$translate = array('local_host'=>'database_hostname',
	'local_db'=>'database_name',
	'local_username'=>'database_username',
	'local_pass'=>'database_password',
	'local_length'=>'session_length',
	'stream_cmd_flac'=>'transcode_cmd_flac',
	'stream_cmd_mp3'=>'transcode_cmd_mp3',
	'stream_cmd_m4a'=>'transcode_cmd_m4a',
	'stream_cmd_ogg'=>'transcode_cmd_ogg',
	'stream_cmd_mpc'=>'transcode_cmd_mpc',
	'sess_name'=>'session_name',
	'sess_cookielife'=>'session_cookielife',
	'sess_cookiesecure'=>'session_cookiesecure'); 

$path = dirname(__FILE__);
$prefix = realpath($path . '/../');
$old_config = file_get_contents($prefix . '/config/ampache.cfg.php');

$data = explode("\n",$old_config);

echo _("Parsing old config file...");
echo "\n";

foreach ($data as $line) {

	// Replace a # with ;
	if ($line['0'] == '#') {
		$line = substr_replace($line,";",0,1);
	}

	foreach ($unmigratable as $option=>$default) {
		if (strstr($line,$option) AND !$migrated[$option]) {
			$line = $option . " = \"$default\"";
			$migrated[$option] = true;
		}
		elseif (strstr($line,$option)) {
			$line = ';' . $line;
		}
	}

	foreach ($translate as $old=>$new) {
		if (strstr($line,$old)) {
			$line = str_replace($old,$new,$line);
		}
	}

	$new_config .= $line . "\n";

} // end foreach lines

echo _("Parse complete, writing");
echo "\n";

$handle = fopen($prefix . '/config/ampache.cfg.php','w');

$worked = fwrite($handle,$new_config);

if ($worked) {
	echo _("Write success, config migrated");
	echo "\n";
}
else {
	echo _("Access Denied, config migration failed");
	echo "\n";
}

?>
