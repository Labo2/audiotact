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


<div id="main_tabs_content">
	<div id="admin_param">
		<div class="admin_param_columns">
			<div class="col_1">
					<!-- RESTART AUDIOTACT  -->
				<center>
					<div class="item_admin">
<!--					<a href="?action=relaunch"><img src="<?php echo $web_path.Config::get('theme_path').'/images/icons/icon_relaunch.png'; ?>"></img></a>
						<h3>Relancer Audiotact</h3>
					</div><!-- item admin -->			
						
					<!-- MAINTENANCE MODE -->
					<div class="item_admin">
<!--					<a href="?action=maintenance"><img src="<?php echo $web_path.Config::get('theme_path').'/images/icons/icon_maintenance.png'; ?>"></img></a>
						<h3>Mode Maintenance</h3>					
					</div><!-- item admin -->
				</center>
				
			</div>
			
			
			<div class="col_2">
					<!--REBOOT AUDIOTACT-->
				<center>
					<div class="item_admin">
						<a href="?action=reboot"><img src="<?php echo $web_path.Config::get('theme_path').'/images/icons/icon_reboot.png'; ?>"></img></a>
						<h3>Redémarrer l'ordinateur</h3>							
					</div>
					<!--HALT AUDIOTACT-->
					<div class="item_admin">
						<a href="?action=halt"><img src="<?php echo $web_path.Config::get('theme_path').'/images/icons/icon_halt.png'; ?>"></img></a>
						<h3>Arrêter l'ordinateur</h3>	
					</div>
				</center>
			</div>
			
	</div><!--admin_param -->
</div><!--main_tabs_content -->
