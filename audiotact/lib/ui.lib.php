<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * UI Function Library
 *
 * This contains functions that are generic, and display information
 * things like a confirmation box, etc and so forth
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
 * Audiotact is an Ampache-based project developped by Oudeis (www.oudeis.fr) with the support of the Labo2 (www.bibliotheque.nimes.fr)

 */

/**
 * show_confirmation
 *
 * shows a confirmation of an action
 *
 * @param	string	$title	The Title of the message
 * @param	string	$text	The details of the message
 * @param	string	$next_url	Where to go next
 * @param	integer	$cancel	T/F show a cancel button that uses return_referrer()
 * @return	void
 */
function show_confirmation($title,$text,$next_url,$cancel=0,$form_name='confirmation') {

	if (substr_count($next_url,Config::get('web_path'))) {
		$path = $next_url;
	}
	else {
		$path = Config::get('web_path') . "/$next_url";
	}

	require Config::get('prefix') . '/templates/show_confirmation.inc.php';

} // show_confirmation

/**
 * flip_class
 *
 * First called with an array of 2 class names.  Subsequent calls reverse the
 * array then return the first element.
 *
 * @param	array	$array
 * @return	mixed	void or classname
 */
function flip_class($array=0) {

	static $classes = array();

	if ($array) {
		$classes = $array;
	}
	else {
		$classes = array_reverse($classes);
		return $classes[0];
	}

} // flip_class

/**
 * _
 * Check to see if the gettext alias _ is defined. If it isn't we define it as
 * a noop.
 */
if (!function_exists('_')) {

	function _($string) {
		return $string;
	} // _

} // if _ isn't defined

/**
 * ngettext
 * checks for ngettext and defines it if it doesn't exist
 */
if (!function_exists('ngettext')) {
	function ngettext($string) {
		return $string;
	}
} // if no ngettext

/**
 * access_denied
 * Throws an error if they try to do something that they aren't allowed to.
 */
function access_denied($error = "Access Denied") {

	// Clear any crap we've got up top
	ob_end_clean();
	header("HTTP/1.1 403 $error");
	require_once Config::get('prefix') . '/templates/show_denied.inc.php';
	exit;

} // access_denied

/**
 * return_referer
 * returns the script part of the referer address passed by the web browser
 * this is not %100 accurate. Also because this is not passed by us we need
 * to clean it up, take the filename then check for a /admin/ and dump the rest
 */
function return_referer() {

	$referer = $_SERVER['HTTP_REFERER'];
	if (substr($referer, -1)=='/'){
		$file = 'index.php';
	}
	else {
		$file = basename($referer);
		/* Strip off the filename */
		$referer = substr($referer,0,strlen($referer)-strlen($file));
	}

	if (substr($referer,strlen($referer)-6,6) == 'admin/') {
		$file = 'admin/' . $file;
	}

	return $file;

} // return_referer

/**
 * truncate_with_ellipsis
 * Function that truncates text to a specific length and appends an ellipsis to
 * the end.
 */
function truncate_with_ellipsis($text, $max='') {

	$max = $max ? $max : '27';

	/* If they want it to be shorter than three, just throw it back */
	if ($max <= 3) {
		return $text;
	}

	/* Make sure the functions exist before doing the iconv mojo */
	if (function_exists('iconv') &&
		function_exists('iconv_substr') &&
		function_exists('iconv_strlen')) {
		$charset = Config::get('site_charset');
		if (iconv_strlen($text, $charset) > $max) {
			$text = iconv_substr($text, 0, $max-3, $charset);
			$text .= iconv("ISO-8859-1", $charset, "...");
		}
	}
	else { // Use normal substr if we don't have iconv
		if (strlen($text) > $max) {
			$text = substr($text,0,$max-3)."...";
		}
	} // else no iconv

	return $text;

} // truncate_with_ellipsis

/**
 * show_header
 * This shows the header.inc.php, it may do something
 * more in the future
 */
function show_header() {

	require_once Config::get('prefix') . '/templates/header.inc.php';

} // show_header

/**
 * show_footer
 * shows the footer of the page
 */
function show_footer() {

	require_once Config::get('prefix') . '/templates/footer.inc.php';
	if (isset($_REQUEST['profiling'])) {
	  Dba::show_profile();
	}

} // show_footer

