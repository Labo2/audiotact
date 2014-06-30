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
if (!Access::check('interface','100')) {access_denied();exit;}?>

<script type="text/javascript">
jQuery.noConflict();	
jQuery(function(){
	jQuery('.shouttext').each(function(){jQuery(this).autoResize();});
	jQuery('.shout_columns').columnize({ columns: 2 });
	//jQuery('#shout_mod_wrap').mCustomScrollbar({scrollButtons:{ enable:true },advanced:{ updateOnContentResize: true }});
	shoutScroll = new iScroll('shout_mod_wrap', { hScrollbar: false, vScrollbar: false});		
	
	/* Keyboard */
	jQuery('textarea').keyboard({
		layout       : 'french-azerty-2',customLayout : { default: ['{cancel}'] },usePreview:true,
	  	position : {
	 			of : jQuery('#content'),
	  			my : 'center center',
	 			at : 'center center',
	 			at2: 'center top' 
			}
	});	  	
});
</script>


<div id="main_tabs_content">	
	<div id="shout_mod_wrap">
		<div id="scroller">
		<div class="shout_columns">
			<div>
			<?php $shouts = shoutBox::get_sticky_false();			
			foreach ($shouts as $shout_id) {
				$shout = new shoutBox($shout_id); $object = shoutBox::get_object($shout->object_type,$shout->object_id); $object->format();	?>
	
				<div class="shout <?php echo flip_class(); ?>">
					<div class="cel_show_comment">
						<p class="subtitles comment dontend"><img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/puce_subtitles.png" />
							<?php echo $object->f_artist; ?> / <?php echo $object->name;  ?>
						</p>
						<span class="information dontend">Le <?php echo date("d/M",$shout->date); ?>, <?php echo $shout->user; ?> a écrit : </span>		
						
						<form id="update_comment" class="autosubmit" method="POST" action="">
							<textarea class="shouttext" name="shouttext" value="<?php echo scrub_out($shout->text); ?>"><?php echo scrub_out($shout->text); ?></textarea>
							<span class="edit_comment_button" style="display:none">	
								<a id="<?php echo ('edit_comment_'.$shout_id); ?>" href="<?php echo Config::get('web_path') . '/admin/update_admin.php?action=update_text_comment'; ?>">
									<img title="Éditer" alt="Éditer" src="<?php echo ($web_path . Config::get('theme_path') . '/images/icons/icon_edit_button.png');?>">
								</a>
							</span>
							<input id="id" type="hidden" name="shout_id" value="<?php echo $shout_id ?>" />
						</form>
					</div><!-- .cel_show_comment -->
					
					<div class="cel_edit_comment">		
						<?php $state = $shout->sticky;
						if ($state == '0') { $icon = 'empty_checkbox'; } 
						elseif ($state == '2') { $icon = 'accept_checkbox'; } 
						elseif ($state == '3') { $icon = 'refuse_checkbox'; }
						$button_flip_state_id = 'button_flip_state_'.$shout_id; ?>		
						<span id="<?php echo($button_flip_state_id); ?>">
							<?php echo Ajax::button('?action=flip_shout_state&shout_id=' . $shout->id,$icon,ucfirst($icon),'flip_shout_'.$shout->id); ?>
						</span>		
					</div><!-- .cel_edit_comment -->
				</div><!-- .shout -->
				<?php } /* end foreach shout */ ?>				
				<?php if (!count($shouts)) { ?><span class="fatalerror"><?php echo ('Aucun commentaire en attente de modération'); ?></span><?php } ?>
			</div><!-- div -->
		</div><!-- shout_columns -->
		</div>
	</div><!-- shout_mod_wrap -->
<?php if (count($shouts)) { echo Ajax::text('?action=moderate_shout','VALIDER CES COMMENTAIRES ?','moderate_shout','','validate_mod'); } ?>
	<div id="up" class="nav_scroll_info" onclick="shoutScroll.scrollTo(0, -40, 200, true);return false">&larr; prev</div>
	<div id="down" class="nav_scroll_info" onclick="shoutScroll.scrollTo(0, 40, 200, true);return false">next &rarr;</div>
</div><!-- #main_tabs_content-->
