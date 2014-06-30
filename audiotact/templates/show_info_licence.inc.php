<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * General info - Licence
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

require_once '../lib/init.php';
session_start();
$web_path = Config::get('web_path');?>

<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function() {
	jQuery('.licence').columnize({width:685, height:295 });
	
	if ((jQuery('.licence .column').size()) > 2) {
		infoScroll = new iScroll('box_text_wrap_licence', {
			snap: true,
			momentum: true,
			hScrollbar: false,
			vScrollbar: false
		});
	} else {
		jQuery('.nav_scroll_info').hide();
	}
});
</script>

<div id="main_tabs_content">
	<div id="box_text_wrap_licence" class="scroll_wrapper">
		<div class="box_text_columns licence">
			<div><?php
			$infoboxes = audiotact_info::get_from_box('licences');
			foreach ($infoboxes as $box_id) {
				$box = new audiotact_info($box_id);?>
				<div class="content"><?php echo nl2br($box->content); ?></div>
			<?php } ?>	
			</div>
		</div><!-- box_text_columns -->
	</div><!-- box_text_wrap -->
<div id="prev_info" class="nav_scroll_info" onclick="infoScroll.scrollToPage('prev', 0, 750);return false">&larr; prev</div>
<div id="next_info" class="nav_scroll_info" onclick="infoScroll.scrollToPage('next', 0, 750);return false">next &rarr;</div>

</div><!-- main_tabs_content -->
		
		
		
		
		