/**
 * get_location
 * This function gets the information about a person's current location.
 * This is used for A) sidebar highlighting & submenu showing and B) titlebar
 * information. It returns an array of information about what they are currently
 * doing.
 * Possible array elements
 * ['title']	Text name for the page
 * ['page']	actual page name
 * ['section']	name of the section we are in, admin, browse etc (submenu)
 */
function get_location() {

	$location = array();

	if (strlen($_SERVER['PHP_SELF'])) {
		$source = $_SERVER['PHP_SELF'];
	}
	else {
		$source = $_SERVER['REQUEST_URI'];
	}

	/* Sanatize the $_SERVER['PHP_SELF'] variable */
	$source = str_replace(Config::get('raw_web_path'), "", $source);
	$location['page'] 	= preg_replace("/^\/(.+\.php)\/?.*/","$1",$source);

	switch ($location['page']) {
		case 'index.php':
			$location['title'] 	= _('Home');
			break;
		case 'upload.php':
			$location['title'] 	= _('Upload');
			break;
		case 'localplay.php':
			$location['title'] 	= _('Local Play');
			break;
		case 'randomplay.php':
			$location['title'] 	= _('Random Play');
			break;
		case 'playlist.php':
			$location['title'] 	= _('Playlist');
			break;
		case 'search.php':
			$location['title'] 	= _('Search');
			break;
		case 'preferences.php':
			$location['title'] 	= _('Preferences');
			break;
		case 'admin/index.php':
			$location['title'] 	= _('Admin-Catalog');
			$location['section']	= 'admin';
			break;
		case 'admin/catalog.php':
			$location['title'] 	= _('Admin-Catalog');
			$location['section']	= 'admin';
			break;
		case 'admin/users.php':
			$location['title']	= _('Admin-User Management');
			$location['section']	= 'admin';
			break;
		case 'admin/mail.php':
			$location['title']	= _('Admin-Mail Users');
			$location['section']	= 'admin';
			break;
		case 'admin/access.php':
			$location['title']	= _('Admin-Manage Access Lists');
			$location['section']	= 'admin';
			break;
		case 'admin/preferences.php':
			$location['title']	= _('Admin-Site Preferences');
			$location['section']	= 'admin';
			break;
		case 'admin/modules.php':
			$location['title']	= _('Admin-Manage Modules');
			$location['section']	= 'admin';
			break;
		case 'browse.php':
			$location['title']	= _('Browse Music');
			$location['section']	= 'browse';
			break;
		case 'albums.php':
			$location['title']	= _('Albums');
			$location['section']	= 'browse';
			break;
		case 'artists.php':
			$location['title']	= _('Artists');
			$location['section']	= 'browse';
			break;
		case 'stats.php':
			$location['title']	= _('Statistics');
			break;
		default:
			$location['title'] = '';
			break;
	} // switch on raw page location

	return $location;

} // get_location

/**
 * show_preference_box
 * This shows the preference box for the preferences pages.
 */
function show_preference_box($preferences) {

	require Config::get('prefix') . '/templates/show_preference_box.inc.php';

} // show_preference_box

/**
 * show_album_select
 * This displays a select of every album that we've got in Ampache (which can be
 * hella long). It's used by the Edit page and takes a $name and a $album_id
 */
function show_album_select($name='album',$album_id=0,$allow_add=0,$song_id=0) {
	// Generate key to use for HTML element ID
	static $id_cnt;
	if ($song_id) {
		$key = "album_select_$song_id";
	} else {
		$key = "album_select_c" . ++$id_cnt;
	}

	// Added ID field so we can easily observe this element
	echo "<select name=\"$name\" id=\"$key\">\n";

	$sql = "SELECT `id`, `name`, `prefix` FROM `album` ORDER BY `name`";
	$db_results = Dba::read($sql);

	while ($r = Dba::fetch_assoc($db_results)) {
		$selected = '';
		$album_name = trim($r['prefix'] . " " . $r['name']);
		if ($r['id'] == $album_id) {
			$selected = "selected=\"selected\"";
		}

		echo "\t<option value=\"" . $r['id'] . "\" $selected>" . scrub_out($album_name) . "</option>\n";

	} // end while

	if ($allow_add) {
		// Append additional option to the end with value=-1
		echo "\t<option value=\"-1\">" . _('Add New') . "...</option>\n";
	}

	echo "</select>\n";

} // show_album_select

