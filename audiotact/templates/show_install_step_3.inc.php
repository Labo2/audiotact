<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Install Config
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

$prefix = realpath(dirname(__FILE__). "/../");?>

<div id="main_tabs_content">
	<div id="box_wrapper">
		<div class="col_1_4">
			<p class="valid_step">La base de données a bien été créée.</p>
			<h1>Configuration et compte administrateur</h1>
			<p>Cette étape prend les valeurs de configuration de base et génère le fichier de configuration.</p>
			<?php Error::display('general'); ?>
		</div><!--notify-->
		
			<?php Error::display('config'); ?>
			<form method="post" action="<?php echo WEB_PATH . "?action=create_config"; ?>" enctype="multipart/form-data" >
				<table class="col_1_3">
				<tr><th colspan="2"><h2>Générer le fichier de configuration</h2></th></tr>
				<tr>
					<td class="align_left">Nom de la base de données</td>
					<td><input type="text" name="local_db" value="<?php echo scrub_out($_REQUEST['local_db']); ?>" />
					
					</td>
				</tr>
				<tr>
					<td class="align_left">Nom d'utilisateur MySQL</td>
					<td><input type="text" name="local_username" value="<?php echo scrub_out($_REQUEST['local_username']); ?>" /></td>
				</tr>
				<tr>
					<td class="align_left">Mot de passe MySQL</td>
					<td><input type="password" name="local_pass" value="" /></td>
				</tr>
				<tr>
					<td class="align_left">Hôte Mysql</td>
					<td><input type="text" name="local_host" value="<?php echo scrub_out($_REQUEST['local_host']); ?>" /></td>
				</tr>
				</table><!--col_1_3-->
		
				<table class="col_3_3">
				<!-- admin account -->
				<tr><th colspan="2"><h2>Création du compte d'administration</h2></th></tr>
					<tr>
						<td class="align_left"><?php echo _('Username'); ?></td>
						<td><input type="text" name="admin_username" value="admin" /></td>
					</tr>
					<tr>
						<td class="align_left"><?php echo _('Password'); ?></td>
						<td><input type="password" name="admin_pass" value="" /></td>
					</tr>
					<tr>
						<td class="align_left"><?php echo _('Confirm Password'); ?></td>
						<td><input type="password" name="admin_pass2" value="" /></td>
					</tr>
					<!-- admin-->
					<tr>
						<td>&nbsp;</td>
						<td><span class="valid">Écrire la configuration et créer de le compte d'administration de la borne</span>
							<input type="hidden" name="web_path" value="<?php echo $web_path; ?>" />
							<input type="submit" class="next_step" value="<?php echo _('Write Config'); ?>" />
							<input type="hidden" name="htmllang" value="<?php echo $htmllang; ?>" />
							<input type="hidden" name="charset" value="<?php echo $charset; ?>" />
						</td>
					</tr>
					</table>
				</form>		
	</div><!--box_wrapper-->
</div><!--main_tabs_content-->

