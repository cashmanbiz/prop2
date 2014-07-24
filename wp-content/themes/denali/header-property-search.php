<?php
/**
 * Header - Property Search Dropdown
 *
 * This can be overridden in child themes using get_template_part()
 *
 * @package Denali
 * @since Denali 3.0
 *
 */

  global $ds;

  //** Do not display this section if disabled in theme support */
  if(!current_theme_supports('header-property-search') || $denali_theme_settings['hide_header_property_search'] == 'true') { return; }

  if(!is_active_sidebar('global_property_search')) { return; }

  $label = (!empty($ds['property_search_label']) ? $ds['property_search_label'] : __( 'Property Search', 'denali' ) );

  $function = create_function('$c', '
    $c["property_search"]["id"] = "dropdown_header_search";
    $c["property_search"]["title"] = "'. addslashes( $label ) .'";
    $c["property_search"]["class"] = "find_top dropdown_tab_find_property";
    $c["property_search"]["href"] = "#";
    return $c;
  ');

  add_filter('denali_header_links', $function, 10, 1);

 ?>

  <div id="dropdown_header_search" class="header_dropdown_div">
    <?php dynamic_sidebar( 'global_property_search' ); ?>
    <div class="cboth"></div>
  </div>
