<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Artists Search
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

session_start();
$web_path = Config::get('web_path');
/*require Config::get('prefix') . '/templates/list_header.inc.php';*/ ?>

<h1>ARTISTES</h1>
<?php foreach ($object_ids as $artist_id) {
		$artist = new Artist($artist_id, $_SESSION['catalog']);
		$artist->format(); ?>
		<div id="artist_<?php echo $artist->id; ?>" class="catalog_item">
			<?php require Config::get('prefix') . '/templates/show_artist_row.inc.php'; ?>
		</div>
<?php } ?>
<?php if (!count($object_ids)) { ?><div class="<?php echo flip_class(); ?>"><span class="fatalerror"><?php echo ("Aucun artiste correspondant à vos critères"); ?></span></div><?php } ?>

