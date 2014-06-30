<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Install Account
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
require $prefix . '/templates/install_header.inc.php';
?>
	<div class="content">
	<?php echo _('Step 1 - Create the Ampache database'); ?><br />
	<?php echo _('Step 2 - Create ampache.cfg.php'); ?><br />
	<strong><?php echo _('Step 3 - Set up the initial account'); ?></strong><br />
	<dl>
	<dd><?php echo _('This step creates your initial Ampache admin account. Once your admin account has been created you will be redirected to the login page.'); ?></dd>
	</dl>
	<?php Error::display('general'); ?>
	<br />
	<span class="header2"><?php echo _('Create Admin Account'); ?></span>
	<form method="post" action="<?php echo WEB_PATH . "?action=create_account&amp;htmllang=$htmllang&amp;charset=$charset"; ?>" enctype="multipart/form-data" >
<table>
<tr>
	<td class="align"><?php echo _('Username'); ?></td>
	<td><input type="text" name="local_username" value="admin" /></td>
</tr>
<tr>
	<td class="align"><?php echo _('Password'); ?></td>
	<td><input type="password" name="local_pass" value="" /></td>
</tr>
<tr>
	<td class="align"><?php echo _('Confirm Password'); ?></td>
	<td><input type="password" name="local_pass2" value="" /></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" value="<?php echo _('Create Account'); ?>" /></td>
</tr>
	</table>
	</form>
	</div>
	<div id="bottom">
    	<p><strong>Ampache Installation.</strong><br />
    	For the love of Music.</p>
   </div>

</div>
</body>
</html>

