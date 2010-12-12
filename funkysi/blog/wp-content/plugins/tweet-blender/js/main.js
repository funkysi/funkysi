/**
 * @author http://kirill-novitchenko.com
 */

var TB_version = '3.2.4',	// Plugin version 
TB_rateLimitData,
TB_tmp,
TB_mode = 'widget',
TB_started = false,
TB_monthNumber = {'Jan':1,'Feb':2,'Mar':3,'Apr':4,'May':5,'Jun':6,'Jul':7,'Aug':8,'Sep':9,'Oct':10,'Nov':11,'Dec':12},
TB_timePeriods = new Array("second", "minute", "hour", "day", "week", "month", "year", "decade"),
TB_timePeriodLengths = new Array("60","60","24","7","4.35","12","10"),
TB_tweetsToCache = new Object(),
TB_allSources = new Array(),
jQnc = jQuery.noConflict(),
TB_sourceCounts = new Array(),
TB_sourceNames = new Array(),
TB_seenTweets = new Array();

// initialize each widget
function TB_start() {

	// prevent initializing twice
	if (TB_started) {
		return;
	}
	else {
		TB_started = true;
	}
	
	// check to make sure config is included
	if (typeof(TB_config) == 'undefined') {
		TB_showMessage(null,'noconf','No configuration settings found.',true);
		return;
	}
	
	// process widget configuration
	TB_config.widgets = new Object();
	jQuery.each(jQuery('form.tb-widget-configuration'),function(i,obj){

		var widgetConfId = obj.id,
		widgetId,
		widgetHTML,
		needWidgetHTML = false;
		
		// if there is widget HTML div following the form we don't need to build HTML
		if (jQuery('#'+obj.id).next().length > 0) { 
			if (jQuery('#' + obj.id).next().attr('id') != '') {
				if (jQuery('#' + obj.id).next().attr('id').indexOf('-mc') > 0) {
					widgetId = widgetConfId.substr(0, widgetConfId.length - 2);
				}
				else {
					needWidgetHTML = true;
					widgetId = widgetConfId;
				}
			}
			else {
				needWidgetHTML = true;
				widgetId = widgetConfId;
			}
		}
		// if it's just a form -> assume that's post/page body content
		else {
			needWidgetHTML = true;
			widgetId = widgetConfId;
		}
		
		TB_config.widgets[widgetId] = new Object;
		
		// set all properties (backward compatibiliy)
		jQuery.each(jQuery('#'+widgetConfId).children('input'),function(j,property) {
			TB_config.widgets[widgetId][property.name] = property.value;
		});
		// set all properties
		jQuery.each(jQuery('#'+widgetConfId + ' > div').children('input'),function(j,property) {
			TB_config.widgets[widgetId][property.name] = property.value;
		});
		
		if (typeof(TB_config.widgets[widgetId].sources) != 'undefined') {
			TB_allSources = TB_allSources.concat(TB_config.widgets[widgetId].sources.split(','));
		}
		
		if (needWidgetHTML) {
			// add widget HTML
			widgetHTML = '<div id="' + widgetId + '-mc"><div class="tb_header">' +
				'<img class="tb_twitterlogo" src="' + TB_pluginPath + '/img/twitter-logo.png" alt="Twitter Logo" />' +
				'<div class="tb_tools" style="background-image:url(' + TB_pluginPath + '/img/bg_sm.png)">' +
				'<a class="tb_infolink" href="http://kirill-novitchenko.com" title="Tweet Blender by Kirill Novitchenko" style="background-image:url(' + TB_pluginPath + '/img/info-kino.png)"> </a>' +
				'<a class="tb_refreshlink" href="javascript:TB_blend(\'' + widgetId + '\');" title="Refresh Tweets"><img src="' + TB_pluginPath + '/img/ajax-refresh-icon.gif" alt="Refresh" /></a></div></div>';
			if (TB_config.general_seo_tweets_googleoff) {
				tweetHTML += '<!--googleoff: index--><div class="tb_tweetlist"></div><!--googleon: index-->';
			}
			else {
				widgetHTML += '<div class="tb_tweetlist"></div>';
			}
			widgetHTML += '<div class="tb_footer">';
			if (!TB_config.archive_is_disabled) {
				if (TB_config.widgets[widgetId].viewMoreUrl) {
					widgetHTML += '<a class="tb_archivelink" href="' + TB_config.widgets[widgetId].viewMoreUrl + '">view more &raquo;</a>';
				}
				else if (TB_config.default_view_more_url) {
					widgetHTML += '<a class="tb_archivelink" href="' + TB_config.default_view_more_url + '">view more &raquo;</a>';
				}
			}
			widgetHTML += '</div></div>';
			jQuery('#'+obj.id).after(widgetHTML);
		}
	});

	// if there are no widgets on the page - no need to continue
	if (TB_getObjectSize(TB_config.widgets) < 1) {
		return;
	}
	
	// de-dupe list of all sources
	TB_allSources = TB_getUniqueElements(TB_allSources);
	
	/* check opt out
	jQuery.ajax({
		dataType: 'jsonp',
		url: 'http://tweet-blender.com/check_optout.php',
		timeout: 500,
		data: ({
			u: window.location.href,
			s: TB_allSources.join(','),
			v: 'wp_' + TB_version
		}),
		success: function (json) {
			if (!json.ERROR) {
				if (json.chk == 0) {
					jQuery('div.tb_tools').css('background-image','url(' + TB_pluginPath + '/img/bg.png)').width(56);
					jQuery('a.tb_infolink').css('display','inline').css('margin-right','11px');
				}
			}
		}
	});
	*/
	jQuery('div.tb_tools').css('background-image','url(' + TB_pluginPath + '/img/bg.png)').width(56);
	jQuery('a.tb_infolink').css('display','inline').css('margin-right','11px');
	
	// make sure plugins are available
	if (typeof(jQuery.toJSON) == 'undefined' && typeof(jQnc.toJSON) == 'function') {
		jQuery.toJSON = jQnc.toJSON;
	}

	// if there is no archive page, hide view more links
	if (!TB_config.default_view_more_url) {
		jQuery('a.defaultUrl').hide();
	}
	
	// get config options and blend
	if (typeof(TB_config) != 'undefined') {
		
		// if admin turned on re-route
		if (TB_config['advanced_reroute_on']) {
			TB_config['rate_limit_url'] = {
				'url': TB_pluginPath + '/ws.php?action=rate_limit_status',
				'dtype': 'json'
			};
		}
		// else check limit for the user's PC
		else {
			TB_config['rate_limit_url'] = {
				'url': 'http://twitter.com/account/rate_limit_status.json',
				'dtype': 'jsonp'
			};
		}
		
		// for each widget on the page
		for (widgetId in TB_config.widgets) {
			
			if (typeof(TB_config.widgets[widgetId].sources) == 'undefined' || TB_config.widgets[widgetId].sources == '') {
				TB_showMessage(widgetId,'nosrc','Twitter sources to blend are not defined', true);
				
				// TODO: disable refresh
				//jQuery('#refreshlink').remove();
			}
			else {
	
				// create info box shown when Twitter logo is clicked
				TB_initInfoBox(widgetId);
				
				// create all the urls for refresh calls
				TB_makeAjaxURLs(widgetId);			
	
				// update values to reflect cache use if there are divs with tweets already
				TB_config.widgets[widgetId]['minTweetId'] = 0;
				TB_config.widgets[widgetId]['maxTweetId'] = 0;
				if (jQuery('#'+widgetId + '-mc > div.tb_tweetlist > div.tb_tweet').size() > 0) {
					if (TB_tmp = jQuery('#'+widgetId + '-mc > div.tb_tweetlist > div:last').attr('id')) {
						TB_config.widgets[widgetId]['minTweetId'] = TB_tmp;
					}
					if (TB_tmp = jQuery('#'+widgetId + '-mc > div.tb_tweetlist > div:first').attr('id')) {
						TB_config.widgets[widgetId]['maxTweetId'] = TB_tmp;
					}
				}
				TB_config.widgets[widgetId]['tweetsShown'] = jQuery('#'+widgetId + '-mc > div.tb_tweetlist').children('div').size();
				
				// wire mouse overs to existing tweets
				jQuery.each(jQuery('#' + widgetId + '-mc > div.tb_tweetlist').children('div'),function(i,obj){ TB_wireMouseOver(obj.id); });

					// wire target="_blank" on links
					jQuery('a.tb_photo, .tb_author a, .tb_msg a, .tweet-tools a, .tb_infolink').click(function(){
						this.target = "_blank";
					});
		
				// add automatic refresh
				if (parseInt(TB_config.widgets[widgetId].refreshRate) > 1) {
					setInterval('TB_blend(\''+widgetId+'\');',parseInt(TB_config.widgets[widgetId].refreshRate) * 1000);
				}
				
				// if we need to refresh once or 
				// if there are no tweets shown from cache
				// or if there are less tweets then needed
				// then blend right away
				if (parseInt(TB_config.widgets[widgetId].refreshRate) == 1 || TB_config.widgets[widgetId].tweetsShown < TB_config.widgets[widgetId].tweetsNum) {
					TB_blend(widgetId);
				}
			}
		}
	}
	else {
		TB_showMessage(null,'noconf','Cannot retrieve Tweet Blender configuration options',true);
	
		// disable refresh
		jQuery('a.tb_refreshlink').remove();
		jQuery('div.tb_tools').css('background-image','url(' + TB_pluginPath + '/img/bg_sm.png)').width(28);
	}
}

