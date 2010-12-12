<?php
/**
 * @package WordPress
 * @subpackage Cute_Bubbles
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>

<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" media="screen" />  

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

<?php wp_head(); ?>

<!--[if IE 6]>
<script src="<?php echo get_option('home'); ?>/wp-content/themes/cute_bubbles/js/DD_belatedPNG_0.0.7a-min.js"></script>
<script>
  /* EXAMPLE */
  DD_belatedPNG.fix('#pageContainerTOP');
  DD_belatedPNG.fix('#searchSheat input');
  DD_belatedPNG.fix('#pageContainerCENTER');
  DD_belatedPNG.fix('.realPostItem');
  DD_belatedPNG.fix('.postmetadata');
  DD_belatedPNG.fix('#pageContainerBOTTOM');

</script>
<![endif]--> 

</head>
<body>

<div id="top_background"></div>
<div id="container">
    <div id="pageContainer">
        
        <div id="headerContent">
        <h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
        <h5><?php bloginfo('description'); ?></h5>
        
        <div id="searchSheat"><form action="" method="post"><input type="text" name="s" value="Search Me" maxlength="20" /></form></div>
        
        </div>
    
        <div id="pageContainerTOP"></div>
        
        <div id="pageContainerCENTER">
        
        <div id="pageStyler"> 
