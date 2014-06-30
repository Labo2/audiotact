<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Gather Art
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
?>

<div id="scrollbar_wrapper">
	<div id="lightbox_wrapper">
		<?php show_box_top('','update-box'); ?>
			<h1>Mise à jour du catalogue terminée</h1>
			<h2>Total des titres ajoutés : <?php echo $this->count; ?></h2>
			<h3>Titres ajoutés</h3>
				<ul><?php foreach ($items as $id) {
							$song = new Song($id); $song->format(); ?>
							<li><?php echo ($song->title.' - '.$song->f_artist.' - '.$song->f_album); ?></li>
					<?php } ?>
				</ul>
		<?php show_box_bottom(); ?>
	</div><!-- #lightbox_wrapper -->
</div><!-- #scrollbar_wrapper -->
