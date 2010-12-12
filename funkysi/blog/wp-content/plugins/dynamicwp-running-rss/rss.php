<?php

/*
Plugin Name: DynamicWP Running Rss
Plugin URI: http://www.dynamicwp.net/plugins/free-plugin-dynamicwp-running-rss/
Description: The plugin displays RSS feeds from other sites on your own, as a running text in the bottom of your site. Based on <a href="http://www.dynamicdrive.com/dynamicindex18/gajaxrssdisplayer.htm">dynamicdrive</a> gAjax Feeds Displayer.
Author: Reza Erauansyah
Version: 1.0
Author URI: http://www.dynamicwp.net
*/

if (!class_exists("DynamicwpAjaxRss")) {
	class DynamicwpAjaxRss {
		var $adminOptionsName = "DynamicwpAjaxRssAdminOptions";
		function DynamicwpAjaxRss() { //constructor
			
		}
		function init() {
			$this->getAdminOptions();
		}
		//Returns an array of admin options
		function getAdminOptions() {
			$rssAdminOptions = array(
				'feedurl' => '',
				'apikey' => '',
				'rssnumber' => '',
				'scrollspeed' =>'',
				'backgroundcolor' => '',
				'title' => '',
				'textcolor' => '',
				'rsstitlecolor' => '',
				'datecolor' => ''
				);
			$rssOptions = get_option($this->adminOptionsName);
			if (!empty($rssOptions)) {
				foreach ($rssOptions as $key => $option)
					$rssAdminOptions[$key] = $option;
			}				
			update_option($this->adminOptionsName, $rssAdminOptions);
			return $rssAdminOptions;
		}
		

		function myrssstyle(){
			$rssOptions = $this->getAdminOptions();

			$backgroundcolor = ($rssOptions['backgroundcolor']) ? $rssOptions['backgroundcolor'] : '#E6E6E6';
			$textcolor = ($rssOptions['textcolor']) ? $rssOptions['textcolor'] : '#333';
			$rsstitlecolor = ($rssOptions['rsstitlecolor']) ? $rssOptions['rsstitlecolor'] : '#486CB8' ;
			$datecolor = ($rssOptions['datecolor']) ? $rssOptions['datecolor'] : '#777' ;
			
		?>
			<style type="text/css">
				/* liScroll styles */
				.dwp-rss-wrapper { width: 100%; border-top: 1px solid #AAA;height: 30px;position: fixed; bottom: 0; left: 0; background: <?php echo $backgroundcolor; ?> ; z-index: 1000;}
				.rss-title {position: absolute;top: 6px; left: 0; z-index: 1000; padding: 2px 10px; background: <?php echo $backgroundcolor; ?>;}
				.rss-title a {color: <?php echo $textcolor; ?>;}
				#ticker-wrapper {width: 100%; margin-top: 8px; height: 30px; color: #000; font-weight: bold;}
				#ticker-wrapper span{ margin-right: 7px;}
				#ticker-wrapper span.snippetfield{margin-right: 50px; color: <?php echo $textcolor; ?>; float: left;}
				.titlefield {color: <?php echo $rsstitlecolor; ?>;float: left; margin-right: 7px;}
				.datefield {color : <?php echo $datecolor; ?>; font-weight: normal;float: left;}
				.labelfield{color: #999; font-weight: normal;float: left;}
				@media screen and (-webkit-min-device-pixel-ratio:0) {
					.snippetfield{display: none;}
					.labelfield{display: none;}
					#ticker-wrapper span.datefield {margin-right: 50px;}
				}				
				
				body {padding-bottom: 30px;}
			</style>	
			<!--[if IE 7]>
				<style>
				.snippetfield{display: none;}
				.labelfield{display: none;}
				#ticker-wrapper span.datefield {margin-right: 50px;}
				</style>
			<![endif]-->
			
		<?php	}

		//Add rss script
		function myrssscript(){
			$rssOptions = $this->getAdminOptions();

			$feedurl = ($rssOptions['feedurl'])? $rssOptions['feedurl'] : 'http://newsrss.bbc.co.uk/rss/newsonline_uk_edition/front_page/rss.xml,BBC; http://rss.news.yahoo.com/rss/topstories,Yahoo News  ';
			$apikey = ($rssOptions['apikey']) ? $rssOptions['apikey'] : 'ABQIAAAAuQkKGVAinqAFcQ_5QO0VDxT2yXp_ZAY8_ufC3CFXhHIE1NvwkxSdEw74ZIapdTwzjvAsFNhbfTYljg';
			$rssnumber = ($rssOptions['rssnumber']) ? $rssOptions['rssnumber'] : 6;
			$backgroundcolor = $rssOptions['backgroundcolor'];
			$title = ($rssOptions['title'])  ? $rssOptions['title'] : 'Breaking News';
			$textcolor = $rssOptions['textcolor'];
			$rsstitlecolor = $rssOptions['rsstitlecolor'];
			$datecolor = $rssOptions['datecolor'];
			$scrollspeed  = ($rssOptions['scrollspeed']) ? $rssOptions['scrollspeed'] : 6;
 
			$linkss = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));


?>
			<div class="dwp-rss-wrapper">
				
				<marquee behavior="scroll" align="left" direction="left" height="27" scrollamount="<?php echo $scrollspeed; ?>" width="100%" onMouseOver="this.stop()" onMouseOut="this.start()" loop="infinite">
					<?php 
						echo '<script type="text/javascript" src="http://www.google.com/jsapi?key='.$apikey.'"></script>';
						echo "<script type=\"text/javascript\" charset=\"utf-8\" src=\"".$linkss."gfeedfetcher.js\"></script>";
						echo '

							<script type="text/javascript">

							var newsfeed=new gfeedfetcher("ticker-wrapper", "ticker-wrapper", "_new")';
							
							
						echo "\n";

							 $values = explode(";", $feedurl);
							 if(is_array($values)) {
								foreach ($values as $item) {
									$rssfeed = explode(',', $item);
									$rsslabel = trim($rssfeed['1']);
									$rssurl = trim($rssfeed['0']);
									if(!empty($rsslabel) && !empty($rssurl)) {
										echo "newsfeed.addFeed(\"$rsslabel\", \"$rssurl\") \n";
									}
								 }
							 }
						echo'
							newsfeed.filterfeed('.$rssnumber.', "date") //Show 8 entries, sort by date
							newsfeed.displayoptions("label datetime snippet")
							newsfeed.setentrycontainer("span")
							newsfeed.init() //Always call this last
							
							</script>

							';
					?>
				</marquee>
				<span class="rss-title"><b><a href="http://www.dynamicwp.net/" title="Plugin By DynamicWP.net" ><?php echo  $title ?></a> : </b></span>
			</div>
		<?php
		}

		//Prints out the admin page
		function printAdminPage() {
					$rssOptions = $this->getAdminOptions();
										
					if (isset($_POST['update_DynamicwpAjaxRssSettings'])) { 
						if (isset($_POST['feedurl'])) {
							$rssOptions['feedurl'] = $_POST['feedurl'];
						}
						if (isset($_POST['apikey'])) {
							$rssOptions['apikey'] = $_POST['apikey'];
						}
						if (isset($_POST['rssnumber'])) {
							$rssOptions['rssnumber'] = $_POST['rssnumber'];
						}
						if (isset($_POST['backgroundcolor'])) {
							$rssOptions['backgroundcolor'] = $_POST['backgroundcolor'];
						}
						if (isset($_POST['title'])) {
							$rssOptions['title'] = $_POST['title'];
						}
						if (isset($_POST['textcolor'])) {
							$rssOptions['textcolor'] = $_POST['textcolor'];
						}
						if (isset($_POST['rsstitlecolor'])) {
							$rssOptions['rsstitlecolor'] = $_POST['rsstitlecolor'];
						}
						if (isset($_POST['datecolor'])) {
							$rssOptions['datecolor'] = $_POST['datecolor'];
						}			
						if (isset($_POST['scrollspeed'])) {
							$rssOptions['scrollspeed'] = $_POST['scrollspeed'];
						}
						
						update_option($this->adminOptionsName, $rssOptions);
						
						?>
						
						<div class="updated"><p><strong><?php _e("Settings Updated.", "DynamicwpAjaxRss");?></strong></p></div>
						
						<?php } ?>
						<div class="wrap">
							<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
							<h2><a href="http://www.dynamicwp.net">DynamicWP</a> Running Rss</h2>
							
							<div style="width: 100%; ">
								<b><label for="dwpfeedurl">URL to rss feed : </label></b><br />
								<textarea id="dwpfeedurl" name="feedurl" style="width: 75%;" rows="5" cols="5" ><?php echo $rssOptions['feedurl'];?></textarea><br />
								<small>enter url to rss feed and its label, separated by comma. Ex : http://newsrss.bbc.co.uk/rss/newsonline_uk_edition/front_page/rss.xml, BBC. if you want to add more rss feed url, separeted it with ";". <br /> Ex: http://newsrss.bbc.co.uk/rss/newsonline_uk_edition/front_page/rss.xml, BBC; http://rss.news.yahoo.com/rss/topstories, Yahoo News</small>

								<br />
								<br />

								<b><label for="dwpapikey">Google API Key : </label></b><br />
								<input type="text" id="dwpapikey" name="apikey" value="<?php echo $rssOptions['apikey'];?>" style="width: 25%; " /><br />
								<small>You can get your api key here: http://code.google.com/apis/ajaxfeeds/signup.html </small>
				
								<br />
								<br />

								<b><label for="dwprssnumber">RSS entries to show: </label></b><br />
								<input type="text" id="dwprssnumber" name="rssnumber" value="<?php echo $rssOptions['rssnumber'];?>" style="width: 25%; " /><br />
								<small>Set the number of total RSS entries to show. Default number is 6</small>
								
								<br />
								<br />
								
								<b><label for="dwprssscrollspeed">Scroll Speed : </label></b><br />
								<input type="text" id="dwprssscrollspeed" name="scrollspeed" value="<?php echo $rssOptions['scrollspeed'];?>" style="width: 25%;" /><br />
								<small>Set the scroll speed. Default is 6. </small>
								
								<br />
								<br />
								
								<b><label for="dwptitle">Running text title : </label></b><br />
								<input type="text" id="dwptitle" name="title" value="<?php echo $rssOptions['title'];?>" style="width: 25%;" /><br />
								<small>Enter title for you running text. Default title is 'Breaking News'</small>

								<br />
								<br />
					
								<b><label for="dwpbackgroundcolor">Background Color : </label></b><br />
								<input type="text" name="backgroundcolor" id="dwpbackgroundcolor" value="<?php echo $rssOptions['backgroundcolor'];?>" style="width: 25%;" /><br />
								<small>Set the background color. Default color: #E6E6E6 </small>

								<br />
								<br />

						
								<b><label for="dwptextcolor">Text Color : </label></b><br />
								<input type="text" id="dwptextcolor" name="textcolor" value="<?php echo $rssOptions['textcolor'];?>" style="width: 25%;" /><br />
								<small>Specify color of rss feed text. Default: #333</small>
								<br />

								<br />
								<br />
								
								<b><label for="dwprsstitlecolor">Rss Feed Title Color : </label></b><br />
								<input type="text" id="dwprsstitlecolor" name="rsstitlecolor" value="<?php echo $rssOptions['rsstitlecolor'];?>" style="width: 25%;" /><br />
								<small>Set the color of rss feed title. Default :#486CB8 </small>
								
								<br />
								<br />
								
								<b><label for="dwpdatecolor">Date Color : </label></b><br />
								<input type="text" id="dwpdatecolor" name="datecolor" value="<?php echo $rssOptions['datecolor'];?>" style="width: 25%;" /><br />
								<small>Specify the date color. Default: #777</small>
								
								
								<br />

							</div>

							<div style="clear:both;"></div>
							
							<div class="submit">
								<input type="submit" name="update_DynamicwpAjaxRssSettings" value="<?php _e('Update Settings', 'DynamicwpAjaxRss') ?>" />	
							</div>
							</form>
						</div>
					<?php
				}//End function printAdminPage()
	
	}

} //End Class DynamicwpAjaxRss

if (class_exists("DynamicwpAjaxRss")) {
	$rss_plugin = new DynamicwpAjaxRss();
}

//Initialize the admin panel
if (!function_exists("DynamicwpAjaxRss_ap")) {
	function DynamicwpAjaxRss_ap() {
		global $rss_plugin;
		if (!isset($rss_plugin)) {
			return;
		}
		if (function_exists('add_options_page')) {
	add_options_page('<b style="color: #C50606;">DynamicWP Running Rss</b>', '<b style="color: #C50606;">DynamicWP Running Rss</b>', 9, basename(__FILE__), array(&$rss_plugin, 'printAdminPage'));
		}
	}	
}

//Actions and Filters	
if (isset($rss_plugin)) {
	//Actions
	add_action('admin_menu', 'DynamicwpAjaxRss_ap');
	add_action('activate_rss/rss.php',  array(&$rss_plugin, 'init'));
	
	if(!is_admin()){
	add_action('wp_footer', array(&$rss_plugin, 'myrssscript'));
	add_action('wp_head', array(&$rss_plugin, 'myrssstyle'));
	}
}


?>
