/*!
 * jQuery fbGrabber Plugin v2.0 17-9-2010
 * Examples and documentation at: http://www.entula.net/wordpress-facebook-grabber/
 * Home: http://www.entula.net/wordpress-facebook-grabber/
 * Author: http://www.borraccetti.it/borraccetti
 * Copyright (c) 2010 Fabio Borraccetti
 *
 * Version: 2.0 (17-9-2010)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * 
 * Tested and Developer with jQuery 1.4
 * Requires: jQuery v1.4 or later
 * 
 *
 */


(function($){
  // plugin definition
	$.fn.grabFBalbum = function(options) {
		var theobj = $(this);
		var settings = {
			fburl: '',
			maxitem: 2
		}
		if(options) 
			jQuery.extend(settings, options);
		// save where we working on
		$.ajax({
			url: settings.fburl,
			dataType: "jsonp",
			cache: true,
			error: function(e){
				$(theobj).append("Error loading document".e);
			},
			success: function(data) {
				//cycle eash item and added to target object.
				$.each(data.data,function(i,item){
					o=0;
					$(theobj).append('<div class="fb_thumb" id=="fb_thumb'+o+'"><a href="'+item.source+'"  rel="lightbox" ><img class="UIPhotoGrid_Image" src="'+item.picture+'" title="'+item.name+'"/></a></div>');
					if(o>=settings.maxitem)
						return false;
					o++;
				});
			}
		});
		//called ajax with json option and fetched all info.
	}
	
	$.fn.grabFBfeed	= function(options) {
		var theobj = $(this);
		var settings = {
			fburl: '',
			maxitem: 2
		}
		if(options) 
			jQuery.extend(settings, options);

		// save where we working on
		$.ajax({
			url: settings.fburl,
			dataType: "jsonp",
			cache: true,
			error: function(e){
				$(theobj).append("Error loading document".e);
			},
			success: function(data) {
				//cycle eash item and added to target object.
				o=0;
				$.each(data.data,function(i,item){
					row='';
					row += '<div class="fb_feed" id="fb_feed'+o+'">';
						row += '<span class="fb_from">'+item.from.name+'</span> ';
						if(item.link != undefined)
							row += '<a href="'+item.link+'"  target="_blank" >';
						if(item.name != undefined)
							row += '<p class="fb_feed_title">'+item.name+'</p>';
						if(item.picture != undefined)
							row += '<div class="fb_feed_thumb"> <img class="UIPhotoGrid_Image" src="'+item.picture+'" title="'+item.from.name+'"/></div>';
						if(item.link != undefined)
							row += '</a>';
						row += '<div class="fb_feed_desc">'+item.message+'</div>';
					row += '</div>';
					$(theobj).append(row);
					if(o>=settings.maxitem-1)
						return false;
					o++;					
				});
			}
		});
		//called ajax with json option and fetched all info.
	}
	
	
})(jQuery);	

