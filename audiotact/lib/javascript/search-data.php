<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**

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
require_once '../init.php';

function arrayToJSON($array) {
	$json = '{ ';
	foreach ($array as $key => $value) {
		$json .= '"' . $key . '" : ';
		if (is_array($value)) {
			$json .= arrayToJSON($value);
		}
		else {
			$json .= '"' . $value . '"';
		}
		$json .= ' , ';
	}
	$json = rtrim($json, ', ');
	return $json . ' }';
}

Header('content-type: application/x-javascript');

$search = new Search($_REQUEST['type']);

echo 'var types = $H(\'';
echo arrayToJSON($search->types) . "'.evalJSON());\n";
echo 'var basetypes = $H(\'';
echo arrayToJSON($search->basetypes) . "'.evalJSON());\n";
echo 'removeIcon = \'<a href="javascript: void(0)">' . get_user_icon('disable', _('Remove')) . '</a>\';';
?>
