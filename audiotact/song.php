<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Song
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

require 'lib/init.php';

show_header();

$show_lyrics = Config::get('show_lyrics');

// Switch on Action
switch ($_REQUEST['action']) {
	default:
	case 'show_song':
		$song = new Song($_REQUEST['song_id']);
		$song->format();
		$song->fill_ext_info();
		require_once Config::get('prefix') . '/templates/show_song.inc.php';
		// does user want to display lyrics?
		if($show_lyrics == 1) {
			$lyric = new Artist();
			$return = $lyric->get_song_lyrics($song->id, ucwords($song->f_artist), ucwords($song->title));
			$link = '<a href="http://lyricwiki.org/' . rawurlencode(ucwords($song->f_artist)) . ':' . rawurlencode(ucwords($song->title)) . '" target="_blank">';
			/* HINT: Artist, Song Title */
			$link .= sprintf(_('%1$s - %2$s Lyrics Detail'), ucwords($song->f_artist), ucwords($song->title));
			$link .= "</a><br /><br />";
			require_once Config::get('prefix') . '/templates/show_lyrics.inc.php';
		}
	break;
	case 'show_lyrics':
		if($show_lyrics == 1) {
			$song = new Song($_REQUEST['song_id']);
			$song->format();
			$song->fill_ext_info();
			require_once Config::get('prefix') . '/templates/show_lyrics_song.inc.php';

			// get the lyrics
			$show_lyrics = Config::get('show_lyrics');
			$lyric = new Artist();
			$return = $lyric->get_song_lyrics($song->id, ucwords($song->f_artist), ucwords($song->title));
			$link = '<a href="http://lyricwiki.org/' . rawurlencode(ucwords($song->f_artist)) . ':' . rawurlencode(ucwords($song->title)) . '" target="_blank">';
			/* HINT: Artist, Song Title */
			$link .= sprintf(_('%1$s - %2$s Lyrics Detail'), ucwords($song->f_artist), ucwords($song->title));
			$link .= "</a><br /><br />";
			require_once Config::get('prefix') . '/templates/show_lyrics.inc.php';
		}
} // end data collection

show_footer();

?>
