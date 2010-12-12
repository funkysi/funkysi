<?php
/**
 * @package WordPress
 * @subpackage Cute_Bubbles
 */

get_header(); ?>

<div id="theREALleft">

 <?php if (have_posts()) : while (have_posts()) : the_post(); ?> 
    
    <div class="titleAndMeta">
        <h2><?php the_title(); ?></h2>
        <div class="metaDate">Posted <?php the_time('l, F jS, Y') ?></div>
        <div class="metaCommentsEdit"><?php the_tags('Tags: ', ', ', '<br />'); ?>  <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></div>
    </div>
    <div class="realPostItem">
    <?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>  
    
    </div>
    
<?php endwhile; endif; ?>     
    
</div>
            

<?php get_sidebar(); ?>

<?php get_footer(); ?>
