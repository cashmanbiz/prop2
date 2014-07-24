<?php
/**
 * The default page for property overview page.
 *
 * Used when no WordPress page is setup to display overview via shortcode.
 * Will be rendered as a 404 not-found, but still can display properties.
 *
 * @package WP-Property
 */

  if(denali_theme::is_active_sidebar('property_overview_sidebar')) {
    $property_overview_sidebar = true;
  }

  if(denali_theme::is_active_sidebar('right_sidebar')) {
    $right_sidebar = true;
  }

  if($property_overview_sidebar || $right_sidebar) {
    $have_sidebar = true;
  }

 ?>

<?php get_header(); ?>

<?php get_template_part('attention', 'property-overview'); ?>

  <div id="content" class="inner_content_wrapper <?php echo ($have_sidebar  ? 'have-sidebar' : 'no_columns'); ?>">

    <div id="post-property-overview" class="main page type-page hentry main">
      <h1 class="entry-title"><?php echo $post->post_title; ?></h1>
      <div class="entry-content">

          <?php if(is_404()): ?>
            <p><?php _e('Sorry, we could not find what you were looking for.  Since you are here, take a look at some of our properties.','denali') ?></p>
          <?php endif; ?>

          <?php if($wp_properties['configuration']['do_not_override_search_result_page'] == 'true'): ?>
            <?php echo $content = apply_filters('the_content', $post->post_content);  ?>
          <?php endif; ?>

          <?php echo WPP_Core::shortcode_property_overview(); ?>

      </div>
    </div>


  <?php if ($have_sidebar) : ?>
    <div class="sidebar">
        <?php dynamic_sidebar( 'property_overview_sidebar' ); ?>
        <?php dynamic_sidebar( 'right_sidebar' ); ?>
    </div>
  <?php endif; ?>

  <div class="cboth"></div>

  </div>

<?php get_footer() ?>