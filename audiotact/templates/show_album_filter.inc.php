<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Show Tagcloud
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

$web_path = Config::get('web_path');

Ajax::start_container('footer_filter_catalog'); ?>

<div id="sub_tab_filter">
	<?php
	$browse_id = $browse2->id ;
	echo Ajax::text('?page=browse&action=sort_new_item&browse_id=' . $browse_id ,('Nouveautés'),'album_sort_new_'.$browse_id); 
	
	$browse_id = $browse3->id ;
	echo Ajax::text('?page=browse&action=sort_popular_item&browse_id=' . $browse_id ,('Les + Populaires'),'album_sort_popular_'.$browse_id); 
	
	$browse_id = $browse4->id ;
	echo Ajax::text('?page=browse&action=sort_all&browse_id=' . $browse_id ,('Ordre Alphabétique'),'album_sort_all'.$browse_id); 
	?>
</div>

<div id="global_alphabet">
	<?php
	for($i='a'; $i<='z'; $i++)
	{
	   if($i=='aa'){break;}
	   echo Ajax::text('?page=browse&action=browse&browse_id=' . $browse->id . '&key=starts_with&multi_alpha_filter='.$i,_($i),'album_sort_name_'.$i.$idart); 
	}
	?>
</div>

<?php Ajax::end_container(); ?>

