<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Artist
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
//require_once '../lib/init.php';
session_start();
$web_path = Config::get('web_path');?>

<div id="main_tabs_content">
	<div id="submit_contrib">
		<p class="valid_contrib">Votre contribution a bien été enregistrée et sera validée sous peu. Merci</p>
		<div class="go_home">
			<a href="<?php echo $web_path;?>"><img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/next_step.png" title="Accueil" alt="Accueil" /></a><br />
			<a href="<?php echo $web_path;?>">RETOUR À L'ACCUEIL</a>
		</div>
		<div class="other_contrib">
			<a href="<?php echo $web_path; ?>/submit_music.php"><img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/back.png" title="Autre proposition" alt="Autre proposition" /></a><br />	
			<a href="<?php echo $web_path; ?>/submit_music.php">AUTRE PROPOSITION</a>
		</div>
		<?php /*if ($ok) { echo $ok ;}*/ ?>
	</div><!-- submit_contrib-->
</div><!--main_tabs_content-->


