<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Playlist Genre
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

$web_path = Config::get('web_path');
?>

<?php Ajax::start_container('show_playlist_genre'); ?>

<?php $genres_col1 = array('Blues','Classique','Hip-Hop','Chanson francophone','Musiques du monde','Rock/Pop','Bruitages, samples'); ?>
<ul class="genre_col_1">
	<?php foreach ($genres_col1 as $data) { ?>
	<li id="click_<?php echo $data; ?>" class="<?php echo $data; ?>"><?php echo $data; ?></span>
	<?php echo Ajax::observe('click_' . $data,'click',Ajax::action('?page=playlist&action=add_filter&browse_id=' . $browse2->id . '&genre=' . $data,'')); ?>
	<?php } ?>
</ul>

<?php $genres_col2 = array('Expérimentale','Électronique','Jazz','Funk, soul','Création radiophonique','Musiques traditionnelles','Inclassables, autres'); ?>
<ul class="genre_col_2">
	<?php foreach ($genres_col2 as $data) { ?>
	<li id="click_<?php echo $data; ?>" class="<?php echo $data; ?>"><?php echo $data; ?></span>
	<?php echo Ajax::observe('click_' . $data,'click',Ajax::action('?page=playlist&action=add_filter&browse_id=' . $browse2->id . '&genre=' . $data,'')); ?>
	<?php } ?>
</ul>

<?php Ajax::end_container(); ?>
