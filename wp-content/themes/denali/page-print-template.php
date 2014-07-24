<?php
/**
 * Template Name: Print Page
 *
 */
?>

<?php get_header('print'); ?>

<?php get_template_part('attention', 'print-page'); ?>

  <div class="inner_content_wrapper no_columns">

  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>" <?php post_class('main-no-sidebar main'); ?>>
      <h1 class="entry-title"><?php the_title();?></h1>
      <div class="entry-content">
      <?php the_content('More Info'); ?>
      </div>
    </div>
  <?php endwhile; endif; ?>
  <div class="cboth"></div>

  </div>

<?php get_footer('print') ?>