// form Twitter API queries
function TB_makeAjaxURLs(widgetId) {
	var TB_searchTerms = new Array(),
	TB_screenNameQueries = new Array(),
	TB_screenNames = new Array(),
	screenName = '',
	modifier = '',
	colonPos,
	pipePos;
	
	TB_config.widgets[widgetId]['ajaxURLs'] = new Array();

	jQuery.each(TB_config.widgets[widgetId].sources.split(','),function(i,src) {

		// remove spaces
		src = jQuery.trim(src);
		
		// if it's a private screen name
		if (src.charAt(0) == '!') {

			// if there is an alias
			if ((colonPos = src.indexOf(':')) > 0) {
				// split into screen name and optional string name/title
				screenName = src.substr(2, colonPos - 1);
				TB_sourceNames[screenName.toLowerCase()] = src.substr(colonPos + 1);
				src = src.substr(1, colonPos - 1);
			}
			else {
				screenName = src.substr(2);
			}

			// if we are serving only favorites
			if (TB_config.widgets[widgetId].favoritesOnly) {
				TB_addAjaxUrl(widgetId,'favorites',screenName,src,1);
			}
			// if we are not using Search API
			else if (TB_config.advanced_no_search_api) {
				TB_addAjaxUrl(widgetId,'user_timeline','screen_name=' + screenName,src,1);
			}
			else {
				TB_addAjaxUrl(widgetId,'search','&from=' + screenName,src,1);
			}
		}
		// if it's a public screen name
		else if (src.charAt(0) == '@' && src.indexOf('/') == -1) {
			
			// if we are serving only favorites
			if (TB_config.widgets[widgetId].favoritesOnly) {
				// if there is an alias
				if ((colonPos = src.indexOf(':')) > 0) {
					// split into screen name and optional string name/title
					screenName = src.substr(1, colonPos - 1);
					TB_sourceNames[screenName.toLowerCase()] = src.substr(colonPos + 1);
					src = src.substr(0, colonPos);
				}
				else {
					screenName = src.substr(1);
				}

				TB_addAjaxUrl(widgetId,'favorites',screenName,src,0);
			}
			// if it includes modifiers, use a one-off URL
			else if ((pipePos = src.indexOf('|')) > 1) {
				// if we had an alias for that name
				if ((colonPos = src.indexOf(':')) > 0) {
					// split into screen name and optional string name/title
					screenName = src.substr(1,pipePos-1);
					modifier = src.substr(pipePos+1,(colonPos - pipePos - 1));
					TB_sourceNames[screenName.toLowerCase()] = src.substr(colonPos + 1);
					src = src.substr(0, colonPos);
				}
				else {
					screenName = src.substr(1,pipePos-1);
					modifier = src.substr(pipePos+1);
				}
				
				// if modifier is a hashtag
				if (modifier.charAt(0) == '#') {
					TB_addAjaxUrl(widgetId,'search','&from=' + screenName + '&tag=' + modifier.substr(1),src,0);
				}
				else {
					TB_addAjaxUrl(widgetId,'search','&from=' + screenName + '&ors=' + modifier,src,0);
				}
			}
			else {

				// if we had an alias for that name
				if ((colonPos = src.indexOf(':')) > 0) {
					// split into screen name and optional string name/title
					screenName = src.substr(1,colonPos-1);
					TB_sourceNames[screenName.toLowerCase()] = src.substr(colonPos + 1);
					src = src.substr(0, colonPos);
				}
				else {
					screenName = src.substr(1);
				}
				
				// if we are not using Search API
				if (TB_config.advanced_no_search_api) {
					TB_addAjaxUrl(widgetId,'user_timeline','screen_name=' +screenName,src,0);
				}
				// else, group with other screen names
				else {
					// check to make sure we are not over the query length limit
					if (escape(TB_screenNameQueries.join(' OR ')).length + src.length > 140) {
						TB_addAjaxUrl(widgetId,'search','&q=' + escape(TB_screenNameQueries.join(' OR ')),escape('@'+TB_screenNames.join(',@')),0);
						TB_screenNames = new Array();
						TB_screenNameQueries = new Array();
					}
					TB_screenNames.push(screenName);
					if (TB_config.filter_hide_mentions) {
						TB_screenNameQueries.push('from:' + screenName);
					}
					else {
						TB_screenNameQueries.push(src + ' OR from:' + screenName);
					}
				}
			}
		}
		// if it's a list
		else if (src.charAt(0) == '@' && src.indexOf('/') > 1) {
			if (TB_config.advanced_reroute_on || TB_config.reached_api_limit) {
				TB_addAjaxUrl(widgetId,'list_timeline','&user=' + src.substr(1, src.indexOf('/') - 1) + '&list=' + src.substr(src.indexOf('/') + 1),src,0);
			}
			else {
				TB_addAjaxUrl(widgetId,'list_timeline',src.substr(1, src.indexOf('/') - 1) + '/lists/' + src.substr(src.indexOf('/') + 1) + '/statuses.json',src,0);
			}
		}
		// else it's a hash or keyword 
		else if (src != '') {

 			// if it's a multi-word keyword give it a dedicated ajax call
			if (src.indexOf(' ') > 0) {
				// if it's not in quotes already then add them
				if (src.charAt(0) != '"') {
					src = '"' + src + '"';
				}
			}

			// check to make sure we are not over the query length limit
			if (escape(TB_searchTerms.join(' OR ')).length + src.length > 140) {
				TB_addAjaxUrl(widgetId,'search','&q=' + escape(TB_searchTerms.join(' OR ')),escape(TB_searchTerms.join(',')),0);
				TB_searchTerms = new Array();
			}
			TB_searchTerms.push(src);

/*
 			// if it's a multi-word keyword give it a dedicated ajax call
			if (src.indexOf(' ') > 0) {
				TB_addAjaxUrl(widgetId,'search','&q=' + src,src,0);
			}
			// else it will be grouped with the rest
			else {
				// check to make sure we are not over the query length limit
				if (escape(TB_searchTerms.join(' ')).length + src.length > 140) {
					TB_addAjaxUrl(widgetId,'search','&ors=' + escape(TB_searchTerms.join(' ')),escape(TB_searchTerms.join(',')),0);
					TB_searchTerms = new Array();
				}
				TB_searchTerms.push(src);
			}
*/					
		}
	});
	
	// if there are terms that are not part of a query - add another query
	if (TB_searchTerms.length > 0) {
		TB_addAjaxUrl(widgetId,'search','&q=' + escape(TB_searchTerms.join(' OR ')),escape(TB_searchTerms.join(',')),0);
	}
	
	// if there are screenNames - join them into a single query
	if (TB_screenNames.length > 0) {
		TB_addAjaxUrl(widgetId,'search','&q=' + escape(TB_screenNameQueries.join(' OR ')),escape('@'+TB_screenNames.join(',@')),0);
	}
}

