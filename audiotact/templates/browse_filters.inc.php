<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Browse Filters
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
$ajax_info = Config::get('ajax_url'); $web_path = Config::get('web_path');
?>
<?php $allowed_filters = Browse::get_allowed_filters($browse->get_type()); ?>
<li><h4><?php echo _('Filters'); ?></h4>
<div class="sb3">
<?php if (in_array('starts_with',$allowed_filters)) { ?>
	<form id="multi_alpha_filter_form" method="post" action="javascript:void(0);">
		<label id="multi_alpha_filterLabel" for="multi_alpha_filter"><?php echo _('Starts With'); ?></label>
		<input type="text" id="multi_alpha_filter" name="multi_alpha_filter" value="<?php $browse->set_catalog($_SESSION['catalog']); echo scrub_out($browse->get_filter('starts_with'));?>" onKeyUp="delayRun(this, '400', 'ajaxState', '<?php echo $ajax_info; ?>?page=browse&action=browse&browse_id=<?php echo $browse->id; ?>&key=starts_with', 'multi_alpha_filter');">
</form>
<?php } // end if alpha_match ?>
<?php if (in_array('minimum_count',$allowed_filters)) { ?>
	<input id="mincountCB" type="checkbox" value="1" />
	<label id="mincountLabel" for="mincountCB"><?php echo _('Minimum Count'); ?></label><br />
	<?php echo Ajax::observe('mincountCB', 'click', Ajax::action('?page=browse&action=browse&browse_id=' . $browse->id . '&key=min_count&value=1', '')); ?>
<?php } ?>
<?php if (in_array('rated',$allowed_filters)) { ?>
	<input id="ratedCB" type="checkbox" value="1" />
	<label id="ratedLabel" for="ratedCB"><?php echo _('Rated'); ?></label><br />
	<?php echo Ajax::observe('ratedCB', 'click', Ajax::action('?page=browse&action=browse&browse_id=' . $browse->id . '&key=rated&value=1', '')); ?>
<?php } ?>
<?php if (in_array('unplayed',$allowed_filters)) { ?>
	<input id="unplayedCB" type="checkbox" <?php echo $string = $browse->get_filter('unplayed') ? 'checked="checked"' : ''; ?>/>
	<label id="unplayedLabel" for="unplayedCB"><?php echo _('Unplayed'); ?></label><br />
<?php } ?>
<?php if (in_array('show_art',$allowed_filters)) { ?>
	<input id="show_artCB" type="checkbox" <?php echo $string = $browse->get_filter('show_art') ? 'checked="checked"' : ''; ?>/>
	<label id="show_artLabel" for="show_artCB"><?php echo _('Show Art'); ?></label><br />
	<?php echo Ajax::observe('show_artCB','click',Ajax::action('?page=browse&action=show_art&browse_id=' . $browse->id, '')); ?>
<?php } // if show_art ?>
<?php if (in_array('playlist_type',$allowed_filters)) { ?>
	<input id="show_allplCB" type="checkbox" <?php echo $string = $browse->get_filter('playlist_type') ? 'checked="checked"' : ''; ?>/>
	<label id="show_allplLabel" for="showallplCB"><?php echo _('All Playlists'); ?></label><br />
	<?php echo Ajax::observe('show_allplCB','click',Ajax::action('?page=browse&action=browse&browse_id=' . $browse->id . '&key=playlist_type&value=1','')); ?>
<?php } // if playlist_type ?>
<?php if (in_array('object_type',$allowed_filters)) { ?>
	<?php $string = 'otype_' . $browse->get_filter('object_type'); ${$string} = 'selected="selected"'; ?>
	<input id="typeSongRadio" type="radio" name="object_type" value="1" <?php echo $otype_song; ?>/>
	<label id="typeSongLabel" for="typeSongRadio"><?php echo _('Song Title'); ?></label><br />
	<?php echo Ajax::observe('typeSongRadio','click',Ajax::action('?page=tag&action=browse_type&browse_id=' . $browse->id . '&type=song','')); ?>
	<input id="typeAlbumRadio" type="radio" name="object_type" value="1" />
	<label id="typeAlbumLabel" for="typeAlbumRadio"><?php echo _('Albums'); ?></label><br />
	<?php echo Ajax::observe('typeAlbumRadio','click',Ajax::action('?page=tag&action=browse_type&browse_id=' . $browse->id . '&type=album','')); ?>
	<input id="typeArtistRadio" type="radio" name="object_type" value="1" />
	<label id="typeArtistLabel" for="typeArtistRadio"><?php echo _('Artist'); ?></label><br />
	<?php echo Ajax::observe('typeArtistRadio','click',Ajax::action('?page=tag&action=browse_type&browse_id=' . $browse->id . '&type=artist','')); ?>
<?php } ?>

<?php if(in_array('catalog',$allowed_filters)) { ?>
<form method="post" id="catalog_choice" action="javascript.void(0);">
	<label id="catalogLabel" for="catalog_select"><?php echo _('Catalog'); ?></label><br />
	<select id="catalog_select" name="catalog_key">
		<option value="0">All</option>
		<?php
			$sql = 'SELECT `id`,`name` FROM `catalog`';
			$db_results = Dba::read($sql);
			while( $data = Dba::fetch_assoc($db_results) ) {
				$results[] = $data;
			}
		
			foreach( $results as $entries ) {
				echo '<option value="' . $entries['id'];
				if( $_SESSION['catalog'] == $entries['id'] ) {
					echo ' selected="selected" ';
				}
				echo '">' . $entries['name'] . '</options>';
			}
		?>
				
	</select>
<?php echo Ajax::observe('catalog_select','click',Ajax::action('?page=browse&action=browse&browse_id=' . $browse->id,'catalog_select','catalog_choice'),'1'); ?>
</form>
<?php } ?>
</div>
</li>
