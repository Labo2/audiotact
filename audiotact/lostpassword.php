<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * LostPassword
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

define('NO_SESSION','1');
require_once 'lib/init.php';


		$result = send_newpassword();
		
		echo ('ok');
		if ($result) {
			Error::add('general', _('Password has been sent'));
		} else {
			Error::add('general', _('Password has not been sent'));
		}

	
function send_newpassword() {
	/* get the Client and set the new password */
	$client = User::get_from_fullname("Administrateur");
		$newpassword = generate_password(6);
		$client->update_password($newpassword);

		$log_date = date('d-m-Y');
   		$message  = sprintf(_("Mot de passe perdu le : %s"), $log_date);
   		$message .= "\n";
   		$message .= sprintf(_("Identifiant de connexion : %s"), $client->username);
		$message .= "\n";
		$message .= sprintf(_("Nouveau mot de passe : %s"), $newpassword);
		$message .= "\n";
		$message .= "----------------------------------------------------";
		$message .= "\n";
   		
   		$path = Config::get('prefix') .'/mdp';
      	if (!file_exists($path)){ mkdir("$path", 0755);}	 
		$h = fopen(Config::get('prefix').'/mdp/mdp.txt', "a");
		fwrite($h, $message);
		fclose($h);
	return false;
}
?>