function TB_addAjaxUrl(widgetId,actionType,urlPart,src,isPrivateSrc) {
	var langFilter = '',
	locationFilter = '',
	negativeFilter = '',
	privateParam = '';

	// check language filter	
	if (typeof(TB_config['filter_lang']) != 'undefined' && TB_config.filter_lang.length == 2) {
		langFilter = '&lang=' + TB_config.filter_lang;
	}
	else {
		langFilter = '&lang=all';
	}
	
	/* FUTURE: check location filter	
	if (typeof(TB_config['filter_location_name']) != 'undefined' && TB_config.filter_location_name.length > 0) {
		locationFilter = escape('near:' + TB_config.filter_location_name + ' within:' + TB_config.filter_location_dist + TB_config.filter_location_dist_units);
	}
	*/
	
	// check negative keywords
	if (typeof(TB_config['filter_bad_strings']) != 'undefined' && TB_config.filter_bad_strings.length > 0) {
		negativeFilter = '&nots=' + escape(TB_config.filter_bad_strings.split(',').splice(0,9).join(' '));
	}
	
	// check private
	if (isPrivateSrc) {
		privateParam = '&private=1';
	}

	if (actionType == 'search' && (TB_config.advanced_reroute_on || TB_config.reached_api_limit || isPrivateSrc)) {
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url':TB_pluginPath + '/ws.php?action=search' + urlPart + langFilter + locationFilter + negativeFilter + privateParam,
			'source':src,
			'privateSrc':isPrivateSrc,
			'dtype':'json'
		});
	}
	else if (actionType == 'search') {
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url': 'http://search.twitter.com/search.json?' + locationFilter + urlPart + langFilter + negativeFilter,
			'source':src,
			'privateSrc':0,
			'dtype':'jsonp'
		});
	}
	else if (actionType == 'list_timeline' && (TB_config.advanced_reroute_on || TB_config.reached_api_limit)) {
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url':TB_pluginPath + '/ws.php?action=list_timeline' + urlPart,
			'source':src,
			'privateSrc':0,
			'dtype':'json'
		});
	}
	else if (actionType == 'list_timeline'){
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url':'http://api.twitter.com/1/' + urlPart,
			'source':src,
			'privateSrc':0,
			'dtype':'jsonp'
		});
	}
	else if (actionType == 'user_timeline' && (TB_config.advanced_reroute_on || TB_config.reached_api_limit || isPrivateSrc)) {
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url':TB_pluginPath + '/ws.php?action=user_timeline&' + urlPart,
			'source':src,
			'privateSrc':0,
			'dtype':'json'
		});
	}
	else if (actionType == 'user_timeline') {
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url': 'http://twitter.com/statuses/user_timeline.json?' + urlPart,
			'source':src,
			'private':0,
			'dtype':'jsonp'
		});
	}
	else if (actionType == 'favorites' && (TB_config.advanced_reroute_on || TB_config.reached_api_limit || isPrivateSrc)) {
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url':TB_pluginPath + '/ws.php?action=favorites&user=' + urlPart,
			'source':src,
			'privateSrc':0,
			'dtype':'json'
		});
	}
	else if (actionType == 'favorites') {
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url': 'http://api.twitter.com/1/favorites/' + urlPart + '.json',
			'source':src,
			'private':0,
			'dtype':'jsonp'
		});
	}
}

