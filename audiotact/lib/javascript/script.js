jQuery.noConflict();

jQuery(document).ready(function(){

/*********************************************************************************************************************************************
           															HOME                            
*********************************************************************************************************************************************/

/* CAROUSSEL FAVORITS */
var itemWidth = 273;
var itemLenght = jQuery('#favorits_items_content > div').length;
var wrapperWidth = itemLenght * itemWidth;
jQuery('#favorits_items_content').width(wrapperWidth);	
		Cpt = 0;
		
		jQuery("#favorits_next").click(function() {
			if (Cpt ==(itemLenght-5) ) {} 
			else {
				Cpt++;			
				posWrapper = (itemWidth)* Cpt;
				jQuery("#favorits_items_content").stop().animate({ marginLeft : -posWrapper },750,"easeInOutCubic");
			}
			return false;
		});
		jQuery("#favorits_prev").click(function() {
			if (Cpt > 0) { 
				Cpt--;			
				posWrapper = (itemWidth)* Cpt;
				if ((posWrapper == 0) || (posWrapper > 0) ){
					jQuery("#favorits_items_content").stop().animate({ marginLeft : - (itemWidth * Cpt) },750,"easeInOutCubic");
				}
			}
			return false;
		});


/*********************************************************************************************************************************************
           															CATALOG                            
*********************************************************************************************************************************************/

/* CATALOG TABS */
jQuery( ".tabs-bottom .ui-tabs-nav, .tabs-bottom .ui-tabs-nav > *" ).removeClass( "ui-corner-all ui-corner-top" ).addClass( "ui-corner-bottom" );
var tabs = jQuery( "#tabs" ).tabs({
	create: function( event, ui ) { },
	load: function( event, ui ) {},
	ajaxOptions: { error: function( xhr, status, index, anchor ) { jQuery( anchor.hash ).html("Erreur lors du chargement de la page" );}}
});


/* LIGHTBOX */
jQuery('a.open_lightbox, .cel_name a, .cel_playlist a').live('click', function() {
	var name= jQuery(this).attr("title");
	if (jQuery("#lightbox_catalog").length == 0) { ajaxDialog = jQuery('<div id="lightbox_catalog" style="display:hidden" title="'+(name)+'"></div>').appendTo('body');}    
    ajaxDialog.dialog({
      	autoOpen: false, width:860, height:700, modal:true, resizable: false, draggable: false, 
      	show: { effect: "clip", easing:"swing", duration:750 },
		hide: { effect: "clip"},
		close: function(event, ui) { ajaxDialog.remove();}
	 });
      ajaxDialog.load(this.href, function(){});
      ajaxDialog.dialog("open");
      return false;
});


/* Click - hover item catalog */
jQuery('.cel_show').live('click', function(){
	jQuery(this).next('.cel_hover_action').fadeIn();
	jQuery(this).next('.cel_hover_action').delay(5000).fadeOut();
});

/* active elements */
jQuery("#global_alphabet a").live('click',function(){
	jQuery("#global_alphabet a").removeClass('active');
	jQuery(this).addClass('active');
	});
	
jQuery("#sub_tab_filter a").live('click',function(){
	jQuery("#sub_tab_filter a").removeClass('active');
	jQuery(this).addClass('active');
	});
jQuery("#show_playlist_genre ul li").live('click',function(){
	jQuery("#show_playlist_genre ul li").removeClass('active');
	jQuery(this).addClass('active');
	});

jQuery('.fav_user').live('click', function(){
	jQuery(this).children('img').attr('src','images/icon_favorite_active_song.png');
	
});

jQuery('.fav_user_lightbox').live('click', function(){
	jQuery(this).addClass('active_fav_li');
	jQuery(this).parent('li').next().children('a').children('img').attr('src','images/icon_favorite_active.png');
	
});

jQuery('.fav_user_lightbox_img').live('click', function(){
	jQuery(this).children('img').attr('src','images/icon_favorite_active.png');
	jQuery(this).parent('li').prev().children('a').addClass('active_fav_li');
	
});


jQuery('.play_song').live('click', function(){
	jQuery('.play_song').removeClass('activePlaying');
	jQuery(this).addClass('activePlaying');
	//jQuery(this).children('img').attr('src','images/icon_favorite_active_song.png');
	
});


/* ************************************************************ ARTISTS **********************************************************************/
jQuery('.album_link').live('click', function(){
	var album_id = jQuery(this).attr('id');
	var url = jQuery(this).attr('rel');
	var name= jQuery(this).attr("title");
	
	jQuery("#lightbox_catalog").dialog("close");
	jQuery('#tabs').tabs( 'select', 1);

	var ajaxDialog = jQuery('<div id="lightbox_catalog" style="display:hidden" title="'+(name)+'"></div>').appendTo('body');
   
	ajaxDialog.dialog({
		autoOpen: false, width:860, height:700, modal:true, resizable: false, draggable: false, 
		show: { effect: "clip", easing:"swing", duration:750 },hide: { effect: "clip"},close: function(event, ui) { ajaxDialog.remove();}
	});
	ajaxDialog.load(url, function(){
		/*lightboxScroll = new iScroll('scrollbar_wrapper', { hScrollbar: false, vScrollbar: false, onBeforeScrollStart: function (e) { var target = e.target;
			while (target.nodeType != 1) target = target.parentNode;
			if (target.tagName != 'SELECT' && target.tagName != 'INPUT' && target.tagName != 'TEXTAREA')
				e.preventDefault();
		}
		});*/
	});
	ajaxDialog.dialog("open");
	return false;
});
	

/* ************************************************************ ALBUMS **********************************************************************/


/* ADD TAG */	
jQuery('#add_album_tag').live('submit', function(){
		var result = jQuery(this).find('#tags_1');
		var action = jQuery(this).attr('action');
		var data = jQuery(this).serialize();
		jQuery.post(action, data, function(data) {
		placeholder = "";
		result.attr("placeholder", placeholder);
		result.val('');
		result.next().next().fadeIn();
		result.next().next().delay(3000).fadeOut(500, function(){
			placeholder = "TAGS SUGGÉRÉS POUR CET ALBUM";
			result.attr("placeholder", placeholder);
			});
  		});
		return false;
	});	
/* ADD SHOUT */
jQuery('#add_shout').live('submit', function(){
		var result = jQuery(this).find('.comment');
		var action = jQuery(this).attr('action');
		var data = jQuery(this).serialize();
		jQuery.post(action, data, function(data) {
			placeholder = "";
			result.attr("placeholder", placeholder);
			result.val('');
			result.next().fadeIn();
			result.next().delay(3000).fadeOut(500, function(){
				placeholder = "VOTRE COMMENTAIRE";
				result.attr("placeholder", placeholder);
				});
  		});
		return false;
});	

/* ARTIST LINK */
jQuery('.artist_link').live('click', function(){
	var url = jQuery(this).attr('href');
	var name= jQuery(this).attr("title");
	
	jQuery("#lightbox_catalog").dialog("close");
	jQuery('#tabs').tabs( 'select', 0);
	
	//if (jQuery("#lightbox_catalog").length == 0) {
		var ajaxDialog = jQuery('<div id="lightbox_catalog" style="display:hidden" title="'+(name)+'"></div>').appendTo('body');
	//} 
	   
		ajaxDialog.dialog({
			autoOpen: false, width:860, height:700, modal:true, resizable: false, draggable: false, 
			show: { effect: "clip", easing:"swing", duration:750 },hide: { effect: "clip"},close: function(event, ui) { ajaxDialog.remove();}
		});
		ajaxDialog.load(url, function(){
		/*	lightboxScroll = new iScroll('scrollbar_wrapper', { hScrollbar: false, vScrollbar: false, onBeforeScrollStart: function (e) { var target = e.target;
			while (target.nodeType != 1) target = target.parentNode;
			if (target.tagName != 'SELECT' && target.tagName != 'INPUT' && target.tagName != 'TEXTAREA')
				e.preventDefault();
			}
			});*/
		});
		ajaxDialog.dialog("open");
		return false;
	});
	

	

/* ************************************************************ FAVORITS ********************************************************************/
	
	
	/* AUTOSUBMIT TMP_PLAYLIST TITLE & GENRE */
	jQuery('#edit_tmp_playlist input').live('change', function(){
		var action = jQuery(this).parent('#edit_tmp_playlist').attr('action');
		var data = jQuery(this).parent('#edit_tmp_playlist').serialize();
	    jQuery.post(action, data, function(data) {});
	});
	jQuery('#edit_tmp_playlist select').live('change', function(){
		var action = jQuery(this).parent().parent('#edit_tmp_playlist').attr('action');
		var data = jQuery(this).parent().parent('#edit_tmp_playlist').serialize();
	    jQuery.post(action, data, function(data) {});
	});
	
	/* AUTOSUBMIT SAVED_PLAYLIST - UPADTE TITLE & GENRE */
	jQuery('#edit_saved_playlist input').live('change', function(){
		var action = jQuery(this).parent('#edit_saved_playlist').attr('action');
		var data = jQuery(this).parent('#edit_saved_playlist').serialize();
		var id = jQuery(this).parent('#edit_saved_playlist').children('input[name="id"]').attr('value');

	    jQuery.post(action, data, function(data) {
	    	jQuery('#show_playlist_submit').find('li#'+id).children().remove();
	    	jQuery('#show_playlist_submit').find('li#'+id).append(data);
	    	});
	});
	jQuery('#edit_saved_playlist select').live('change', function(){
		var action = jQuery(this).parent().parent('#edit_saved_playlist').attr('action');
		var data = jQuery(this).parent().parent('#edit_saved_playlist').serialize();
		var id = jQuery(this).parent().parent('#edit_saved_playlist').children('input[name="id"]').attr('value');

	    jQuery.post(action, data, function(data) {
	    	jQuery('#show_playlist_submit').find('li#'+id).children().remove();
	    	jQuery('#show_playlist_submit').find('li#'+id).append(data);
	    	});
	});
	
	
	
	jQuery('#rb_current_playlist_edit').sortable({
		placeholder: 'highlight', // classe à ajouter à l'élément fantome
		cursor: "move",
		update: function() {  // callback quand l'ordre de la liste est changé
		var order = jQuery('#rb_current_playlist').sortable('serialize'); // récupération des données à envoyer
		//jQuery.post(url,order); // appel ajax au fichier ajax.php avec l'ordre des photos
	}
	});	
	
	/* delete artist - album*/
jQuery('a.delete_user_playlist').live('click', function() {
	var action = jQuery(this).attr('href');
	var playlist = jQuery(this).attr('id');
	if (jQuery("#dialog-confirm").length == 0) {
      	 var confirmDialog = jQuery('<div id="dialog-confirm" style="display:hidden">Supprimer ?<br />Cette action est définitive.</div>').appendTo('body');     	
	}
	confirmDialog.dialog({
		resizable: false, height:160, width:300, modal: true, dialogClass: "lightbox_confirm",
		close: function(event, ui) { confirmDialog.remove();},
		buttons: {
			"Annuler": function() { confirmDialog.dialog( "close" ); },
			"Supprimer": function() { 
				jQuery.post(action, function(data) {
					jQuery('#show_playlist_submit').find('li#'+ playlist).remove();
					jQuery('#ui-tabs-5').load(data);
				}); 
				confirmDialog.dialog( "close" );}
		}
	});
	return false;
});

/* ************************************************************ ADMIN UPDATE ********************************************************************/		
/* Update Artist name - web = Update Album name */
jQuery('#update_album_btn').live('click', function() {	
	if (jQuery("#lightbox_search").length == 0) { var ajaxDialog = jQuery('<div id="lightbox_search" style="display:hidden"></div>').appendTo('body');}
    ajaxDialog.dialog({
    	autoOpen: false, 
    	width:1050, height:50, modal:true, position: "center top+80", resizable: false, draggable: false,
    	dialogClass: "search_box",
		show: { effect: "clip"/*, easing:"swing", duration:750 */},
		hide: { duration:150 },
		close: function(event, ui) { ajaxDialog.remove();}
	});

    ajaxDialog.load(this.href);
    ajaxDialog.dialog("open"); 
	return false;

});

jQuery('#update_name').live('submit', function(){
		var action = jQuery(this).attr('action');
		var web = jQuery(this).children('input.web').attr('value');
		var data = jQuery(this).serialize();
		jQuery.post(action, data, function(data) {
			jQuery("#lightbox_search").dialog( "close" );
			if (web) {jQuery('#excerpt_info #info_web').find('h3').text("SITE WEB : "+data);} else {
			jQuery('#excerpt_info').find('h1').text(data);
			}
  		});
		return false;
});	


/* ARTIST Update bio */	
jQuery('#update_bio_btn').live('click', function() {
	var update = jQuery(this).children('img.update');
	var updating = jQuery(this).children('img.updating');
	
	if (update.hasClass('active')) {
		update.removeClass('active').hide();
		updating.addClass('active').show();
		jQuery(this).next().children().css({'background':'white', 'border-radius': '5px 5px 5px 5px','box-shadow': '2px 2px 4px #8B8B8B'});
		jQuery('textarea.biotext').keyboard({
		layout       : 'french-azerty-2',
  		usePreview:false,
  		position : {
 			of : null,
  			my : 'left bottom',
 			at : 'left bottom',
 			at2: 'left-30 top-10' 
		}
  	});	
		return false;
	}
	if (updating.hasClass('active')) {
		updating.removeClass('active').hide();
		update.addClass('active').show();
		jQuery(this).next().children().css({'background':'none','border-radius': 'none','box-shadow': 'none'});

		var action = jQuery(this).next().attr('action');
		var data = jQuery(this).next().serialize();
		jQuery.post(action, data, function(data) {});
		return false;
	}
	

});

/* Delete Shout */
jQuery('a.delete_shout').live('click', function() {
	var action = jQuery(this).attr('href');
	var value = jQuery(this).attr('id');
	var shout = jQuery(this).parent().parent('.shout'); 
	if (jQuery("#dialog-confirm").length == 0) { var confirmDialog = jQuery('<div id="dialog-confirm" style="display:hidden">Supprimer ce commentaire ?<br />Cette action est définitive.</div>').appendTo('body');}

	confirmDialog.dialog({
		resizable: false,height:160,width:300,dialogClass: "lightbox_confirm",modal: true,close: function(event, ui) { confirmDialog.remove();},
		buttons: {
			"Annuler": function() {confirmDialog.dialog( "close" );},
			"Supprimer": function() {
				jQuery.post(action, function(data) {
					shout.remove();
  				});
				confirmDialog.dialog( "close" );
		}}
	});
	return false;
});
/* Delete Tag */
jQuery('a.delete_tag, a.to_delete_tag').live('click', function() {
	var action = jQuery(this).attr('href');
	var tag = jQuery(this);
	var tag_name = jQuery(this).prev();
	
	if (jQuery("#dialog-confirm").length == 0) { var confirmDialog = jQuery('<div id="dialog-confirm" style="display:hidden">Supprimer ce tag sur cet album ?<br />Cette action est définitive.</div>').appendTo('body');}

	confirmDialog.dialog({
		resizable: false,height:160,width:300,dialogClass: "lightbox_confirm",modal: true,close: function(event, ui) { confirmDialog.remove();},
		buttons: {
			"Annuler": function() {confirmDialog.dialog( "close" );},
			"Supprimer": function() {
				jQuery.post(action, function(data) {
					tag.remove();tag_name.remove();
  				});
				confirmDialog.dialog( "close" );
		}}
	});
	return false;
});


/* Delete Artist picture */
jQuery('a.artist_img_delete').live('click', function() {
	var action = jQuery(this).attr('href');
	var value = jQuery(this).attr('id'); 
	var cat_picture = '#artist_'+value+' .cel_show .cel_image a';
	var update_picture = jQuery(this).parent().prev('#artist_picture') ;
	if (jQuery("#dialog-confirm").length == 0) { var confirmDialog = jQuery('<div id="dialog-confirm" style="display:hidden">Supprimer l&rsquo;image ?<br />Cette action est définitive.</div>').appendTo('body');}

	confirmDialog.dialog({
		resizable: false,height:160,width:300,dialogClass: "lightbox_confirm",modal: true,close: function(event, ui) { confirmDialog.remove();},
		buttons: {
			"Annuler": function() {confirmDialog.dialog( "close" );},
			"Supprimer": function() {
				jQuery.post(action, function(data) {
						update_picture.children().remove();
						update_picture.html(data);
						jQuery(cat_picture).children().remove();
						jQuery(cat_picture).html(data);
  				});
				confirmDialog.dialog( "close" );
		}}
	});
	return false;
});

/* Add artist img*/
jQuery('.artist_img_upload').live('change', function() { 
		var value = jQuery(this).children('input[name="id_artist"]').val(); 
		var cat_picture = '#artist_'+value+' .cel_show .cel_image';
		jQuery(".artist_img_upload").ajaxForm({
			target: '#artist_picture,'+ cat_picture
		}).submit();
});

/* Suppression jaquette album */
jQuery('a.album_img_delete').live('click', function() {
	var action = jQuery(this).attr('href');
	var value = jQuery(this).attr('id'); 
	var cat_picture = '#album_'+value+' .cel_show .cel_image a';
	var update_picture = jQuery(this).parent().prev('.album_art') ;
	
	if (jQuery("#dialog-confirm").length == 0) { var confirmDialog = jQuery('<div id="dialog-confirm" style="display:hidden">Supprimer la jaquette d&rsquo;album ?<br />Cette action est définitive.</div>').appendTo('body');     	}	
	confirmDialog.dialog({
		resizable: false,height:160,width:300,modal: true,dialogClass: "lightbox_confirm",close: function(event, ui) { confirmDialog.remove();},
		buttons: {
			"Annuler": function() {
				confirmDialog.dialog( "close" );
			},
			"Supprimer": function() {
				jQuery.post(action, function(data) {
						update_picture.children().remove();
						update_picture.html(data);
						jQuery(cat_picture).children().remove();
						jQuery(cat_picture).html(data);
  				});
				jQuery( this ).dialog( "close" );
			}
		}
	});
	return false;
});

/* Ajouter - Chercher jaquette d'album*/
jQuery('a.album_img_add').live('click', function() {

	var action = jQuery(this).attr('href');
	var value = jQuery(this).attr('id'); 
	var cat_picture = '#album_'+value+' .cel_show .cel_image a';
	var update_picture = jQuery(this).parent().prev('.album_art') ;
	
	if (jQuery("#lightbox_find_art").length == 0) {var ajaxDialog = jQuery('<div id="lightbox_find_art" style="display:hidden" title="Ajouter une jaquette"><div id="lightbox_wrapper"></div></div>').appendTo('body');}
	ajaxDialog.dialog({ 
      		autoOpen: false, width:855, height:700, modal:true, resizable: false, draggable: false, 
      		show: { effect: "clip", easing:"swing", duration:750  },
			hide: { effect: "clip"},
			close: function(event, ui) { ajaxDialog.remove();},
			open: function(event, ui) { }
    });
	ajaxDialog.dialog("open");
	jQuery('.ajax_loader').show();
	
	jQuery.post(action, function(data) {						
        ajaxDialog.html(data);
        jQuery('.ajax_loader').fadeOut();
        /* select */
        jQuery('a.select_art').live('click', function(){
            var action2 = jQuery(this).attr('href');
          	jQuery.post(action2, function(data) {
				update_picture.children().remove();
				jQuery(cat_picture).children().remove();
				update_picture.html(data);
				jQuery(cat_picture).html(data);
          		ajaxDialog.dialog("close");	
          	});
          	return false;
      	});
      	/* upload */
      	jQuery('.album_img_upload').live('change', function() { 
		var value = jQuery(this).children('input[name="album_id"]').val(); 
		var cat_picture = '#album_'+value+' .cel_show .cel_image';
		jQuery(".album_img_upload").ajaxForm({
			success: function() {ajaxDialog.dialog("close");},
			target: '.album_art,'+ cat_picture
		}).submit();
		});
  	});
	return false;
});



/* delete artist - album*/
jQuery('a.delete_artist, a.delete_album, a.delete_playlist, a.delete_duplicated').live('click', function() {
	var action = jQuery(this).attr('href');
	var this_el = jQuery(this);
	if (jQuery("#dialog-confirm").length == 0) {
      	 var confirmDialog = jQuery('<div id="dialog-confirm" style="display:hidden">Supprimer ?<br />Cette action est définitive.</div>').appendTo('body');     	
	}
	confirmDialog.dialog({
		resizable: false, height:160, width:300, modal: true, dialogClass: "lightbox_confirm",
		close: function(event, ui) { confirmDialog.remove();},
		buttons: {
			"Annuler": function() { confirmDialog.dialog( "close" ); },
			"Supprimer": function() { jQuery.post(action, function(data) {
				if (this_el.hasClass('delete_duplicated')) {
					this_el.parent().parent().fadeOut();
				}
			
			}); confirmDialog.dialog( "close" );}
		}
	});
	return false;
});

/* delete artist - album*/
jQuery('a.delete_song').live('click', function() {
	var action = jQuery(this).attr('href');
	if (jQuery("#dialog-confirm").length == 0) {
      	 var confirmDialog = jQuery('<div id="dialog-confirm" style="display:hidden">Supprimer ?<br />Cette action est définitive.</div>').appendTo('body');     	
	}
	confirmDialog.dialog({
		resizable: false, height:160, width:300, modal: true, dialogClass: "lightbox_confirm",
		close: function(event, ui) { confirmDialog.remove();},
		buttons: {
			"Annuler": function() { confirmDialog.dialog( "close" ); },
			"Supprimer": function() { jQuery.post(action, function(data) {}); confirmDialog.dialog( "close" );}
		}
	});
	return false;
});
					
		


/*********************************************************************************************************************************************
           															 SEARCH BOX                             
*********************************************************************************************************************************************/

jQuery('a.search').live('click', function() {	
	if (jQuery("#lightbox_search").length == 0) { var ajaxDialog = jQuery('<div id="lightbox_search" style="display:hidden"></div>').appendTo('body');}
    ajaxDialog.dialog({
    	autoOpen: false, 
    	width:1050, height:50, modal:true, position: "center top+80", resizable: false, draggable: false,
    	dialogClass: "search_box",
		show: { effect: "clip"/*, easing:"swing", duration:750 */},
		hide: { duration:150 },
		close: function(event, ui) { ajaxDialog.remove();}
	});

    ajaxDialog.load(this.href);
    ajaxDialog.dialog("open"); 
	return false;
});

jQuery('#search_form').live('submit', function(){
		var action = jQuery(this).attr('action');
		var data = jQuery(this).serialize();
		jQuery.post(action, data, function(data) {
			jQuery("#lightbox_search").dialog( "close" );
			addTab();
			tabs.tabs( "refresh" );
			tabs.tabs( 'select', 5);
			jQuery('#ui-tabs-6').html(data);
  		});
		return false;
});	

function addTab() {
	var tabTemplate = "<li class='ui-state-default ui-corner-top search'><a href='#{href}'>#{label}</a> <span class='close_tab' role='presentation'></span></li>";
	var tabCounter = 6;
	if ((jQuery( "#ui-tabs-6").length == 1) && (jQuery( "#tabs ul li.search").length == 1)) {
		var panelId = jQuery( "#tabs ul li.search").remove().attr( "aria-controls" );
		jQuery( "#ui-tabs-6").remove();
		tabs.tabs( "refresh" );
	}
	var label = "RÉSULTATS", id = "ui-tabs-" + tabCounter, cssClass = "", li = jQuery( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ) ), tabContentHtml = "Test";
	tabs.find( ".ui-tabs-nav" ).append( li );
	tabs.append( "<div id='" + id + "' class='" + cssClass + "'>" + tabContentHtml + "</div>" );
	tabs.tabs( "refresh" );
} // addTab()

tabs.delegate( "span.close_tab", "click", function() {
	var panelId = jQuery( this ).closest( "li" ).remove().attr( "aria-controls" );
	jQuery( "#ui-tabs-6").remove();
	tabs.tabs( "refresh" );
});
	
/*	tabs.bind( "keyup", function( event ) {
	if ( event.altKey && event.keyCode ===jQuery.ui.keyCode.BACKSPACE ) {
	var panelId = tabs.find( ".ui-tabs-active" ).remove().attr( "aria-controls" );
	jQuery( "#" + panelId ).remove();
	tabs.tabs( "refresh" );
	}
	});*/








/*********************************************************************************************************************************************
           															AUDIOTACT INFO                            
*********************************************************************************************************************************************/
/* TAB - Audiotact infos - Adminisitration - Modération*/
//jQuery( "#main_tabs" ).tabs({ ajaxOptions: { error: function( xhr, status, index, anchor ) { jQuery( anchor.hash ).html("Erreur de chargement ");}}});
	
//jQuery( ".tabs-bottom .ui-tabs-nav, .tabs-bottom .ui-tabs-nav > *" ).removeClass( "ui-corner-all ui-corner-top" ).addClass( "ui-corner-bottom" );
var maintabs = jQuery( "#main_tabs" ).tabs({
				create: function( event, ui ) {},
				load: function( event, ui ) {},
				ajaxOptions: { error: function( xhr, status, index, anchor ) { jQuery( anchor.hash ).html("Erreur lors du chargement de la page" );}}
});

/*********************************************************************************************************************************************
           															ADMINISTRATION                            
*********************************************************************************************************************************************/
/* Mot de passe - borne */
jQuery('.update_login').live('submit', function(){
	var action = jQuery('.update_login').attr('action');
	var data = jQuery('.update_login').serialize();
	jQuery.post(action, data, function(data) {
		alert (data);
		jQuery('.update_login .saved').fadeIn().delay(2000).fadeOut();
  	});
return false;
});

/* Mot de passe - borne */
jQuery('.update_mdp').live('submit', function(){
	var action = jQuery('.update_mdp').attr('action');
	var data = jQuery('.update_mdp').serialize();
	jQuery.post(action, data, function(data) {
		jQuery('.update_mdp .saved').fadeIn().delay(2000).fadeOut();
  	});
return false;
});

/* Nom associé à la borne */
jQuery('.update_web_title').live('submit', function(){
	var action = jQuery('.update_web_title').attr('action');
	var data = jQuery('.update_web_title').serialize();
	jQuery.post(action, data, function(data) {
		jQuery('.update_web_title .saved').fadeIn().delay(2000).fadeOut();
  	});
return false;
});

/* Mise à jour catalogue */
jQuery('.update_catalog').live('submit',function(){
	if (jQuery("#lightbox_catalog").length == 0) {
      	var ajaxDialog = jQuery('<div id="lightbox_catalog" style="display:hidden" title="Mise à jour du catalogue"></div>').appendTo('body');     
	}
	ajaxDialog.dialog({
		autoOpen: false, width:860,height:440,dialogClass: "background_box",modal:true,resizable: false,draggable: false, 
		show: {effect: "clip", easing:"swing", duration:750 },hide: {effect: "clip"},close: function(event, ui) { ajaxDialog.remove();}
	});
	ajaxDialog.dialog("open");
	jQuery('.ajax_loader').show();
	var options = { target: '#lightbox_catalog', success:    function() { /*jQuery('#scrollbar_wrapper').mCustomScrollbar({ scrollButtons:{enable:true}, advanced:{ updateOnContentResize: true}});*/ 
	updateScroll = new iScroll('scrollbar_wrapper', { hScrollbar: false, vScrollbar: false});
	jQuery('.ajax_loader').hide();} }; 	
	jQuery(this).ajaxSubmit(options); 		
	return false;
});
	

/* Lightbox - Mise à jour des infos Audiotact */
jQuery('.admin_infobox').live('click', function() {
	var name= jQuery(this).attr("title");
	if (jQuery("#lightbox_catalog").length == 0) {
      	 var ajaxDialog = jQuery('<div id="lightbox_catalog" style="display:hidden" title="'+(name)+'"></div>').appendTo('body');	
	}
    ajaxDialog.dialog({
      	autoOpen: false, width:855,height:350,position: "center top+30",modal:true,resizable: false,draggable: false, 
      	show: {effect: "clip", easing:"swing", duration:750 },
		hide: {effect: "clip"},
		close: function(event, ui) { ajaxDialog.remove();}
     });
 	ajaxDialog.load(this.href);
    ajaxDialog.dialog("open");
	return false;
});

jQuery('.infobox_form').live('submit', function(){
	var action = jQuery(this).attr('action');
	var id = jQuery('.infobox_form input.id').attr('value');
	var box = '.infobox_'+id ;
	var data = jQuery('.infobox_form').serialize();
	jQuery.post(action, data, function(data) {
		jQuery(box).html(data);
		jQuery("#lightbox_catalog").dialog("close");	
  	});
	return false;
});

/* Background */
jQuery('.admin_background').live('click', function() {
	var name= jQuery(this).attr("title");
	if (jQuery("#lightbox_catalog").length == 0) {
      	 var ajaxDialog = jQuery('<div id="lightbox_catalog" style="display:hidden" title="'+(name)+'"></div>').appendTo('body');     	
	}
    ajaxDialog.dialog({
      	autoOpen: false, width:860,height:440,dialogClass: "background_box",modal:true,resizable: false,draggable: false, 
      	show: {effect: "clip", easing:"swing", duration:750 },
		hide: {effect: "clip"},
		close: function(event, ui) { ajaxDialog.remove();}
   });
 	ajaxDialog.load(this.href);
    ajaxDialog.dialog("open");
	return false;
});
	

/* Favoris selection type*/
jQuery('#select_favorit_type input').live('change', function(e){			
	jQuery(this).prop('checked', true);
	var action = jQuery('#select_favorit_type').attr('action');
	var data = jQuery('#select_favorit_type').serialize();
	e.preventDefault();
	jQuery.post(action, data, function(data) {});
});
			
/*********************************************************************************************************************************************
           															MODERATION                            
*********************************************************************************************************************************************/	

/******** CONTRIBUTIONS ********/		
jQuery('a.admin_submit').live('click', function() {
	var el = jQuery(this);
	var action = jQuery(this).attr('href');
	var box_id = jQuery(this).attr('id');
	var box = 'div#'+ box_id;
	if (el.hasClass('validate')) { var box = jQuery('<div id="dialog-confirm" style="display:hidden">Valider ?</div>');}
	else if (el.hasClass('delete')) { var box = jQuery('<div id="dialog-confirm" style="display:hidden">Supprimer ?<br />Cette action est définitive.</div>');}
	
	if (jQuery("#dialog-confirm").length == 0) {
      	 var confirmDialog = box.appendTo('body');     	
	}
	confirmDialog.dialog({
		resizable: false, height:160, width:300, modal: true, dialogClass: "lightbox_confirm",
		close: function(event, ui) { confirmDialog.remove();},
		buttons: {
			"Annuler": function() { confirmDialog.dialog( "close" ); },
			"Ok": function() { 
				jQuery.post(action, function(data) {
					jQuery('#main_tabs_content').load('admin_moderation.php #contrib_mod_wrap', function(){
					jQuery(".audio").mb_miniPlayer({swfPath:"../modules/html5/Jplayer.swf"});
					jQuery('.flash_player').height('0').width('0');
					contribScroll = new iScroll('contribScroll', { hScrollbar: false, vScrollbar: false});		
					});
				}); 
				confirmDialog.dialog( "close" );
			}
		}
	});
	return false;
});

jQuery("#contrib_mod_pag li").live('click', function(){
	var url = jQuery(this).children('a').attr('href') ;
	jQuery("#contrib_mod_wrap").load(url+this.id, function(){
		jQuery(".audio").mb_miniPlayer({ swfPath:"../modules/html5/Jplayer.swf"});
		jQuery('.flash_player').height('0').width('0');
		contribScroll = new iScroll('contribScroll', { hScrollbar: false, vScrollbar: false});	
	});
	return false;
});

/******** COMMENTAIRES ********/

/* Comment click */
jQuery('#update_comment .shouttext').live('click', function() {
	jQuery(this).addClass('active_shoutext');
	jQuery(this).next('.edit_comment_button').css({'display':'block'});
	jQuery(this).css({'background':'white','border-radius': '5px 5px 5px 5px','box-shadow': '2px 2px 4px #8B8B8B'});
});
		
/* Valide update text comment */	
jQuery('.edit_comment_button').live('click', function(){
	var button = jQuery(this);
	var textarea = button.prev('.shouttext');
	var action = jQuery(this).children('a').attr('href');
	var data = jQuery(this).parent().serialize();

	jQuery.post(action, data, function(data) {
		button.css({'display':'none'});
	    textarea.css({'background': 'none repeat scroll 0 0 transparent','border': 'medium none','border-radius': 'none','box-shadow': 'none'}).removeClass('active_shoutext');
      	textarea.autosize();
  	});
	return false;
});
		
/******** TAGS ********/		
jQuery('a.tag').live('click',function(){
	var el = jQuery(this);	
	var url = jQuery(this).attr('href') ;
	jQuery.post(url, function(data) {
		if (data =='active_delete') { el.removeClass('inactive_delete'); el.addClass(data);} 
		else if (data =='inactive_delete') {el.removeClass('active_delete');el.addClass(data);}
	});
	return false;
});
	

		
});