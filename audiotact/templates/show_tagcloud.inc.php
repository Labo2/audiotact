<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Tagcloud
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

Ajax::start_container('tag_filter'); 
	$max = count($object_ids);
	foreach ($object_ids as $data) {
		
		$tag = new Tag($data['id']); $tag->format();	
		$fontsize = rand(12,42).'px'; 
		$prop = array('baseline','top','middle','bottom','super','sub','text-top','text-bottom');
		$vertical_align = $prop[rand(0,7)];	
		$margin_right = rand(5,50).'px'; ?>		
		<span id="click_<?php echo intval($tag->id); ?>" class="<?php echo $tag->f_class; ?>" style="font-size:<?php echo $fontsize; ?>;vertical-align:<?php echo $vertical_align; ?>;margin-right:<?php echo $margin_right; ?>;"><?php echo $tag->name; ?></span>
		<?php echo Ajax::observe('click_' . intval($tag->id),'click',Ajax::action('?page=tag&action=add_filter&browse_id=' . $browse2->id . '&tag_id=' . intval($tag->id),'')); ?>
	
	<?php } /*end foreach*/ 
	if (!count($object_ids)) { ?> <span class="fatalerror"><?php echo _('Aucun tag'); ?></span><?php } 
Ajax::end_container(); ?>
