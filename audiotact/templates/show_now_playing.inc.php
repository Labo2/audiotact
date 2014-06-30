<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Now Playing
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
 * This is the now playing container, it holds the master div for now playing
 * and loops through what's current playing as passed and includes
 * the now_playing_row's This will display regardless, but potentially
 * goes all ajaxie if you've got javascript on
 */

if (count($results)) {
$link = Config::get('use_rss') ? ' ' . AmpacheRSS::get_display('nowplaying') : '';
?>
<?php show_box_top(_('Now Playing') . $link); ?>
<?php
foreach ($results as $item) {
	$media = $item['media'];
	$np_user = $item['client'];
	$agent = $item['agent'];

	/* If we've gotten a non-song object just skip this row */
	if (!is_object($media)) { continue; }
	if (!$np_user->fullname) { $np_user->fullname = "Ampache User"; }
?>
<div class="np_row">
<?php require Config::get('prefix') . '/templates/show_now_playing_row.inc.php'; ?>
</div>
<?php
} // end foreach
?>
<?php show_box_bottom(); ?>
<?php } // end if count results ?>
