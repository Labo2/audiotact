<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Install
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
		<div class="notify">	
			<h1>Création de la base de données</h1>
			<p>Cette étape permet de créer et de remplir la base de données. Vous avez besoin ici de vos identifiants de connexion à votre compte MySQL.</p>
			<?php Error::display('general'); ?>
		</div>
		
		<div class="content_form">
		<h2>Création de la base de données</h2>
			<form class="bdd_form" method="post" action="<?php echo WEB_PATH . "?action=create_db&amp;htmllang=$htmllang&amp;charset=$charset"; ?>" enctype="multipart/form-data" >
				<table>
					<tr>
						<td class="align">Nom de la base de données à créer</td>
						<td><input type="text" name="local_db" value="audiotact" /></td>
					</tr>
					<tr>
						<td class="align">Nom d'utilisateur de l'administrateur MySQL</td>
						<td><input type="text" name="local_username" value="root" /></td>
					</tr>
					<tr>
						<td class="align">Mot de passe de l'administrateur MySQL</td>
						<td><input type="password" name="local_pass" /></td>
					</tr>
					<tr>
						<td class="align">Hôte Mysql</td>
						<td><input type="text" name="local_host" value="localhost" /></td>
					</tr>
					<tr>
						<td></td>
						<td><span class="valid">Créer et remplir la base de données</span><input type="submit" class="next_step" value="" /></td>
					</tr>
				</table>
			</form>
		</div><!--content_form-->
	</div><!--box_wrapper-->
</div><!--main_tabs_content-->
