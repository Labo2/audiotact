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
 * Audiotact is an Ampache-based project developped by Oudeis (www.oudeis.fr) with the support of Labo2 (www.bibliotheque.nimes.fr)
 */
require_once '../lib/init.php'; ?>

<div id="infobox_objects">
<?php $id = $_REQUEST['id']; $infoboxes = audiotact_info::get_to_update_box($id);
foreach ($infoboxes as $box_id) {
	$box = new audiotact_info($box_id); ?>
	<form class="infobox_form" method="post" enctype="multipart/form-data" action="<?php echo Config::get('web_path'); ?>/admin/update_admin.php?action=update_infobox">
		<textarea id="update_info" rows="5" cols="70" name="content"><?php echo $box->content; ?></textarea>	
		<?php echo Core::form_register('update_infobox'); ?>
		<input class="id" type="hidden" name="id" value="<?php echo $id; ?>" />
		<input class="ok_submit" type="submit" value="<?php echo _('Ok'); ?>" />
	</form>
<?php } ?>
</div>

<script type="text/javascript">
jQuery.noConflict();	
jQuery(document).ready(function(){
	jQuery('textarea#update_info').keyboard({
		layout       : 'french-azerty-2',
  		customLayout : { default: ['{cancel}'] },
  		usePreview:false,
  		position : {
 			of : jQuery("#lightbox_catalog"),
  			my : 'center bottom',
 			at : 'center bottom',
 			at2: 'center top+50' 
		}
  	});
  	
});	
</script>