function TB_initInfoBox(widgetId) {
	// create HTML for sources
	TB_config.widgets[widgetId].sourcesHTML = '';
	TB_config.widgets[widgetId].sourcesCount = 0;
	jQuery.each(TB_config.widgets[widgetId].sources.split(','),function(i,src) {
		// if there is a private source mark - strip it
		if (src.charAt(0) == '!') {
		 	src = src.substr(1);
		}
		// if there is an alias - strip it
		if ((colonPos = src.indexOf(':')) > 0) {
			src = src.substr(0, colonPos);
		}
		// if there is a modfier - strip it
		if ((pipePos = src.indexOf('|')) > 0) {
			src = src.substr(0, pipePos);
		}
		
		
		TB_config.widgets[widgetId].sourcesHTML += '<a href="';
		if (src.charAt(0) == '@') {
		 	TB_config.widgets[widgetId].sourcesHTML += 'http://twitter.com/' + src.substr(1);
		}
		else {
		 	TB_config.widgets[widgetId].sourcesHTML += 'http://search.twitter.com/search?q=' + escape(src);
		}
		TB_config.widgets[widgetId].sourcesHTML += '">' + src + '</a> ';
		TB_config.widgets[widgetId].sourcesCount++;
	});		
	
	// add action to twitter logo
	jQuery('#' + widgetId + '-mc').children('div.tb_header').children('img.tb_twitterlogo').click(function(){
		TB_showMessage(widgetId,'info','Powered by Tweet Blender plugin v' + TB_version + ' blending ' + TB_config.widgets[widgetId].sourcesHTML,false);
	});	
}

function TB_blend(widgetId) {

	// show loading indicator
	TB_showLoader(widgetId);

	// if not using cache/server then check limit for user viwing the page
	if (!TB_config.advanced_reroute_on && !TB_config.reached_api_limit) {
		jQuery.ajax({
			url: TB_config.rate_limit_url.url,
			dataType: TB_config.rate_limit_url.dtype,
			success: function(json){
				// if can't get the limit or reached it
				if (json.error || json.remaining_hits < TB_config.widgets[widgetId].ajaxURLs.length) {

					TB_config['reached_api_limit'] = true;
					
					// if cache is not disabled, reroute traffic through server
					if (!TB_config.advanced_disable_cache) {
						// switch back to normal mode once limit has been reset
						var wait = 1000 * 60 * 5,	// by default, try again in 5 minutes
						now = new Date(),
						dateObj;
						// if we have actual reset time, use it
						if (json.reset_time) {
							dateObj = TB_str2date(json.reset_time);
							wait = Math.round(dateObj.getTime() - now.getTime());
						}
						setTimeout("TB_config.reached_api_limit=false;TB_makeAjaxURLs('"+widgetId+"');TB_blend('"+widgetId+"');",wait);

						// regen URLs so they go to server and get tweets
						TB_makeAjaxURLs(widgetId);
						TB_getTweets(widgetId);
					}
					// if we reached limit, don't have cache turned on, and need to tell user - show message
					else if (TB_config.advanced_show_limit_msg) {
						TB_showMessage(widgetId,'limit','You reached Twitter API connection limit. Next reset ' + TB_verbalTime(TB_str2date(json.reset_time)), false);
					}
				}
				// else, get new feeds
				else {
					TB_getTweets(widgetId);
				}
			},
			error: function(){
				TB_getTweets(widgetId);
			}
		});
	}
	else {
		TB_getTweets(widgetId);
	}
}

function TB_checkComplete(widgetId) {
	
	if (TB_config.widgets[widgetId].urlsDone == TB_config.widgets[widgetId].ajaxURLs.length) {

		// hide loading message
		TB_hideLoader(widgetId);

		// if nothing added after we are through all sources let user know
		if(jQuery('#' + widgetId + '-mc > div.tb_tweetlist').children('div').size() == 0) {
			// show no tweets message
			
			/* FUTURE: include location in message
			if (typeof(TB_config['filter_location_name']) != 'undefined' && TB_config.filter_location_name.length > 0) {
				TB_showMessage(widgetId, 'notweets', 'No tweets found for ' + TB_config.widgets[widgetId].sourcesHTML + '(within ' + TB_config.filter_location_dist + TB_config.filter_location_dist_units + ' of ' + TB_config.filter_location_name + ')', true);
			}
			else {
			*/
				TB_showMessage(widgetId, 'notweets', 'No tweets found for ' + TB_config.widgets[widgetId].sourcesHTML, true);
		}
		else {
			TB_hideMessage(widgetId,'notweets');
			
			// store cache
			if((typeof(TB_config.advanced_disable_cache) != 'undefined' && !TB_config.advanced_disable_cache)) {
				TB_cacheNewTweets();	
			}
		}
	}
}
	
