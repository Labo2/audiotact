<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Denied
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ampache -- Debug Page</title>
<link rel="stylesheet" href="<?php echo Config::get('web_path'); ?>/templates/install.css" type="text/css" media="screen" />
</head>
<body bgcolor="#f0f0f0">
<div id="header">
<h1>Ampache :: <?php echo _('Access Denied'); ?></h1>
<p>This Event has been logged</p>
</div>
<p class="error">
<?php if (!Config::get('demo_mode')) { ?>
<?php
echo _("You've been redirected to this page because you do not have access to this function.");
echo _("If you believe this is an error please contact an Ampache administrator.");
echo _("This event has been logged");
?>
<?php } else { ?>
<?php
echo _("You've been redirected to this page because you've attempted to access a function that is disabled in the demo.");
echo _("Functions are disabled in the demo because previous users of the demo have used the functionality to post inappropriate materials");
?>
<?php } ?>
</p>
<div id="bottom">
<p><strong>Ampache</strong><br />
Pour l'Amour de la Musique.</p>
</div>
</body>
</html>
