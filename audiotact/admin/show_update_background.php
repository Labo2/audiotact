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
require_once '../lib/init.php';
$web_path = Config::get('web_path');
session_start();
?>
<script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/lib/javascript/FileAPI.js"></script>
<script type="text/javascript">
jQuery.noConflict();	
jQuery(function(){ 
	//jQuery('#scrollbar_wrapper').mCustomScrollbar({ scrollButtons:{enable:true}, advanced:{ updateOnContentResize: true}});
	contribScroll = new iScroll('scrollbar_wrapper', { hScrollbar: false, vScrollbar: false});	
	var selecting = "<?php echo $web_path.Config::get('theme_path').'/images/icons/browse_button.png'; ?>";
	jQuery("input.file_1").filestyle({ image: selecting, imageheight : 27, imagewidth : 54, width : 0 });

	jQuery(".bg_trash").click(function(){
		alert ("Supprimer cette image ?");
	});
});	
</script>
<script type="text/javascript">
if (typeof FileReader == "undefined") alert ("Ce navigateur ne supporte pas cette fonctionnalité.");
    FileAPI = new FileAPI(
        document.getElementById("bg_img_list"),
        document.getElementById("bg_upload"),
        document.forms["form_img_upload"].action 
    );
    FileAPI.init();
    var reset = document.getElementById("reset");
    reset.onclick = FileAPI.clearList;
    var upload = document.getElementById("upload");
    upload.onclick = FileAPI.uploadQueue;
</script>



<div id="scrollbar_wrapper">
	<div id="lightbox_wrapper">
		
		<div id="left_info_bg">
			<h3>Arrières-plans actuels</h3>
			<?php $sql = "SELECT * FROM `background_image` ;"; $result = mysql_query($sql) or exit(mysql_error());
			while ($data = mysql_fetch_assoc($result)) { $img = $data['img_path']; $id = $data['id']; ?>
				<div id="bg_item_<?php echo $id; ?>" class="bg_item">
					<img src="<?php echo $web_path.'/images_background/'.$img ;?>" width="160px" style="border:1px solid"/>
					<?php echo Ajax::button('?action=delete_background&id=' .$id,'remove_picto','delete','delete_bg_'.$id,'','bg_trash'); ?>
				</div>
			<?php } ?>
		</div><!-- left_info_bg -->

		<div id="right_info_bg">
			<h3>Ajouter des images</h3>
			<p class="notice_cat">Veuillez sélectionner un visuel (jpeg, png, gif, tiff)<br />Dimensions : 1600×900 pixels</p>
			<div id="images_upload">
				<form name="form_img_upload" class="form_img_upload" action="<?php echo Config::get('web_path'); ?>/admin/update_admin.php?action=upload_bg" method="post" enctype="multipart/form-data"><input type="file" id="bg_upload" name="bg_upload" multiple /></form>  <!--class="file_1"-->
		
		         <div id="files">
					<a id="reset" href="#" title="Effacer">Effacer la liste</a>
		            <ul id="bg_img_list"></ul>
		            <a id="upload" href="#" title="Upload">Uploader</a>
		        </div>
		 	</div><!-- images_upload -->  
		 </div><!-- right_info_bg --> 
	
	</div><!-- lightbox_wrapper -->
</div><!-- scrollbar_wrapper -->