/**
 * show_artist_select
 * This is the same as show_album_select except it's *gasp* for artists! How
 * inventive!
 */
function show_artist_select($name='artist', $artist_id=0, $allow_add=0, $song_id=0) {
	// Generate key to use for HTML element ID
	static $id_cnt;
	if ($song_id) {
		$key = "artist_select_$song_id";
	} else {
		$key = "artist_select_c" . ++$id_cnt;
	}

	echo "<select name=\"$name\" id=\"$key\">\n";

	$sql = "SELECT `id`, `name`, `prefix` FROM `artist` ORDER BY `name`";
	$db_results = Dba::read($sql);

	while ($r = Dba::fetch_assoc($db_results)) {
		$selected = '';
		$artist_name = trim($r['prefix'] . " " . $r['name']);
		if ($r['id'] == $artist_id) {
			$selected = "selected=\"selected\"";
		}

		echo "\t<option value=\"" . $r['id'] . "\" $selected>" . scrub_out($artist_name) . "</option>\n";

	} // end while

	if ($allow_add) {
		// Append additional option to the end with value=-1
		echo "\t<option value=\"-1\">Add New...</option>\n";
	}

	echo "</select>\n";

} // show_artist_select

/**
 * show_catalog_select
 * Yet another one of these buggers. this shows a drop down of all of your
 * catalogs.
 */
function show_catalog_select($name='catalog',$catalog_id=0,$style='') {

	echo "<select name=\"$name\" style=\"$style\">\n";

	$sql = "SELECT `id`, `name` FROM `catalog` ORDER BY `name`";
	$db_results = Dba::read($sql);

	while ($r = Dba::fetch_assoc($db_results)) {
		$selected = '';
		if ($r['id'] == $catalog_id) {
			$selected = "selected=\"selected\"";
		}

		echo "\t<option value=\"" . $r['id'] . "\" $selected>" . scrub_out($r['name']) . "</option>\n";

	} // end while

	echo "</select>\n";

} // show_catalog_select

/**
 * show_user_select
 * This one is for users! shows a select/option statement so you can pick a user
 * to blame
 */
function show_user_select($name,$selected='',$style='') {

	echo "<select name=\"$name\" style=\"$style\">\n";
	echo "\t<option value=\"\">" . _('All') . "</option>\n";

	$sql = "SELECT `id`,`username`,`fullname` FROM `user` ORDER BY `fullname`";
	$db_results = Dba::read($sql);

	while ($row = Dba::fetch_assoc($db_results)) {
		$select_txt = '';
		if ($row['id'] == $selected) {
			$select_txt = 'selected="selected"';
		}
		// If they don't have a full name, revert to the username
		$row['fullname'] = $row['fullname'] ? $row['fullname'] : $row['username'];

		echo "\t<option value=\"" . $row['id'] . "\" $select_txt>" . scrub_out($row['fullname']) . "</option>\n";
	} // end while users

	echo "</select>\n";

} // show_user_select

/**
 * show_playlist_select
 * This one is for playlists!
 */
function show_playlist_select($name,$selected='',$style='') {

	echo "<select name=\"$name\" style=\"$style\">\n";
	echo "\t<option value=\"\">" . _('None') . "</option>\n";

	$sql = "SELECT `id`,`name` FROM `playlist` ORDER BY `name`";
	$db_results = Dba::read($sql);

	while ($row = Dba::fetch_assoc($db_results)) {
		$select_txt = '';
		if ($row['id'] == $selected) {
			$select_txt = 'selected="selected"';
		}
		// If they don't have a full name, revert to the username
		echo "\t<option value=\"" . $row['id'] . "\" $select_txt>" . scrub_out($row['name']) . "</option>\n";
	} // end while users

	echo "</select>\n";

} // show_playlist_select

/**
 * show_box_top
 * This function requires the top part of the box
 * it takes title as an optional argument
 */
function show_box_top($title='',$class='') {

	require Config::get('prefix') . '/templates/show_box_top.inc.php';

} // show_box_top

/**
 * show_box_bottom
 * This function requires the bottom part of the box
 * it does not take any arguments
 */
function show_box_bottom() {

	require Config::get('prefix') . '/templates/show_box_bottom.inc.php';

} // show_box_bottom

/**
 * get_user_icon
 * this function takes a name and a returns either a text representation
 * or an <img /> tag
 */
