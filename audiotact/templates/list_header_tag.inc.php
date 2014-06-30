<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * List Header
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
 * List Header
 * The default pager widget for moving through a list of many items.
 * This relies heavily on the View object to get pieces about how
 * to layout this page.
 */

// Pull these variables out to allow shorthand (easier for lazy programmers)
$limit	= '6';
$start	= $browse->get_start();
$total	= $browse->get_total();
$uid	= Config::get('list_header_uid');
$sides  = 6;

// ++ the uid
Config::set('list_header_uid', $uid + 1, 1);

// Next
$next_offset = $start + $limit;
if ($next_offset > $total) { $next_offset = $start; }

// Prev
$prev_offset = $start - $limit;
if ($prev_offset < 0) { $prev_offset = '0'; }

/* Calculate how many pages total exist */
if ($limit > 0 && $total > $limit) {
	$pages = ceil($total / $limit);
}
else {
	$pages = 0;
}

// are there enough items to even need this view?
if ($pages > 1) {

	/* Calculate current page and how many we have on each side */
	$page_data = array('up' => array(), 'down' => array());

	// Can't divide by 0
	if ($start > 0) {
		$current_page = floor($start / $limit);
	}
	else {
		$current_page = 0;
	}

	// Create 10 pages in either direction
	// Down first
	$page = $current_page;
	$i = 0;
	while ($page > 0) {
		if ($i == $sides) { $page_data['down'][1] = '...'; $page_data['down'][0] = '0'; break; }
		$i++;
		$page = $page - 1;
		$page_data['down'][$page] = $page * $limit;
	} // while page > 0

	// Then up
	$page = $current_page + 1;
	$i = 0;
	while ($page <= $pages) {
		if ($page * $limit > $total) { break; }
		if ($i == $sides) {
			$key = $pages - 1;
			if (!$page_data['up'][$key]) { $page_data['up'][$key] = '...'; }
			$page_data['up'][$pages] = ($pages - 1) * $limit;
			break;
		}
		$i++;
		$page = $page + 1;
		$page_data['up'][$page] = ($page - 1) * $limit;
	} // end while

	// Sort these arrays of hotness
	ksort($page_data['up']);
	ksort($page_data['down']);
?>
<div class="list-header">
<?php
$id = $browse->id;
echo Ajax::text('?page=browse&action=page&browse_id=' . $browse->id . '&start=' . $prev_offset,_('Prev'),'browse_' . $id . 'prev','','browse_prev'); 
echo Ajax::text('?page=browse&action=page&browse_id=' . $browse->id . '&start=' . $next_offset,_('Next'),'browse_' . $id . 'next','','browse_next'); 
?>
</div>
<?php
} // if stuff
?>
