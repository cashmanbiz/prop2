<?php
/**
 * Property Default Template for Single Property View
 *
 * Overwrite by creating your own in the theme directory called either:
 * property.php
 * or add the property type to the end to customize further, example:
 * property-building.php or property-floorplan.php, etc.
 *
 * By default the system will look for file with property type suffix first,
 * if none found, will default to: property.php
 *
 * Copyright 2010 Andy Potanin <andy.potanin@twincitiestech.com>
 *
 * @version 1.4
 * @author Andy Potanin <andy.potnain@twincitiestech.com>
 * @package WP-Property
*/



  if(denali_theme::is_active_sidebar("wpp_sidebar_" . $property['property_type'])) {
    $right_sidebar = true;
  }

  if(get_post_meta($post->ID, 'hide_sidebar', true) != 'true' && $right_sidebar) {
    $have_sidebar = true;
  }
  
  
  	define('WP_USE_THEMES', false);

  	echo "<h1>printer friendly version:</h1>";
    
    setup_postdata($_GET['pid']); 


    ?>
 
<?php  the_post(); ?>
 
<?php get_header(); ?>

<?php get_template_part('attention','single-property'); ?>

  <div id="content" class="inner_content_wrapper property_content <?php echo ($have_sidebar  ? 'have-sidebar' : 'no_columns'); ?>">

  <div id="post-<?php the_ID(); ?>" <?php post_class('main property_page_post'); ?>>
    <div id="container" class="<?php echo (!empty($property['property_type']) ? $property['property_type'] . "_container" : "");?>">
      <div class="property_title_wrapper building_title_wrapper">
        <h1 class="property-title entry-title"><?php the_title(); ?></h1>
        <h3 class="entry-subtitle"><?php the_tagline(); ?></h3>
      </div>

      <div class="entry-content">

        <div class="the_content">
        <?php @the_content(); ?>
        </div>

        <?php if ( empty($wp_properties['property_groups']) || $wp_properties['configuration']['property_overview']['sort_stats_by_groups'] != 'true') : ?>
          <ul id="property_stats" class="overview_stats list">
            <?php @draw_stats("display=list"); ?>
          </ul>
        <?php else: ?>
          <?php @draw_stats("display=list&sort_by_groups=true"); ?>
        <?php endif; ?>

         <?php if(!empty($wp_properties['taxonomies'])) foreach($wp_properties['taxonomies'] as $tax_slug => $tax_data): ?>
          <?php if(get_features("type={$tax_slug}&format=count")):  ?>
          <div class="<?php echo $tax_slug; ?>_list features_list">
          <h2><?php echo $tax_data['label']; ?></h2>
          <ul class="wp_<?php echo $tax_slug; ?>s  wpp_feature_list clearfix">
          <?php get_features("type={$tax_slug}&format=list&links=false"); ?>
          </ul>
          </div>
          <?php endif; ?>
        <?php endforeach; ?>

        <br class="cboth" />

        <?php @draw_stats("display=detail&exclude=tagline&include_clsf=detail&title=false"); ?>

    </div><!-- .entry-content -->

    <?php // get_template_part('content','single-property-map'); ?>

    <?php // get_template_part('content','single-property-inquiry'); ?>

    </div><!-- #container -->

    <?php get_template_part('content','single-property-bottom'); ?>

  </div>

  <?php if ($have_sidebar) : ?>
    <div class="sidebar">
      <ul>
        <?php dynamic_sidebar( "wpp_sidebar_" . $property['property_type'] ); ?>
      </ul>
    </div>
  <?php endif; ?>

 <div class="cboth"></div>

  </div>

<?php get_footer(); ?> 
