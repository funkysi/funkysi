<?php
/**
 * @package WordPress
 * @subpackage  Cute_Bubbles
 */

get_header(); ?>

<div id="theREALleft">

<?php if (have_posts()) : ?> 

<?php while (have_posts()) : the_post(); ?> 
<div class="titleAndMeta">
    <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
    <div class="metaDate">Posted <?php the_time('l, F jS, Y') ?></div>
    <div class="metaCommentsEdit"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></div>
</div>
<div class="realPostItem">
<?php the_content('Read the rest of this entry &raquo;'); ?>
</div>

<?php endwhile; ?> 

        <div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>
		

<?php else : ?>

        <h2 class="center">Not Found</h2>
        <div class="realPostItem">Sorry, but you are looking for something that isn't here.</div> 
        <?php get_search_form(); ?>

<?php endif; ?>



</div>  



<?php get_sidebar(); ?>

<?php get_footer(); ?>
