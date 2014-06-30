<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Filename
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

// Switch on Action
switch ($_REQUEST['action']) {
	case 'show_create':
		if (!Access::check('interface','25')) {
			access_denied();
			exit;
		}

		require_once Config::get('prefix') . '/templates/show_add_live_stream.inc.php';

	break;
	case 'create':
		if (!Access::check('interface','25') || Config::get('demo_mode')) {
			access_denied();
			exit;
		}

		if (!Core::form_verify('add_radio','post')) {
			access_denied();
			exit;
		}

		// Try to create the sucker
		$results = Radio::create($_POST);

		if (!$results) {
			require_once Config::get('prefix') . '/templates/show_add_live_stream.inc.php';
		}
		else {
			$body = _('Radio Station Added');
			$title = '';
			show_confirmation($title,$body,Config::get('web_path') . '/index.php');
		}
	break;
} // end data collection

show_footer();

?>
