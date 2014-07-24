<?php
/**
 * Content - Home Bottom
 *
 * Displays the bottom of page element on the home page.
 *
 * This can be overridden in child themes using get_template_part()
 *
 * @package Denali
 * @since Denali 3.0
 *
 */ 
 
 ?>
 
<div class="content_horizontal_widget widget_area clearfix">
  <?php dynamic_sidebar( 'home_bottom_sidebar' ); ?>
</div>