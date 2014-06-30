<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Index
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

?>
<div id="now_playing">
        <?php show_now_playing(); ?>
</div> <!-- Close Now Playing Div -->
<!-- Randomly selected albums of the moment -->
<?php
if (Art::is_enabled()) {
	echo Ajax::observe('window','load',Ajax::action('?page=index&action=random_albums','random_albums'));
?>
<div id="random_selection">
	<?php show_box_top(_('Albums of the Moment')); echo _('Loading...'); show_box_bottom(); ?>
</div>
<?php } ?>
<!-- Recently Played -->
<div id="recently_played">
        <?php
                $data = Song::get_recently_played();
		Song::build_cache(array_keys($data));
                require_once Config::get('prefix') . '/templates/show_recently_played.inc.php';
        ?>
</div>
<!-- Shoutbox Objects, if shoutbox is enabled -->
<?php if (Config::get('sociable')) { ?>
<div id="shout_objects">
	<?php
		$shouts = shoutBox::get_top('5');
		if (count($shouts)) {
			//require_once Config::get('prefix') . '/templates/show_shoutbox.inc.php';
		}
	?>
</div>
<?php } ?>