function get_user_icon($name,$title='',$id='') {

	/* Because we do a lot of calls cache the URLs */
	static $url_cache = array();

	// If our name is an array
	if (is_array($name)) {
		$hover_name = $name['1'];
		$name = $name['0'];
	}

	if (!$title) { $title = _(ucfirst($name)); }

	if ($id) {
		$id = ' id="' . $id . '" ';
	}

	if (isset($url_cache[$name])) {
		$img_url = $url_cache[$name];
		$cache_url = true;
	}

	if (empty($hover_name)) {
		$cache_hover = true;
		$hov_txt = '';
	}
	elseif (isset($url_cache[$hover_name])) {
		$hover_url = $url_cache[$hover_name];
		$cache_hover = true;
	}

	if (!isset($cache_url) OR !isset($cache_hover)) {

		$icon_name = 'icon_' . $name . '.png';

		/* Build the image url */
		if (file_exists(Config::get('prefix') . Config::get('theme_path') . '/images/icons/' . $icon_name)) {
			$img_url = Config::get('web_path') . Config::get('theme_path') . '/images/icons/' . $icon_name;
		}
		else {
			$img_url = Config::get('web_path') . '/images/' . $icon_name;
		}

		$url_cache[$name] = $img_url;

		/* If Hover, then build its url */
		if (!empty($hover_name)) {
			$hover_icon = 'icon_' . $hover_name . '.png';
			if (file_exists(Config::get('prefix') . Config::get('theme_path') . '/images/icons/' . $icon_name)) {
				$hov_url = Config::get('web_path') . Config::get('theme_path') . '/images/icons/' . $hover_icon;
			}
			else {
				$hov_url = Config::get('web_path') . '/images/' . $hover_icon;
			}

			$hov_txt = " onmouseover=\"this.src='$hov_url'; return true;\" onmouseout=\"this.src='$img_url'; return true;\" ";
			$url_cache[$hover_name] = $hov_txt;
		} // end hover

	} // end if not cached

	$string = '<img src="' . $img_url . '"' . $id . 'alt="' . $title . '" title="' . $title . '"' . $hov_txt . '/>';

	return $string;

} // get_user_icon

/**
 * xml_from_array
 * This takes a one dimensional array and creates a XML document from it. For
 * use primarily by the ajax mojo.
 */
function xml_from_array($array, $callback = false, $type = '') {

	$string = '';

	// If we weren't passed an array then return
	if (!is_array($array)) { return $string; }

	// The type is used for the different XML docs we pass
	switch ($type) {
	case 'itunes':
		foreach ($array as $key=>$value) {
			if (is_array($value)) {
				$value = xml_from_array($value,1,$type);
				$string .= "\t\t<$key>\n$value\t\t</$key>\n";
			}
			else {
				if ($key == "key"){
				$string .= "\t\t<$key>$value</$key>\n";
				} elseif (is_int($value)) {
				$string .= "\t\t\t<key>$key</key><integer>$value</integer>\n";
				} elseif ($key == "Date Added") {
				$string .= "\t\t\t<key>$key</key><date>$value</date>\n";
				} elseif (is_string($value)) {
				/* We need to escape the value */
				$string .= "\t\t\t<key>$key</key><string><![CDATA[$value]]></string>\n";
				}
			}

		} // end foreach

		return $string;
	break;
	case 'xspf':
		foreach ($array as $key=>$value) {
			if (is_array($value)) {
				$value = xml_from_array($value,1,$type);
				$string .= "\t\t<$key>\n$value\t\t</$key>\n";
			}
			else {
				if ($key == "key"){
				$string .= "\t\t<$key>$value</$key>\n";
				} elseif (is_numeric($value)) {
				$string .= "\t\t\t<$key>$value</$key>\n";
				} elseif (is_string($value)) {
				/* We need to escape the value */
				$string .= "\t\t\t<$key><![CDATA[$value]]></$key>\n";
				}
			}

		} // end foreach

		return $string;
	break;
	default:
		foreach ($array as $key => $value) {
			// No numeric keys
			if (is_numeric($key)) {
				$key = 'item';
			}

			if (is_array($value)) {
				// Call ourself
				$value = xml_from_array($value, true);
				$string .= "\t<content div=\"$key\">$value</content>\n";
			}
			else {
				/* We need to escape the value */
				$string .= "\t<content div=\"$key\"><![CDATA[$value]]></content>\n";
			}
		// end foreach elements
		}
		if (!$callback) {
			$string = '<?xml version="1.0" encoding="utf-8" ?>' .
				"\n<root>\n" . $string . "</root>\n";
		}

		// Remove invalid XML characters.
		// See http://www.w3.org/TR/2006/REC-xml-20060816/#charsets
		$clean = preg_replace('/[\x{0}-\x{8}\x{b}\x{c}\x{e}-\x{1f}\x{d800}-\x{dfff}\x{fffe}-\x{ffff}]/u', '', $string);

		if ($clean) {
			return $clean;
		}
		else {
			debug_event('xml_from_array', 'Charset cleanup failed, generated XML may be invalid', 1);
			return $string;
		}
	break;
	}
} // xml_from_array

