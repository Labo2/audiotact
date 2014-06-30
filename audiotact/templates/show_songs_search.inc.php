<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Songs
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

// First let's setup some vars we're going to use a lot
$web_path = Config::get('web_path');
$ajax_url = Config::get('ajax_url');?>

<h1>TITRES</h1>
<table class="tabledata" cellpadding="0" cellspacing="0">
<?php foreach ($object_ids as $song_id) {
		$song = new Song($song_id);
		$song->format(); ?>
		<tr class="<?php echo flip_class(); ?>" id="song_<?php echo $song->id; ?>">
			<?php require Config::get('prefix') . '/templates/show_songs_search_row.inc.php'; ?>
		</tr>
<?php } ?>
<?php if (!count($object_ids)) { ?><tr class="<?php echo flip_class(); ?>"><td colspan="9"><span class="fatalerror"><?php echo _('Not Enough Data'); ?></span></td></tr><?php } ?>
</table>

