<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Index Ajax
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
 * Sub-Ajax page, requires AJAX_INCLUDE
 */
if (!defined('AJAX_INCLUDE')) { exit; }

switch ($_REQUEST['action']) {
	case 'random_albums':
		$albums = Album::get_random_albums('6');
		if (count($albums) AND is_array($albums)) {
			ob_start();
			require_once Config::get('prefix') . '/templates/show_random_albums.inc.php';
			$results['random_selection'] = ob_get_clean();
		}
		else {
			$results['random_selection'] = '<!-- None found -->';
		}
	break;
	case 'reloadnp':
		ob_start();
		show_now_playing();
		$results['now_playing'] = ob_get_clean();
		ob_start();
		$data = Song::get_recently_played();
		Song::build_cache(array_keys($data));
		if (count($data)) {
                        require_once Config::get('prefix') . '/templates/show_recently_played.inc.php';
		}
		$results['recently_played'] = ob_get_clean();
	break;
	case 'sidebar':
                switch ($_REQUEST['button']) {
                        case 'home':
                        case 'modules':
                        case 'localplay':
                        case 'player':
                        case 'preferences':
                                $button = $_REQUEST['button'];
                        break;
                        case 'admin':
                                if (Access::check('interface','100')) { $button = $_REQUEST['button']; }
                                else { exit; }
                        break;
                        default:
                                exit;
                        break;
                } // end switch on button

                ob_start();
                $_SESSION['state']['sidebar_tab'] = $button;
                require_once Config::get('prefix') . '/templates/sidebar.inc.php';
                $results['sidebar'] = ob_get_contents();
                ob_end_clean();
	default:
		$results['rfc3514'] = '0x1';
	break;
} // switch on action;

// We always do this
echo xml_from_array($results);
?>
