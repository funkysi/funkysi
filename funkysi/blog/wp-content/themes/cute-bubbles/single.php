<?php
/**
 * @package WordPress
 * @subpackage Cute_Bubbles
 */

get_header();
?>

<div id="theREALleft">

 <?php if (have_posts()) : while (have_posts()) : the_post(); ?> 
 
    <div class="navigation">
        <div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
        <div class="alignright"><?php next_post_link('%link &raquo;') ?> </div>
    </div>
       
    <div class="titleAndMeta">
        <h2><?php the_title(); ?></h2>
        <div class="metaDate">Posted <?php the_time('l, F jS, Y') ?></div>
        <div class="metaCommentsEdit"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></div>
    </div>
    
    <div class="realPostItem">
    <?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
    </div>
    
    <div class="postmetadata_single">
            You can follow any responses to this entry through the <?php post_comments_feed_link('RSS 2.0'); ?> feed.

            <?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
                // Both Comments and Pings are open ?>
                You can <a href="#respond">leave a response</a>, or <a href="<?php trackback_url(); ?>" rel="trackback">trackback</a> from your own site.

            <?php } elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
                // Only Pings are Open ?>
                Responses are currently closed, but you can <a href="<?php trackback_url(); ?> " rel="trackback">trackback</a> from your own site.

            <?php } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
                // Comments are open, Pings are not ?>
                You can skip to the end and leave a response. Pinging is currently not allowed.

            <?php } elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
                // Neither Comments, nor Pings are open ?>
                Both comments and pings are currently closed. 

            <?php } edit_post_link('Edit this entry','','.'); ?>
    
        <div class="entry">
            <?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
        </div>
<div>   <a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical" data-via="funkysi1701">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>
    </div> 
 
  
    <?php comments_template(); ?>

    <?php endwhile; else: ?>

        <p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>   
</div>
 
<?php get_sidebar(); ?> 

<?php get_footer(); ?>
