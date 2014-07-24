<?php
/**
 * WP-Property Overview Grid Template
 *
 * To customize this file, copy it into your theme directory, and the plugin will
 * automatically load your version.
 *
 * You can also customize it based on property type.  For example, to create a custom
 * overview page for 'building' property type, create a file called property-overview-building.php
 * into your theme directory.
 *
 * @version 1.4
 * @author Andy Potanin <andy.potnain@twincitiestech.com>
 * @package WP-Property
*/
global $ds;
?>

<?php if ( have_properties() ) { ?>

  <script type="text/javascript"><?php do_action('wpp_js_on_property_overview_display', 'grid'); ?> </script>

  <div class="wpp_grid_view all-properties wpp_property_view_result clearfix">

  <?php foreach ( returned_properties('load_gallery=false') as $property) {  ?>

    <div class="property_div <?php echo $property['post_type']; ?> <?php echo $property['property_type']; ?> clearfix">

      <div class="wpp_overview_left_column">
        <?php property_overview_image("image_type={$thumbnail_size}"); ?>
      </div>

      <ul class="wpp_overview_data" style="">
        <li class="property_title">
          <a <?php echo $in_new_window; ?> href="<?php echo $property['permalink']; ?>"><?php echo $property['post_title']; ?></a>
          <?php if($property['is_child']): ?>
          of <a <?php echo $in_new_window; ?> href='<?php echo $property['parent_link']; ?>'><?php echo $property['parent_title']; ?></a>
          <?php endif; ?>
        </li>
        <?php if($ds['grid_property_overview_attributes']['stats']) : ?>
        <?php draw_stats( 'include=' . implode(',', $ds['grid_property_overview_attributes']['stats']) . '&display='.(version_compare("2.0",WPP_Version,"<=")?"div_strong_span":"list"), $property ); ?>
        <?php endif; ?>
        <?php if(!empty($wpp_query['detail_button'])) : ?>
        <li class="detail_button"><a style="margin:6px auto 0 auto;" class="denali-button" href="<?php echo $property['permalink']; ?>"><?php echo $wpp_query['detail_button'] ?></a></li>
        <?php endif; ?>

      </ul>

    </div><?php // .property_div ?>
  <?php } ?>
  </div><?php // .wpp_grid_view ?>
<?php } else { ?>
  <div class="wpp_nothing_found">
    <?php echo sprintf(__('Sorry, no properties found - try expanding your search, or <a href="%s">view all</a>.','denali'), site_url().'/'.$wp_properties['configuration']['base_slug']); ?>
  </div>
<?php } ?>
