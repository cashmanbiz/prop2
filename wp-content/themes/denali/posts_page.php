<?php
/**
 * Template for Posts.
 *
 * Typically used to display the "Blog Portion" of a site when used as CMS
 *
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Denali
*/

  if(denali_theme::is_active_sidebar('posts_page_sidebar')) {
    $posts_page_sidebar = true;
  }

  if($posts_page_sidebar) {
    $have_sidebar = true;
  }

?>

<?php get_header() ?>

<?php get_template_part('attention','blog-home'); ?>

<div id="content" class="inner_content_wrapper <?php echo ($have_sidebar  ? ' have-sidebar' : 'wide-home no_columns'); ?>">

  <div class="posts_page main">

    <?php get_template_part( 'loop', 'blog' ); ?>

    <div class="cboth"></div>

  </div>

	<?php if($have_sidebar): ?>
    <div class="sidebar"><?php dynamic_sidebar( 'posts_page_sidebar' ); ?></div>
	<?php endif; ?>

  <div class="cboth"></div>

</div>

<?php get_footer() ?>