function TB_getTweets(widgetId) {
	
	TB_config.widgets[widgetId]['urlsDone'] = 0;
	
	// iterate over AJAX URLs
	jQuery.each(TB_config.widgets[widgetId].ajaxURLs,function(i,urlInfo) {
		jQuery.ajax({
			dataType: urlInfo.dtype,
			url: urlInfo.url,
			success: function (json) {
				// if we had valid JSON but with error
				if (json.error) {
					// if we reached the API limit
					if (json.error.indexOf('Rate limit exceeded') == 0) {
						TB_config['reached_api_limit'] = true;
					}
					TB_config.widgets[widgetId].urlsDone++;
					TB_checkComplete(widgetId);
				}
				else {
					TB_addTweets(widgetId,json,urlInfo);
				}
			},
			error: function() {
				TB_config.widgets[widgetId].urlsDone++;
				TB_checkComplete(widgetId);
			}
		});
	});
}

function TB_cacheNewTweets() {

	if (TB_getObjectSize(TB_tweetsToCache) > 0) {
		
		jQuery.ajax({
			url: 		TB_pluginPath + '/ws.php?action=cache_data',
			type:		'POST',
			dataType: 	'json',
			data: ({
				tweets: jQuery.toJSON(TB_tweetsToCache)
			}),
			success: function(json){
				if (!json.error) {
					TB_tweetsToCache = new Object();
				}
			}
		});
	}
}

function TB_addTweets(widgetId,jsonData,urlInfo) {

	var i,
	tweets = jsonData,
	originalTweet,
	isNewTweet = false,
	tweetUser,
	badStrings,
	tb_tweet;
	
	if (typeof(jsonData.results) != 'undefined') {
		tweets = jsonData.results;
	}
	
	jQuery.each(tweets,function(i,tweetJson) {

		tb_tweet = new TB_tweet(tweetJson);
		
		// make sure it's OK to show
		if (!tb_tweet.isOKtoShow())	{
			return true;
		}
		
		tb_tweet.selectRelevantSources(urlInfo.source);
				
		isNewTweet = false;
		
		// if this tweet already in the set, skip it
		if (jQuery('#' + tb_tweet.divId).length > 0) {
			return true;
		}
		// if this is the first tweet, just add it and set it to be both min and max
		else if (TB_config.widgets[widgetId].tweetsShown == 0) {
			TB_config.widgets[widgetId].tweetsShown++;
			TB_config.widgets[widgetId].minTweetId = tb_tweet.divId;
			TB_config.widgets[widgetId].maxTweetId = tb_tweet.divId;			

			// add at the end
			jQuery('#'+widgetId+'-mc > div.tb_tweetlist').append(tb_tweet.getHTML());
			
			isNewTweet = true;
		}
		// if tweet older than the oldest
		else if (tb_tweet.isOlderThan(TB_config.widgets[widgetId].minTweetId)) {
			// if we are at max already, no need to work through the rest of this set as the rest will be older
			if (TB_config.widgets[widgetId].tweetsShown >= TB_config.widgets[widgetId].tweetsNum) {
				return false;
			}
			else {
				TB_config.widgets[widgetId].tweetsShown++;

				// add at the end
				jQuery('#'+widgetId+'-mc > div.tb_tweetlist').append(tb_tweet.getHTML());

				// make it the oldest
				TB_config.widgets[widgetId].minTweetId = tb_tweet.divId;
				
				// if we have only one tweet then make it the newest as well
				if (TB_config.widgets[widgetId].tweetsNum == 1) {
					TB_config.widgets[widgetId].maxTweetId = tb_tweet.divId;
				}
				
				isNewTweet = true;
			}
		}
		// if tweet is newer than the newest
		else if (tb_tweet.isNewerThan(TB_config.widgets[widgetId].maxTweetId)) {
			// if we are at max already, remove bottom tweet
			TB_enforceLimit(widgetId);
			
			// add in the beginning
			jQuery('#'+widgetId+'-mc > div.tb_tweetlist').prepend(tb_tweet.getHTML());
			TB_config.widgets[widgetId].tweetsShown++;

			// make it the newest
			TB_config.widgets[widgetId].maxTweetId = tb_tweet.divId;
			
			// if we have only one tweet then make it the oldest as well
			if (TB_config.widgets[widgetId].tweetsNum == 1) {
				TB_config.widgets[widgetId].minTweetId = tb_tweet.divId;
			}

			isNewTweet = true;
		}
		// if tweet is in the middle
		else {
			// if we are at max already, remove bottom tweet
			TB_enforceLimit(widgetId);

			// traverse currently shown tweets and insert in the appropriate spot
			var prevTweetId = TB_config.widgets[widgetId].maxTweetId,
			nextTweetId;
			jQuery('#'+widgetId+'-mc > div.tb_tweetlist > div.tb_tweet').each(function(i,nextTweet){
				nextTweetId = nextTweet.id;
				if (tb_tweet.isOlderThan(prevTweetId) && tb_tweet.isNewerThan(nextTweetId)) {
					jQuery('#'+prevTweetId).after(tb_tweet.getHTML());
					TB_config.widgets[widgetId].tweetsShown++;
					return false;
				}
				prevTweetId = nextTweetId;
			});
			
			// if got to here and tweet still not there, make it the last
			if (jQuery('#'+tb_tweet.divId).length <= 0) {
					jQuery('#'+TB_config.widgets[widgetId].minTweetId).after(tb_tweet.getHTML());
					TB_config.widgets[widgetId].minTweetId = tb_tweet.divId;
					// if we have only one tweet then make it the newest as well
					if (TB_config.widgets[widgetId].tweetsNum == 1) {
						TB_config.widgets[widgetId].maxTweetId = tb_tweet.divId;
					}
					TB_config.widgets[widgetId].tweetsShown++;
			}
			
			isNewTweet = true;
		}

		// if new tweet and cache is on, queue it for caching
		if (isNewTweet && (typeof(TB_config.advanced_disable_cache) != 'undefined' && !TB_config.advanced_disable_cache)) {
			TB_tweetsToCache[tb_tweet.divId] = {
				"s" :	tb_tweet.sources,
				"p" :	urlInfo.privateSrc,
				"t" :	tb_tweet.jsonCode
			};
		}
		
		// wire mouseover action items
        TB_wireMouseOver(tb_tweet.divId);
		
		// if filtering out same tweets - add text to seen tweets
		if (TB_config.filter_hide_same_text) {
			TB_seenTweets.push(tb_tweet.jsonCode.text);
		}
	});
	
	TB_config.widgets[widgetId].urlsDone++;
	
	// wire target="_blank" on links
	jQuery('a.tb_photo, .tb_author a, .tb_msg a, .tweet-tools a, .tb_infolink').click(function(){
		this.target = "_blank";
	});
	
	TB_checkComplete(widgetId);
}

