<?php
/**
 * @package WordPress
 * @subpackage Cute_Bubbles
 */
?>
<ul id="theREALright">

  <?php     
  /* Widgetized sidebar, if you have the plugin installed. */
    if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
                    

  <?php wp_list_pages('title_li=<h5>Pages</h5>'); ?>

  <?php wp_list_categories('show_count=0&title_li=<h5>Categories</h5>'); ?>
    

  <?php wp_list_bookmarks(); ?>
   
   <li class="pagenav">
   <h5>Archive</h5>
   <ul>
       <?php wp_get_archives('type=monthly'); ?>
   </ul>
   </li>
    
    <?php endif; ?>

</ul>

