<?php

  if(is_property_overview_page() && denali_theme::is_active_sidebar('property_overview_sidebar')) {
    $property_overview_sidebar = true;
  }    
 
  if(denali_theme::is_active_sidebar('right_sidebar')) {
    $right_sidebar = true;
  }  
     
  if(get_post_meta($post->ID, 'hide_sidebar', true) != 'true' && ($property_overview_sidebar || $right_sidebar)) {
    $have_sidebar = true;
  }

?>

<?php get_header(); ?>

<?php get_template_part('attention', 'page'); ?>

  <div id="content" class="inner_content_wrapper <?php echo ($have_sidebar  ? 'have-sidebar' : 'no_columns'); ?>">
  
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
          <div id="post-<?php the_ID(); ?>" <?php post_class('main'); ?>>

              <?php if(!hide_page_title()) { ?>
              <h1 class="entry-title"><?php the_title();?></h1>
              <?php } ?>

              <div class="entry-content">
                  <?php the_content('More Info'); ?>
                  <?php comments_template(); ?>
              </div>
              <?php do_action('denali_page_below_entry_content'); ?>
          </div>
      <?php endwhile; endif; ?>

  <?php if ($have_sidebar) : ?>
    <div class="sidebar">
        <?php dynamic_sidebar( 'property_overview_sidebar' ); ?>
        <?php dynamic_sidebar( 'right_sidebar' ); ?>
    </div>
  <?php endif; ?>

  <div class="cboth"></div>

  </div>

<?php get_footer() ?>