function TB_wireMouseOver(tweetId) {
	// wire mouseover action items
    if(TB_config[TB_mode + '_show_reply_link'] || TB_config[TB_mode + '_show_follow_link']) {
		jQuery('#'+tweetId).hover(
		      function () {
				jQuery(this).find("div:last").slideDown()
		      }, 
		      function () {
		        jQuery(this).find("div:last").slideUp();
		      }
		);
	}		
}

function TB_enforceLimit(widgetId) {
	
	if (TB_config.widgets[widgetId].tweetsShown == TB_config.widgets[widgetId].tweetsNum) {
		var lastTweet = jQuery('#' + TB_config.widgets[widgetId].minTweetId),
		nextToLastTweet = lastTweet.prev('div.tb_tweet');
		
		// remove last tweet
		lastTweet.remove();
		TB_config.widgets[widgetId].tweetsShown--;
		
		// remove from cache queue as well if we planned to cache it
		delete TB_tweetsToCache[TB_config.widgets[widgetId].minTweetId];
		
		// if no tweets left, reset min and max and finish
		if (TB_config.widgets[widgetId].tweetsShown == 0) {
			TB_config.widgets[widgetId].minTweetId = 0;
			TB_config.widgets[widgetId].maxTweetId = 0;
			return;
		}
		else {
			// make next to last to be last now
			if(nextToLastTweet.length > 0) {
				TB_config.widgets[widgetId].minTweetId = nextToLastTweet.attr('id');
			}
		}
	}
}

function TB_showLoader(widgetId) {
	// if there are not tweets, show loading message
	if(TB_config.widgets[widgetId].tweetsShown == 0) {
		TB_showMessage(widgetId,'loading','Loading tweets...',true);
	}
	// show animated icon
	jQuery('#' + widgetId + '-mc > div.tb_header > div.tb_tools > a.tb_refreshlink > img').attr('src',TB_pluginPath + '/img/ajax-refresh.gif');
	jQuery('#' + widgetId + '-mc > div.tb_header > div.tb_tools > a.tb_refreshlink').addClass('loading');
}

function TB_hideLoader(widgetId) {
	// hide loading message
	TB_hideMessage(widgetId,'loading');

	// show static icon
	jQuery('#' + widgetId + '-mc > div.tb_header > div.tb_tools > a.tb_refreshlink > img').attr('src',TB_pluginPath + '/img/ajax-refresh-icon.gif');
	jQuery('#' + widgetId + '-mc > div.tb_header > div.tb_tools > a.tb_refreshlink').removeClass('loading');
}

function TB_showMessage(widgetId, messageId, msg, keepOnScreen){

	// if no widgetId is given -> show message in all widgets and ignore keepOnScreen
	if(!widgetId) {
		jQuery('div.tb_tweetlist').before('<div id="msg_' + messageId + '" class="tb_msg" style="display:none;">' + msg + '</div>');
		return;
	}
	
	// if it doesn't exist
	if (!jQuery('#' + widgetId + '-mc').children('#msg_' + messageId).length) {
		jQuery('#' + widgetId + '-mc').children('div.tb_tweetlist').before('<div id="msg_' + messageId + '" class="tb_msg" style="display:none;">' + msg + '</div>');
		jQuery('#' + widgetId + '-mc').children('#msg_' + messageId).slideDown();
		if (!keepOnScreen) {
			setTimeout('TB_hideMessage("' + widgetId + '","' + messageId + '")', 8000);
		}
	}
	// else if it's hidden
	else if (jQuery('#' + widgetId + '-mc').children('#msg_' + messageId).is(':hidden')) {
		jQuery('#' + widgetId + '-mc').children('#msg_' + messageId).slideDown();
	}
}

function TB_hideAllMessages() {
	jQuery('div.tb_msg').slideUp(1000,function(){jQuery('div.tb_msg').remove()});
}

function TB_hideMessage(widgetId,messageId) {
	jQuery('#' + widgetId + '-mc').children('#msg_' + messageId).slideUp(1000,function(){jQuery('#' + widgetId + '-mc').children('#msg_' + messageId).remove()});
}

