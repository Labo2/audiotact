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
 * Audiotact is an Ampache-based project developped by Oudeis (www.oudeis.fr) with the support of the Labo2 (www.bibliotheque.nimes.fr)

 */

?>
<div id="logo-home"><img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/logo_home.png" /></div>
<ul id="main-menu">

	<!-- ACCES ADMIN -->
	<?php if (Access::check('interface','100')) { ?>
	<li class="admin">
		<a href="<?php echo $web_path; ?>/catalog_browse.php">
			<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/0_ecouter.png" title="ecouter" alt="ecouter" />
		</a>
	</li>
	<li class="admin">
		<a href="<?php echo $web_path; ?>/submit_music.php">
			<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/1_contribuer.png" title="contribuer" alt="contribuer" />
		</a>
	</li>
	<li class="admin">
		<a href="<?php echo $web_path; ?>/audiotact_info.php">
			<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/2_informer.png" title="informer" alt="informer" />
		</a>
	</li>
	<li class="admin">
		<a href="<?php echo $web_path; ?>/admin/admin_moderation.php">
			<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/3_moderer.png" title="moderer" alt="moderer" />
		</a>
	</li>
	<li class="admin">
		<a href="<?php echo $web_path; ?>/admin/admin_parametre.php">
			<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/4_parametrer.png" title="parametrer" alt="parametrer" />
		</a>
	</li>
	<?php } else { ?>
	<li>
		<a href="<?php echo $web_path; ?>/catalog_browse.php">
			<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/ecouter.png" title="ecouter" alt="ecouter" />
		</a>
	</li>
	<li>
		<a href="<?php echo $web_path; ?>/submit_music.php">
			<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/contribuer.png" title="contribuer" alt="contribuer" />
		</a>
	</li>
	<li>
		<a href="<?php echo $web_path; ?>/audiotact_info.php">
			<img src="<?php echo $web_path; ?><?php echo Config::get('theme_path'); ?>/images/informer.png" title="informer" alt="informer" />
		</a>
	</li>	
	<?php } ?>	
</ul>

<div id="favorits_home">
	<div class="cartouche"><h1>La sélection de <?php echo Config::get('site_title'); ?></h1></div>
	<?php
		$selected_object = Favorits::get_selected_object();
			foreach ($selected_object as $key) {
				$selected_object_type = $key;
				
			}
			$selected = Favorits::get_selected($key);
			if (count($selected) AND is_array($selected)) {
				require_once Config::get('prefix') . '/templates/show_favorits.inc.php';
			}
	?>
</div>

