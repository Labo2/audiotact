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

$prefix = realpath(dirname(__FILE__). "/../");
$web_path = Config::get('web_path');
?>

<div id="main_tabs_content">
	<div id="box_wrapper">
		
		<div class="col_1_4">
			<h1>Configuration et compte administrateur</h1>
			<?php Error::display('general'); ?>
			<table>
				<tr>
			        <td class="align_left">Le fichier ampache.cfg.php a-t-il bien été créé ?</td>
			        <td><?php if (!is_readable($configfile)) { echo debug_result('',false); }
			              else { echo debug_result('',true); }
			        ?>
			        </td>
				</tr>
				<tr>
			        <td class="align_left">Le fichier ampache.cfg.php est-il configuré ?</td>
			        <td><?php $results = @parse_ini_file($configfile);
			        if (!check_config_values($results)) { echo debug_result('',false); }
			        else { echo debug_result('',true);}?>
			        </td>
				</tr>
				<tr><th colspan="2"><p class="valid_step">Votre compte administrateur a été créé.</p></th></tr>
			</table>
		</div><!--col_1_4-->
	
		<div class="col_2_4">
			<h1>Indexation du catalogue</h1>
			<p>Le dossier "Catalogue" est dédié aux fichiers musicaux de votre catalogue.<br />
		Après avoir importé votre catalogue dans ce dossier, vous pouvez dès à présent procéder son indexation au sein de la borne. </p>
			<img class="notice_cat" src="images/notice_cat.png" title="notice" alt="notice"/>	
			
			<div class="launch_index">
				<a class="index_cat index" href="<?php echo Config::get('web_path'); ?>/lightbox_item.php?action=update_catalog&catalogs[]=1">Lancer l'indexation</a>
				<a class="index_cat index_upload" href="<?php echo Config::get('web_path'); ?>/lightbox_item.php?action=update_catalog&catalogs[]=1"><img src="images/upload.png" title="upload" alt="upload"/></a>
			</div>
		</div><!--col_2_4 -->
		
		<div class="col_4_4">
			<h1>L'installation est terminée !</h1>
			<div class="go_admin">
				<a class="index_upload" href="<?php echo Config::get('web_path'); ?>/login.php"><img src="images/go_admin.png" title="upload" alt="upload"/></a><br />
				<a class="index" href="<?php echo Config::get('web_path'); ?>/login.php">Administration</a>
			</div>
			<div class="go_home">
				<a class="index_upload" href="<?php echo Config::get('web_path'); ?>"><img src="images/go_home.png" title="upload" alt="upload"/></a><br /><a class="index" href="<?php echo Config::get('web_path'); ?>">Accueil</a>
			</div>
		</div><!--col_4_4-->
	
	</div><!--box_wrapper-->
</div><!--main_tabs_content-->