// search: Wed, 27 May 2009 15:52:40 +0000
// user feed: Thu May 21 00:09:16 +0000 2009
function TB_str2date(dateString) {
	
	var dateObj = new Date(),
	dateData = dateString.split(/[\s\:]/);
	
	// if it's a search format
	if (dateString.indexOf(',') >= 0) {
		// $wday,$mday, $mon, $year, $hour,$min,$sec,$offset
		dateObj.setUTCFullYear(dateData[3],TB_monthNumber[""+dateData[2]]-1,dateData[1]);
		dateObj.setUTCHours(dateData[4],dateData[5],dateData[6],0);
	}
	// if it's a user feed format
	else {
		// $wday,$mon,$mday,$hour,$min,$sec,$offset,$year
		dateObj.setUTCFullYear(dateData[7],TB_monthNumber[""+dateData[1]]-1,dateData[2]);
		dateObj.setUTCHours(dateData[3],dateData[4],dateData[5],0);
	}

	return dateObj;
}

function TB_verbalTime(dateObj) {
   
    var j,
	now = new Date(),
	difference,
	verbalTime,
	prefix = '',
	postfix = '';
	
	if (now.getTime() > dateObj.getTime()) {
		difference = Math.round((now.getTime() - dateObj.getTime()) / 1000);
		postfix = ' ago';
	}
	else {
		difference = Math.round((dateObj.getTime() - now.getTime()) / 1000);
		prefix = 'in ';
	}
		
   
    for(j = 0; difference >= TB_timePeriodLengths[j] && j < TB_timePeriodLengths.length; j++) {
        difference = difference / TB_timePeriodLengths[j];
    }
    difference = Math.round(difference);
   
    verbalTime = TB_timePeriods[j];
    if (difference != 1) {
        verbalTime += 's';
    }
   
    return prefix + difference + ' ' + verbalTime + postfix;
}

function TB_addLoadEvent(func) { 
	var oldonload = window.onload; 
	if (typeof window.onload != 'function') { 
	    window.onload = func; 
	} else { 
	    window.onload = function() { 
	      oldonload(); 
	      func(); 
	    }
	} 
}

// function to get the size of an object
function TB_getObjectSize(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
}

// function to dedupe array
function TB_getUniqueElements(arr) {
    var uniques = [], i, val;
    for(i=arr.length;i--;){
        val = arr[i];  
        if(jQuery.inArray( val, uniques )===-1){
            uniques.unshift(val);
        }
    }
    return uniques;
}

// Add format function to enable templates
String.prototype.format = function() {
    var s = this,
        i = arguments.length;

    while (i--) {
        s = s.replace(new RegExp('\\{' + i + '\\}', 'gm'), arguments[i]);
    }
    return s;
};

