<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Contrib Step - File Upload
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
$web_path = Config::get('web_path'); ?>

<script type="text/javascript">
jQuery.noConflict();
jQuery(function(){	
	jQuery('.fileUpload').fileUploader();
	jQuery("#main_tabs").tabs("option", "disabled", [ 1,2,3 ] );
	listUploadScroll = new iScroll('box_list', { hScrollbar: false, vScrollbar: false});
	if (jQuery('.list_upload').height() > 260) {jQuery('.nav_scroll_info').show();} else {jQuery('.nav_scroll_info').hide();}
});
</script>

<div id="ui-tabs-1">
	<div id="main_tabs_content">
		<div id="submit_contrib" class="step-1">
		<div>	
			<div id="submit_info">
				<h1>Vous souhaitez proposer un album à l’écoute et au téléchargement ? </h1>
				<p>Cette borne vous est dédiée, n’hésitez donc pas à vous l’approprier et contribuer au catalogue.<br />				Pour cela, téléchargez votre album sur la borne depuis le formulaire suivant et informez les différents champs demandés.</p>
				<h2>Préconisations techniques</h2>
				<p>Il est recommandé de rassembler les différentes pistes de votre album sous la forme d’une archive au format zip ou rar. La qualité de votre encodage est primordiale. <br />				Privilégiez un bitrate minimal de 192 kbps et enregistrez vos pistes au format mp3.<br /><br />				Enfin, afin de documenter au mieux votre fiche Artiste et Albums, pensez à intégrer des visuels adaptés ainsi qu’une licence de diffusion parmi les choix proposés.<br />				À bientôt !</p>
			</div><!--submit_info-->
			
			<div id="submit_form_upload">
				<form method="post" enctype="multipart/form-data" action="<?php echo Config::get('web_path'); ?>/submit_music_write_tag.php?action=upload_music_file" class="<?php echo $data_id ?>">
					<input type="file" name="userfile" class="fileUpload" multiple>
					<span id="valid_upload">Valider l'envoi</span>
				  	<input  id="px-submit" class="button" type="submit" value="<?php echo _('Submit Catalog'); ?>" />
				</form>
			</div><!--submit_form_upload-->
			<div id="up_contrib" class="nav_scroll_info" onclick="listUploadScroll.scrollTo(0, -40, 200, true);return false">&larr; prev</div>
			<div id="down_contrib" class="nav_scroll_info" onclick="listUploadScroll.scrollTo(0, 40, 200, true);return false">next &rarr;</div>
		</div>
		</div><!--submit_contrib-->
	</div><!-- main_tabs_content-->
</div><!--ui-tabs-1-->
