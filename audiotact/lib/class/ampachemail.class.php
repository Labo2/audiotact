<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * AmpacheMail Class
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
 * @authro	Karl Vollmer <vollmer@ampache.org>
 * @copyright	2001 - 2011 Ampache.org
 * @license	http://opensource.org/licenses/gpl-2.0 GPLv2
 * @link	http://www.ampache.org/
 */

/**
 * AmpacheMail Class
 *
 * This class handles the Mail
 *
 * @package	Ampache
 * @copyright	2001 - 2011 Ampache.org
 * @license	http://opensource.org/licenses/gpl-2.0 GPLv2
 * @link	http://www.ampache.org/
 */
class AmpacheMail {

	// The message, recipient and from
	public $message;
	public $subject;
	public $recipient;
	public $recipient_name;
	public $sender;
	public $sender_name;

	/**
	 * Constructor
	 *
	 * This does nothing. Much like goggles.
	 */
	public function __construct() {

		// Eh bien.

	} // Constructor

	/**
	 * set_default_sender
	 *
	 * Does the config magic to figure out the "system" email sender and
	 * sets it as the sender.
	 */
	public function set_default_sender() {
		$user = Config::get('mail_user');
		if (!$user) {
			$user = 'info';
		}

		$domain = Config::get('mail_domain');
		if (!$domain) {
			$domain = 'example.com';
		}
		
		$fromname = Config::get('mail_name');
		if (!$fromname) {
			$fromname = 'Ampache';
		}

		$this->sender = $user . '@' . $domain;
		$this->sender_name = $fromname;
	} // set_default_sender

	/**
 	 * get_users
	 * This returns an array of userids for people who have e-mail
	 * addresses based on the passed filter
	 */
	public static function get_users($filter) {

		switch ($filter) {
			default:
			case 'all':
				$sql = "SELECT * FROM `user` WHERE `email` IS NOT NULL";
			break;
			case 'users':
				$sql = "SELECT * FROM `user` WHERE `access`='25' AND `email` IS NOT NULL";
			break;
			case 'admins':
				$sql = "SELECT * FROM `user` WHERE `access`='100' AND `email` IS NOT NULL";
			break ;
			case 'inactive':
				$inactive = time() - (30*86400);
				$sql = "SELECT * FROM `user` WHERE `last_seen` <= '$inactive' AND `email` IS NOT NULL";
			break;
		} // end filter switch

		$db_results = Dba::read($sql);

		$results = array();

		while ($row = Dba::fetch_assoc($db_results)) {
			$results[] = array('id'=>$row['id'],'fullname'=>$row['fullname'],'email'=>$row['email']);
		}

		return $results;

	} // get_users

	/**
	 * add_statistics
	 * This should be run if we want to add some statistics to this e-mail,
	 * appends to self::$message
	 */
	public function add_statistics($methods) {



	} // add_statistics

	/**
	 * send
	 * This actually sends the mail, how amazing
	 */
	public function send($phpmailer = null) {

		$mailtype = Config::get('mail_type');
		
		if ($phpmailer == null) {
			$mail = new PHPMailer();

			$recipient_name = $this->recipient_name;
			if(function_exists('mb_encode_mimeheader')) {
				$recipient_name = mb_encode_mimeheader($recipient_name);
			}
			$mail->AddAddress($this->recipient, $recipient_name);
		}
		else {
			$mail = $phpmailer;
		}

		$mail->CharSet	= Config::get('site_charset');
		$mail->Encoding	= 'base64';
		$mail->From	= $this->sender;
		$mail->Sender	= $this->sender;
		$mail->FromName	= $this->sender_name;
		$mail->Subject	= $this->subject;

		if(function_exists('mb_eregi_replace')) {
			$this->message = mb_eregi_replace("\r\n", "\n", $this->message);
		}
		$mail->Body	= $this->message;

		$sendmail       = Config::get('sendmail_path');
		$sendmail	= $sendmail ? $sendmail : '/usr/sbin/sendmail';
		$mailhost	= Config::get('mail_host');
		$mailhost	= $mailhost ? $mailhost : 'localhost';
		$mailport	= Config::get('mail_port');
		$mailport	= $mailport ? $mailport : 25;
		$mailauth	= Config::get('mail_auth');
		$mailuser       = Config::get('mail_auth_user');
		$mailuser	= $mailuser ? $mailuser : '';
		$mailpass       = Config::get('mail_auth_pass');
		$mailpass	= $mailpass ? $mailpass : '';

		switch($mailtype) {
			case 'smtp':
				$mail->IsSMTP();
				$mail->Host = $mailhost;
				$mail->Port = $mailport;
				if($mailauth == true) {
					$mail->SMTPAuth = true;
					$mail->Username = $mailuser;
					$mail->Password = $mailpass;
				}
				if ($mailsecure = Config::get('mail_secure_smtp')) {
					$mail->SMTPSecure = ($mailsecure == 'ssl') ? 'ssl' : 'tls';
				}
			break;
			case 'sendmail':
				$mail->IsSendmail();
				$mail->Sendmail = $sendmail;
			break;
			case 'php':
			default:
				$mail->IsMail();
			break;
		}

		$retval = $mail->send();
		if( $retval == true ) {
			return true;
		} else {
			return false;
		}
	} // send

	public function send_to_group($group_name) {
		$mail = new PHPMailer();

		foreach(self::get_users($group_name) as $member) {
			if(function_exists('mb_encode_mimeheader')) {
				$member['fullname'] = mb_encode_mimeheader($member['fullname']);
			}
			$mail->AddBCC($member['email'], $member['fullname']);
		}

		return $this->send($mail);
	}

} // AmpacheMail class
?>
