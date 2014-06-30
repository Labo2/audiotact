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
if (!Access::check('interface','100')) { access_denied(); exit;} ?>
<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function() {		
	jQuery('input[type="text"], input[type="password"]').keyboard({
		layout       : 'french-azerty-2',customLayout : { default: ['{cancel}'] },usePreview:true,
  		position : {
 			of : jQuery('#content'),
  			my : 'center center',
 			at : 'center center',
 			at2: 'center top' 
		}
  	});
	jQuery('#admin_param .ui-widget-content').each(function(){jQuery(this).removeClass('ui-widget-content');})
	
	var selecting = "<?php echo $web_path.Config::get('theme_path').'/images/icons/admin_browse_button.png'; ?>";
	jQuery("input.file_cat").filestyle({ image: selecting,imageheight : 27,imagewidth : 54, width : 315});
});
</script>



<div id="main_tabs_content">
	<div id="admin_param">
		<div class="admin_param_columns">
			<div class="col_1">
			<!-- LOGIN  -->
			<?php $user_id = $GLOBALS['user']->id; $client	= new User($user_id);?>
			<div class="item_admin">
				<form name="update_user_login" class="update_login" enctype="multipart/form-data" method="post" action="<?php echo Config::get('web_path') . "/admin/update_admin.php?action=update_login"; ?>">
					
					<h3>Modifier l'identifiant de connexion</h3> 
						<div class="left">
						<input type="text" name="username" size="30" value="<?php echo scrub_out($client->username); ?>" placeholder="<?php echo scrub_out($client->username); ?>" class="param"/>

							
							<div class="saved" style="display:none">Veuillez vous reconnecter<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/icon_saved.png" /></div>
							<input type="hidden" name="access" value="100" />
						</div>
			
						<div class="right">
							<div class="formValidation">
								<!--<input type="hidden" name="action" value="update_user" />-->
								<input type="submit" value="Ok" class="admin_ok_submit"/>
								<?php echo Core::form_register('edit_user_login'); ?>
								<input type="hidden" name="user_id" value="<?php echo $client->id; ?>" />
							</div>
						</div>
				</form>
			</div><!-- item admin -->
			
			
			
			<!-- MOT DE PASSE -->
			<?php $user_id = $GLOBALS['user']->id; $client	= new User($user_id);?>
			<div class="item_admin">
				<form name="update_user" class="update_mdp" enctype="multipart/form-data" method="post" action="<?php echo Config::get('web_path') . "/admin/update_admin.php?action=update_mdp"; ?>">
					<h3>Changement du mot de passe général</h3> 
						<input type="password" name="password_1" size="30" value="" placeholder="Entrez votre nouveau de mot de passe" class="param"/>
						<?php Error::display('password'); ?>
						<div class="left">
							<input type="password" name="password_2" size="30" value="" placeholder="Confirmez-le" class="param"/>
							<img class="saved" style="display:none" src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/icon_saved.png" />
							<input type="hidden" name="username" size="30" maxlength="128" value="<?php echo scrub_out($client->username); ?>" />
							<input type="hidden" name="fullname" size="30" value="<?php echo scrub_out($client->fullname); ?>" />	
							<input type="hidden" name="email" size="30" value="<?php echo scrub_out($client->email); ?>" />
							<input type="hidden" name="access" value="100" />
						</div>
			
						<div class="right">
							<div class="formValidation">
								<!--<input type="hidden" name="action" value="update_user" />-->
								<input type="submit" value="Ok" class="admin_ok_submit"/>
								<?php echo Core::form_register('edit_user'); ?>
								<input type="hidden" name="user_id" value="<?php echo $client->id; ?>" />
							</div>
						</div>
				</form>
			</div><!-- item admin -->


			<!-- NOM DE LA BORNE -->
			<?php $fullname= _('Server'); $preferences = $GLOBALS['user']->get_preferences(-1,'system'); $is_admin = true; ?>
			<div class="item_admin">
				<form method="post" class="update_web_title" name="preferences" action="<?php echo Config::get('web_path'); ?>/admin/update_admin.php?action=update_web_title" enctype="multipart/form-data">
				<h3>Changement du nom associé à cette borne</h3>
					<div class="left">
						<input type="text" value="<?php echo $preferences['system']['prefs']['site_title']['value']; ?>" name="site_title" class="param">
						<img class="saved" style="display:none" src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/icons/icon_saved.png" />
						<input type="hidden" name="check_<?php echo $preferences['system']['prefs']['site_title']['value']; ?>" value="1" />
					</div>
			
					<div class="right">
						<div class="formValidation">
							<input class="button admin_ok_submit" type="submit" value="Ok"  />
							<?php echo Core::form_register('update_preference'); ?>
							<input type="hidden" name="tab" value="system" />
							<input type="hidden" name="method" value="admin" />
							<?php if (Access::check('interface','100')) { ?>
							<input type="hidden" name="user_id" value="" />
							<?php } ?>
						</div>
					</div>
				</form>
			</div><!-- item admin -->
			</div>
			
			
			<div class="col_2">
			<!--AUDIOTACT INFOS -->
				<div class="item_admin">
					<h3>Modification de l'onglet <i>Esprit du libre ?</i></h3>
					<?php $infoboxes = audiotact_info::get_from_box('esprit_du_libre');
					foreach ($infoboxes as $box_id) { $box = new audiotact_info($box_id); ?>
						<textarea disabled id="param_box_info" class="infobox_<?php echo $box_id; ?>"><?php echo $box->content; ?></textarea>
					<?php } ?>	
					<a class="admin_infobox" title="Esprit du libre ?" href="<?php echo Config::get('web_path'); ?>/admin/show_update_audiotact_info.php?id=<?php echo $box_id;?>"><?php echo get_user_icon('edit_button','',('Éditer')); ?></a>
				</div>
					
				<div class="item_admin">
					<h3>Modification de l'onglet <i>Les licences</i></h3>
					<?php $infoboxes = audiotact_info::get_from_box('licences');
				  	foreach ($infoboxes as $box_id) { $box = new audiotact_info($box_id);?>
						<textarea disabled id="param_box_info" class="infobox_<?php echo $box_id; ?>"><?php echo $box->content; ?></textarea>
					<?php } ?>	
					<a class="admin_infobox" title="Les licences" href="<?php echo Config::get('web_path'); ?>/admin/show_update_audiotact_info.php?id=<?php echo $box_id;?>"><?php echo get_user_icon('edit_button','',('Éditer')); ?></a>
				</div>
				
				<div class="item_admin">
					<h3>Modification de l'onglet <i>Audiotact en détail</i></h3>
					<?php $infoboxes = audiotact_info::get_from_box('audiotact'); 
					foreach ($infoboxes as $box_id) { $box = new audiotact_info($box_id);?>
						<textarea disabled id="param_box_info" class="infobox_<?php echo $box_id; ?>"><?php echo $box->content; ?></textarea>
					<?php } ?>
					<a class="admin_infobox" title="Audiotact en détail" href="<?php echo Config::get('web_path'); ?>/admin/show_update_audiotact_info.php?id=<?php echo $box_id;?>"><?php echo get_user_icon('edit_button','',('Éditer')); ?>
					</a>
				</div>
			</div>
			<div class="col_3">
				<div class="item_admin">	
					<h3>Modification de l'onglet <i>Crédits</i></h3>
					<?php $infoboxes = audiotact_info::get_from_box('credits');
					foreach ($infoboxes as $box_id) { $box = new audiotact_info($box_id);?>
						<textarea disabled id="param_box_info" class="infobox_<?php echo $box_id; ?>"><?php echo $box->content; ?></textarea>
					<?php } ?>		
					<a class="admin_infobox" title="Crédits" href="<?php echo Config::get('web_path'); ?>/admin/show_update_audiotact_info.php?id=<?php echo $box_id;?>"><?php echo get_user_icon('edit_button','',('Éditer')); ?></a>
				</div>
			
			
			<!-- MISE À JOUR - CATALOGUE -->
			<?php $fullname= _('Server'); $preferences = $GLOBALS['user']->get_preferences(-1,'system'); $is_admin = true;?>
			<div class="item_admin">
				<form method="post" class="update_catalog" name="preferences" action="<?php echo Config::get('web_path'); ?>/admin/update_admin.php?action=update_catalog&catalogs[]=1" enctype="multipart/form-data">	
					<h3>Mettre à jour le catalogue</h3>
					<p class="notice_cat">Veuillez sélectionner l'album que vous souhaitez ajouter au catalogue. Format de fichier accepté (zip)</p>
					<div class="left">
						<input type="file" name="zip_update_cat" class="file_cat"/>
					</div>
					<div class="right">
						<div class="formValidation">
							<input class="button admin_ok_submit" type="submit" value="Ok"  />
							<?php echo Core::form_register('update_catalog'); ?>
						</div>
					</div>
				</form>
			</div><!-- item admin -->
			
			<!-- BACKGROUNDS -->
			<div class="item_admin">
			<h3>Modifier l'arrière-plan de la borne</h3>
			<?php  $sql = "SELECT * FROM `background_image` ;"; $result = mysql_query($sql) or exit(mysql_error());
			while ($data = mysql_fetch_assoc($result)) { $img = $data['img_path']; ?>
				<img src="<?php echo $web_path.'/images_background/'.$img ;?>" width="40px" height="40px" style="border:1px solid"/>
			<?php } ?>
			<a class="admin_background" title="Modifier l'arrière-plan de la borne" href="<?php echo Config::get('web_path'); ?>/admin/show_update_background.php">
				<?php echo get_user_icon('edit_button','',('Éditer')); ?>
			</a>
			</div><!-- item admin -->

			
			</div>
		</div><!--admin_param_columns -->
	</div><!--admin_param -->
</div><!--main_tabs_content -->
