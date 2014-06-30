<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Admin Shout
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
require_once '../lib/init.php';
session_start();
$web_path = Config::get('web_path');
if (!Access::check('interface','100')) { access_denied(); exit;}?>

<script type="text/javascript">
jQuery.noConflict();	
jQuery(function(){
	jQuery('#tag_columns').columnize({ columns: 6 });
	tagScroll = new iScroll('tag_mod_wrap', { hScrollbar: false, vScrollbar: false});
});
</script>

<div id="main_tabs_content">	
	<div id="tag_mod_wrap">
		<div id="scroller">
		<p class="subtitles"><img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/puce_subtitles.png" />Validation des derniers tags rédigés</p>
		<?php
		$browse = new Browse();
		$browse->set_type('tag');
		$browse->set_simple_browse(true);
		$browse->set_sort('count','ASC');
		$browse->set_simple_browse(false);
		$browse->save_objects(Tag::get_tags_mod(array()));
		$object_ids = $browse->get_saved();
		$keys = array_keys($object_ids);
		Tag::build_cache($keys);
		?>

		<div id="tag_columns">
			<div><?php 
			foreach ($object_ids as $data) {
				$tag = new Tag($data['id']); $tag->format(); $tag_id = intval($tag->id); ?>
				<div class="tagmod" style="">
				<?php $tag_flip_state_id = 'tag_flip_state_'.$tag_id; $state = $tag->tag_mod ? 'active_delete' : 'inactive_delete'; ?>
					<span id="<?php echo($tag_flip_state_id); ?>">	
						<a id="<?php echo (intval($tag->id)); ?>" class="<?php echo $state;?> tag" href="<?php echo Config::get('web_path') . '/admin/update_admin.php?action=flip_state_delete&tag_id='.intval($tag->id) ; ?>">
							<?php echo $tag->name ;?>
						</a>
					</span>
				</div>
			<?php } ?>
			<?php if (!count($object_ids)) { ?><span class="fatalerror"><?php echo ('Aucun tag ajouté'); ?></span><?php } ?>
			</div>
		</div><!-- tag_columns -->	
	</div>
	</div><!--tag_mod_wrap-->
	<?php echo Ajax::text('?page=tag&action=moderate_tag','VALIDER CES TAGS ?','moderate_tag','','validate_mod'); ?>
	<div id="up" class="nav_scroll_info" onclick="tagScroll.scrollTo(0, -40, 200, true);return false">&larr; prev</div>
	<div id="down" class="nav_scroll_info" onclick="tagScroll.scrollTo(0, 40, 200, true);return false">next &rarr;</div>
</div><!--main_tabs_content-->
