<?php
/*
Plugin Name: WP Facebook grabber
Plugin URI: http://www.entula.net/wordpress-facebook-grabber/
Description: WP Facebook grabber allows you to grab facebook album and add it to a post or a page.
Author: http://www.borraccetti.it/borraccetti
Version: 3.0
 * Examples and documentation at: http://www.entula.net/wordpress-facebook-grabber/
 * Home: http://www.entula.net/wordpress-facebook-grabber/
 * 
 * Copyright (c) 2010 Fabio Borraccetti
 *
 * Version: v3.0 29-9-2010
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * 
 * Tested and Developer with php 5
 * Requires: php 5 or later
 * 
 *
 */ 
function wpfbAgAddCSS(){
	echo '<link rel="stylesheet" type="text/css" href="'.get_option('siteurl').'/wp-content/plugins/wordpress-facebook-grabber/wp-fb-grabber.css" />';
}
add_action('wp_print_styles', 'wpfbAgAddCSS');

function curl_get_content($url)
{
    $ch = curl_init();

    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_HEADER, 0);

    ob_start();

    curl_exec ($ch);
    curl_close ($ch);
    $string = ob_get_contents();

    ob_end_clean();
   
    return $string;    
}

function mygetRemoteFile($url){
	$fp = fopen( $url, 'r' );
    $content = "";
    while( !feof( $fp ) ) {
       $buffer = trim( fgets( $fp, 4096 ) );
       $content .= $buffer;
    }
	return $content;
}

function fbAlbumGrabber($fburl){
	$maxitem = get_option('max_photo');
	if (function_exists("curl_init")) {
		$content = @curl_get_content($fburl);
	}else{
		$content = @file_get_contents($fburl);
	}
	if($content){
		$data = json_decode($content);
		$retval = "";
		$i=get_the_ID();
		$count=0;
		foreach($data->data as $data->item){
			$retval .= '<div class="fb_thumb" id="fb_thumb_'.$i.'_'.$count.'">
				<a title=" '.str_replace("-"," ",sanitize_title($data->item->name)).'" href="'.htmlentities($data->item->source).'"  rel="lightbox['.$i.']" >
				<img class="wp_fb_album_grabber_img" src="'.htmlentities($data->item->picture).'" alt=" '.str_replace("-"," ",sanitize_title($data->item->name)).'" title=" '.str_replace("-"," ",sanitize_title($data->item->name)).' " />
				</a>
			</div>';
			if($count>$maxitem-2)
				break;
			$count++;
		}
		return $retval;
	}else{
		//					$handle,      $src,                                         $deps,          $ver, $in_footer 
		//wp_enqueue_script('newscript',  WP_PLUGIN_URL . '/wp-fb-grabber/wp-fb-grabber.js' );
		$retval .= '<script src="'.  WP_PLUGIN_URL . '/wordpress-facebook-grabber/wp-fb-grabber.js"></script> ';
		$retval .= '<div id="wp-fb-grabber">';
		$retval .= '</div>';
		$retval .= '<script type="text/javascript">
			$("#wp-fb-grabber").grabFBalbum({ 
				fburl: "'.$fburl.'" 
				maxitem: '.get_option("max_photo").'
			});
		</script>';
		return $retval;
	}
}
function fbFeedGrabber($fburl){
	$maxitem = get_option('max_feed');
	if (function_exists("curl_init")) {
		$content = @curl_get_content($fburl);
	}else{
		$content = @file_get_contents($fburl);
	}
	if($content){
		$data = json_decode($content);
		$retval = "";
		$i=get_the_ID();
		$count=0;
		foreach($data->data as $data->item){
		if($count != -1) {
			$id = $data->item->from;
			$retval .= '<div class="fb_feed" id="fb_feed_'.$i.'_'.$count.'" >
			<a href="'.str_replace("graph","www",$data->item->link).'"  target="_blank" >
			<span class="fb_from">'.$id->name.'</span>
			<p class="fb_feed_title">'.str_replace("-"," ",sanitize_title($data->item->name)).'</p>
				';
				if($data->item->picture)
				$retval .='<div class="fb_feed_thumb"><img class="wp_fb_album_grabber_img" src="'.htmlentities($data->item->picture).'" alt=" '.str_replace("-"," ",sanitize_title($data->item->name)).'" title=" '.str_replace("-"," ",sanitize_title($data->item->name)).' " /></div>';
				$desc=$data->item->message?$data->item->message:$data->item->description;
				$retval .='	
				</a>
				<div class="fb_feed_desc">
				<p>
					'.$desc.'
				</p>
				</div>
			</div>
			';
		}
			if($count>$maxitem-2)
				break;
			$count++;
		}
		return $retval;
	}else{
		//					$handle,      $src,                                         $deps,          $ver, $in_footer 
		//wp_enqueue_script('newscript',  WP_PLUGIN_URL . '/wp-fb-grabber/wp-fb-grabber.js' );
		$retval .= '<script src="'.  WP_PLUGIN_URL . '/wordpress-facebook-grabber/wp-fb-grabber.js"></script> ';
		$retval .= '<div id="wp-fb-grabber">';
		$retval .= '</div>';
		$retval .= '<script type="text/javascript">
			$("#wp-fb-grabber").grabFBfeeed({ 
				fburl: "'.$fburl.'" 
				maxitem: '.get_option("max_feed").'
			});
		</script>';
		return $retval;
	}
	
}
//https://graph.facebook.com/126319364057939?metadata=1
/*
function wpfbGrabber($text) {
 
    $album_tag_pattern = '/(\[fbAlbum\](.*?)\[\/fbAlbum\])/i'; 
	$feed_tag_pattern = '/(\[fbFeed\](.*?)\[\/fbFeed\])/i'; 
    # Check for in-post [tag] [/tag]
	
    if (preg_match_all ($feed_tag_pattern, $text, $matches)) {
        unset($toprint);
		$toprint = fbFeedGrabber("http://graph.facebook.com/".$matches[2][0]."/feed");
		$tag_pattern = $feed_tag_pattern;
    }else if(preg_match_all ($album_tag_pattern, $text, $matches)) {
        unset($toprint);
		$toprint = fbAlbumGrabber("http://graph.facebook.com/".$matches[2][0]."/photos");
		$tag_pattern = $album_tag_pattern;
    }
	
	//$toprint .= $post_replacement;
	$pre_replacement = '<div class="wp_fb_grabber">';
	$post_replacement = '</div>';
    if (isset($toprint)) { 
            $text = preg_replace($tag_pattern, $pre_replacement.$toprint.$post_replacement ,$text); 
        }
    return $text;
}*/

//bugfix thanks to Carlo Alberto Ferraris http://cafxx.strayorange.com/ - cafxx@strayorange.com
function wpfbReplaceCallback($match) {
    if ( strtolower( $match[1] ) == "album" )
        $toprint = fbAlbumGrabber("http://graph.facebook.com/$match[2]/photos");
    else
        $toprint = fbFeedGrabber("http://graph.facebook.com/$match[2]/feed");
    return $toprint ? "<div class=\"wp_fb_grabber\">$toprint</div>" : "";
}

function wpfbGrabber($text) {
    return preg_replace_callback('/\[fb(Album|Feed)\](.*?)\[\/fb\1\]/i', 'wpfbReplaceCallback', $text);
}

add_filter('the_content', 'wpfbGrabber');
include("option_panel.php");
?>