/**
 * xml_get_header
 * This takes the type and returns the correct xml header
 */
function xml_get_header($type){
	switch ($type){
	case 'itunes':
		$header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
		"<!DOCTYPE plist PUBLIC \"-//Apple Computer//DTD PLIST 1.0//EN\"\n" .
		"\"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">\n" .
		"<plist version=\"1.0\">\n" .
		"<dict>\n" .
		"       <key>Major Version</key><integer>1</integer>\n" .
		"       <key>Minor Version</key><integer>1</integer>\n" .
		"       <key>Application Version</key><string>7.0.2</string>\n" .
		"       <key>Features</key><integer>1</integer>\n" .
		"       <key>Show Content Ratings</key><true/>\n" .
		"       <key>Tracks</key>\n" .
		"       <dict>\n";
		return $header;
	break;
	case 'xspf':
		$header = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n" .
			"<!-- XML Generated by Ampache v." .  Config::get('version') . " -->";
			"<playlist version = \"1\" xmlns=\"http://xspf.org/ns/0/\">\n ".
			"<title>Ampache XSPF Playlist</title>\n" .
			"<creator>" . Config::get('site_title') . "</creator>\n" .
			"<annotation>" . Config::get('site_title') . "</annotation>\n" .
			"<info>". Config::get('web_path') ."</info>\n" .
			"<trackList>\n\n\n\n";
		return $header;
	break;
	default:
		$header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		return $header;
	break;
	}
} //xml_get_header

/**
 * xml_get_footer
 * This takes the type and returns the correct xml footer
 */
function xml_get_footer($type){
	switch ($type){
	case 'itunes':
		$footer = "      </dict>\n" .
		"</dict>\n" .
		"</plist>\n";
		return $footer;
	break;
	case 'xspf':
		$footer = "	  </trackList>\n" .
			  "</playlist>\n";
		return $footer;
	break;
	default:

	break;
	}
} // xml_get_footer

/**
 * ajax_include
 * This does an ob_start, getcontents, clean
 * on the specified require, only works if you
 * don't need to pass data in
 */
function ajax_include($include) {

	ob_start();
	require_once Config::get('prefix') . '/templates/' . $include;
	$results = ob_get_contents();
	ob_end_clean();

	return $results;

} // ajax_include

/**
 * toggle_visible
 * This is identical to the javascript command that it actually calls
 */
function toggle_visible($element) {

	echo '<script type="text/javascript">';
	echo "toggleVisible('$element');";
	echo "</script>\n";

} // toggle_visible

/**
 * update_text
 * Convenience function
 */
function update_text($field, $value) {
	echo '<script type="text/javascript">';
	echo "updateText('$field', '$value');";
	echo "</script>\n";
}

/**
 * print_bool
 * This function takes a boolean value and then prints out a friendly text
 * message.
 */
function print_bool($value) {

	if ($value) {
		$string = '<span class="item_on">' . _('On') . '</span>';
	}
	else {
		$string = '<span class="item_off">' . _('Off') . '</span>';
	}

	return $string;

} // print_bool

/**
 * show_now_playing
 * This shows the now playing templates and does some garbage collecion
 * this should really be somewhere else
 */
function show_now_playing() {

	Stream::gc_session();
	Stream::gc_now_playing();

	$web_path = Config::get('web_path');
	$results = Stream::get_now_playing();
	require_once Config::get('prefix') . '/templates/show_now_playing.inc.php';

} // show_now_playing


?>