// TB Class for tweets
function TB_tweet(tweetJson) {

	// raw JSON for the tweet
	this.jsonCode  = tweetJson;
		
	// tweetDate property
	this.tweetDate = TB_str2date(tweetJson.created_at);
	
	// author screen name property
	if (typeof(tweetJson.from_user) != 'undefined') {
		this.screenName = tweetJson.from_user;
	}
	else if (typeof(tweetJson.user.screen_name) != 'undefined') {
		this.screenName = tweetJson.user.screen_name;
	}
	
	// id of the tweet
	this.id = tweetJson.id_str;
		
	// sources relevant to this tweet
	this.sources = '';
	
	// process sources and select the ones relevant to this tweet
	this.selectRelevantSources = function(urlSources) {
		var allSources = new Array(),
		sourceScreenName = '@'+this.screenName,
		jsonCode = this.jsonCode;
		jQuery.each(urlSources.split(','),function(i,src) {
			if (sourceScreenName == src || jsonCode.text.indexOf(src) > 0){
				allSources.push(src);
			}
		});
		//  set property with relevant sources
		if (allSources.length > 0) {
			this.sources = allSources.join(',');
		}
		// just in case to make sure we don't have empty source
		else {
			this.sources = urlSources;
		}
	}
	
	/* setup template
	 * the following placeholders will be used
	 * {0} - tweet div id
	 * {1} - screen name
	 * {2} - profile image url
	 * {3} - tweet message
	 * {4} - tweet id as string
	 * {5} - date
	 * {6} - source as link
	 */
	// if user supplied a custom format - use it
	var template;
	if (TB_config.custom_template) {
		template = TB_config.custom_template;
	}
	// if not, use default format
	else {
		template = '<div id="{0}" class="tb_tweet">';
		if (TB_config['widget_show_photos']) {
			template += '<a class="tb_photo" rel="nofollow" href="http://twitter.com/{1}"><img src="{2}" alt="{1}"></a>';
		}
		if (TB_config['widget_show_user']) {
			template += '<span class="tb_author"><a rel="nofollow" href="http://twitter.com/{1}">{1}</a>: </span> ';
		}
		template += '<span class="tb_msg">{3}</span><br />';
		// start tweet footer with info
		if (!TB_config.general_seo_tweets_googleoff && TB_config.general_seo_footer_googleoff) {
			template += '<!--googleoff: index-->';
		}
		template += ' <span class="tb_tweet-info">';
		// show timestamp
		template += '<a rel="nofollow" href="http://twitter.com/{1}/statuses/{4}">{5}</a>';		
		// show source if requested
		if (TB_config['widget_show_source'] && tweetJson.source) {
			template += ' from {6}';
		}
		// end tweet footer
		template += '</span>';
		if (!TB_config.general_seo_tweets_googleoff && TB_config.general_seo_footer_googleoff) {
			template += '<!--googleon: index-->';
		}
		// add tweet tools
	   if (TB_config.widget_show_follow_link || TB_config.widget_show_reply_link) {
			template += '<div class="tweet-tools" style="display:none;">';
	        if (TB_config.widget_show_reply_link) {
	        	template += '<a rel="nofollow" href="http://twitter.com/home?status=@{1}%20&in_reply_to_status_id={4}&in_reply_to={1}">reply</a>';
	        }
	        if (TB_config.widget_show_follow_link && TB_config.widget_show_reply_link) {
	        	template += ' | ';
	        }
	        if (TB_config.widget_show_follow_link) {
	        	template += '<a rel="nofollow" href="http://twitter.com/{1}">follow {1}</a>';
	        }
	        template += '</div>'; 
		}
		// end tweet	
		template += "</div>\n";
	}
	
	// creates unique div ID for this tweet
	getDivId = function(tweetDate,screenName) {
		return 't-' + tweetDate.getTime() + '-' + screenName;	
	}

	// div id of the tweet
	this.divId = getDivId(this.tweetDate,this.screenName);
	
	// makes HTML for each tweet
	this.getHTML = function() {
		/* the following placeholders will be used in the template
		 * {0} - tweet div id
		 * {1} - screen name
		 * {2} - profile image url
		 * {3} - tweet message
		 * {4} - tweet id as string
		 * {5} - date
		 * {6} - source as link
		 */
		var textHtml = this.jsonCode.text,
		nameHtml,
		imageUrl = '',
		dateHtml = '',
		sourceHtml = '';

		if (typeof(TB_sourceNames[this.screenName.toLowerCase()]) != 'undefined') {
			nameHtml = TB_sourceNames[this.screenName.toLowerCase()];
		}
		else {
			nameHtml =  this.screenName;
		}
				
		// link URLs
		if (TB_config.general_link_urls) {
			textHtml = textHtml.replace(/(https?:\/\/\S+)/gi, '<a rel="nofollow" href="$1">$1</a>');
		}
		// link screen names
		if (TB_config.general_link_screen_names) {
			textHtml = textHtml.replace(/\@([\w]+)/gi,'<a rel="nofollow" href="http://twitter.com/$1">@$1</a>'); 
		}
		// link hash tags
		if (TB_config.general_link_hash_tags) {
			textHtml = textHtml.replace(/\#([\w\-]+)/gi,'<a rel="nofollow" href="http://search.twitter.com/search?q=%23$1">#$1</a>'); 
		}
		if (tweetJson.profile_image_url) {
			imageUrl = tweetJson.profile_image_url;
		}
		else {
			imageUrl = tweetJson.user.profile_image_url;
		}
		// make date
		if (TB_config.general_timestamp_format) {
			if (typeof(jQuery.PHPDate) != 'undefined') {
				dateHtml += jQuery.PHPDate(TB_config.general_timestamp_format,this.tweetDate);
			}
			else if (typeof(jQnc.PHPDate) != 'undefined') {
				dateHtml += jQnc.PHPDate(TB_config.general_timestamp_format,this.tweetDate);
			}
		}
		else {
			dateHtml += TB_verbalTime(this.tweetDate);
		} 
		// if source is url encoded -> decode
		if (tweetJson.source.indexOf('&lt;') >= 0) {
			sourceHtml += jQuery('<textarea/>').html(tweetJson.source).val();
		}
		// else use as is
		else {
			sourceHtml += tweetJson.source;
		}

		// return formatted string
		return template.format(this.divId,nameHtml,imageUrl,textHtml,tweetJson.id_str,dateHtml,sourceHtml);
	}
	
	this.isNewerThan = function(TB_tweetId) {
		var tweetIdParts,
		ourTimeStamp,
		otherTweetTimeStamp,
		otherTweetScreenName;
		
		// if other tweet's ID is not defined - assume we are newer
		if (typeof(TB_tweetId) == 'undefined') {
			return true;
		}
		// if it's some weird format - assume we are newer
		else if (TB_tweetId.indexOf('-') <= 0) {
			return true;
		}
		// else, prepare for real comparisons
		else {
			tweetIdParts = TB_tweetId.split('-');
			otherTweetTimeStamp = tweetIdParts[1];
			otherTweetScreenName = tweetIdParts[2];
			ourTimeStamp = this.tweetDate.getTime();
		}

		// if our timestamp is later
		if (ourTimeStamp > otherTweetTimeStamp) {
			return true;
		}
		// if timestamp is older
		else if (ourTimeStamp < otherTweetTimeStamp) {
			return false;
		}
		// if timestamps are same but users are different
		else if (this.screenName != otherTweetScreenName){
			return true;
		}
		// if timestamps are same and users same
		else {
			return false;
		}
	}
	
	this.isOlderThan = function(TB_tweetId) {
		return !this.isNewerThan(TB_tweetId);
	}
	
	/* returns true if this tweet doesn't contain any words that are supposed to be filtered out 
	 * and if it's not supposed to be hidden due to other criteria
	 */
	this.isOKtoShow = function() {

		// if we don't show tweets with same content
		if (TB_config.filter_hide_same_text) {
			if (jQuery.inArray(this.jsonCode.text,TB_seenTweets) > 0) {
				return false;
			}
		}

		// if this is a reply
		if (this.jsonCode.in_reply_to_user_id || this.jsonCode.to_user_id) {
			// if we don't show replies
			if (TB_config.filter_hide_replies) {
				return false;
			}
		}
		// else, if it's not a reply but we are showing only replies
		else if (TB_config.filter_hide_not_replies) {
			return false;
		}

		// if there are filtered words and the tweet text matches any of them - skip this tweet
		if (typeof(TB_config['filter_bad_strings']) != 'undefined' && TB_config.filter_bad_strings.length > 0) {
			badStrings = TB_config.filter_bad_strings.split(',');
			for (i = 0; i < badStrings.length; i++) {
				if (this.jsonCode.text.indexOf(badStrings[i]) >= 0 || this.screenName == badStrings[i]) {
					return false;
				}
			}
		}
		
		// if throttling is ON and we are at max for this user, skip it
		if (TB_config.filter_limit_per_source > 0) {
			if (typeof(TB_sourceCounts[this.screenName]) != 'undefined' || TB_sourceCounts[this.screenName] == 0) {
			
				if (TB_sourceCounts[this.screenName] >= TB_config.filter_limit_per_source) {
					return false;
				}
				else {
					TB_sourceCounts[this.screenName]++;
				}
			}
			else {
				TB_sourceCounts[this.screenName] = 1;
				if (TB_config.widget_limit_per_source_time > 0) {
					setTimeout('TB_sourceCounts["' + this.screenName + '"]=0',TB_config.widget_limit_per_source_time * 1000);
				}
			}
		}
		
		return true;
	}
	
}

// initialize
TB_addLoadEvent(TB_start); jQuery(document).ready(TB_start);