<?php
add_action('admin_menu', 'my_plugin_menu');
//call register settings function
add_action( 'admin_init', 'register_mysettings' );
function register_mysettings(){
	register_setting( 'wordpress-facebook-grabber', 'max_feed' ); 
	register_setting( 'wordpress-facebook-grabber', 'max_photo' ); 
}


function my_plugin_menu() {

  add_options_page('WP Facebook Grabber Options', 'WP Facebook Grabber', 'manage_options', 'wordpress-facebook-grabber', 'my_plugin_options');

}

function my_plugin_options() {

  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }
/*
<div id="profileimage" class="profileimage">
<a href="/album.php?profile=1&amp;id=35346524498">
<img class="logo img" src="http://profile.ak.fbcdn.net/profile-ak-snc4/object3/595/114/n35346524498_7434.jpg" alt="Greenpeace Italia" id="profile_pic">
</a>
</div>
*/
 
echo '<link rel="stylesheet" type="text/css" href="'.get_option('siteurl').'/wp-content/plugins/wordpress-facebook-grabber/wp-fb-grabber.css" />';
?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="<?=WP_PLUGIN_URL?>/wordpress-facebook-grabber/wp-fb-grabber.js"></script> 
<div class="wrap">
<h2>WP Facebook Grabber Options</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'wordpress-facebook-grabber' ); ?>
	<script type="text/javascript">//<![CDATA[
	function get_facebook_id(URL){
		//$("#grabres").html(url);
		var fburl = "http://graph.facebook.com/"+unescape(URL.substring((URL.lastIndexOf("/")) + 1))
		$.ajax({
			url: fburl,
			dataType: "jsonp",
			cache: true,
			error: function(e){
				$("#facebook_id").val("Error loading document".e);
			},
			success: function(data) {
				//cycle eash item and added to target object.
				$("#facebook_id").val(data.id);
				$("#tag_demo").html("<p>Use this tag in your post or pages to get facebook feed: [fbFeed]"+data.id+"[/fbFeed]</p>");
				$("#tag_demo").append("<p>Use this tag in your post or pages to get facebook profile Album: [fbAlbum]"+data.id+"[/fbAlbum]</p>");
			}
		});
		$("#wp-fb-grabber").grabFBfeed({ 
			fburl:  fburl+"/feed",
			maxitem: <?php echo get_option('max_feed'); ?>
		});
	}
	//]]></script>


    <table class="form-table">
        <tr valign="top">
        <th scope="row">Get Your Facebook ID</th>
        <td>
			<input type="text" name="url" id="url" size="100" value="http://www.facebook.com/GreenpeaceItalia"  /><span onclick="javascript:get_facebook_id($('#url').val())">get it!</span>
			<input type="text" name="facebook_id" id="facebook_id" value=""  />
			<div id="tag_demo"></div>
		</td>
        </tr>

        <tr valign="top">
        <th scope="row">Maximum number of facebook feed to print</th>
        <td><input type="text" name="max_feed" value="<?php echo get_option('max_feed'); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Maximum number of facebook album photos to print</th>
        <td><input type="text" name="max_photo" value="<?php echo get_option('max_photo'); ?>" /></td>
        </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
	<div id="wp-fb-grabber"></div>
</div>
<?php } ?>