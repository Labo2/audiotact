<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Browse tags
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

/**
 * Browse Page
 * This page shows the browse menu, which allows you to browse by many different
 * fields including artist, album, and catalog.
 *
 * This page also handles the actual browse action
 */

/* Base Require */
require_once 'lib/init.php';
session_start(); 

/* Ordre alphabétique */
$browse = new Browse();
$browse->set_type('tag');
$browse->set_sort('count','ASC');

$browse->set_simple_browse(false);
$browse->save_objects(Tag::get_random_tags(40,array()));
$object_ids = $browse->get_saved();
$keys = array_keys($object_ids);
Tag::build_cache($keys);

$browse2 = new Browse();
$browse2->set_type('album_tag');
$browse2->set_offset('6');
$browse2->store();

$browse3 = new Browse();
$browse3->set_type('tag');
$browse3->store();
		
require_once Config::get('prefix') . '/templates/show_tagcloud.inc.php';
require_once Config::get('prefix') . '/templates/browse_content_tag.inc.php';
?>

<div id="footer_filter_catalog">
	<div id="sub_tab_filter">
		<?php
		$browse_id = $browse3->id ;
		echo Ajax::text('?page=tag&action=sort_top&browse_id=' . $browse3->id ,('Les + Populaires'),'tag_sort_top_'.$browse3->id); 
		echo Ajax::button('?page=tag&action=random_filter&browse_id=' . $browse3->id,'random',_('Random'),'tag_sort_random_'.$browse3->id,'','tag_random'); ?>
	</div>		
	<div id="global_alphabet">
		<?php
		$ident = $browse3->id;
		for($i='a'; $i<='z'; $i++)
		{
			if($i=='aa'){break;}
			echo Ajax::text('?page=tag&action=add_filter_alphabet&browse_id=' . $browse3->id . '&key=starts_with&multi_alpha_filter='.$i,_($i),'tag_sort_name_'.$i.$ident); 
		} 
		?>
	</div>
</div>




