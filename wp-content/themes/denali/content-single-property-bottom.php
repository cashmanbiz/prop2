<?php
/**
 * Content - Single Property Map. 
 *
 * Displays the attenion grabbing element on the homage page.
 *
 * This can be overridden in child themes using get_template_part()
 *
 * @package Denali
 * @since Denali 3.0
 *
 */ 
 
  $this_widget_area = 'wpp_foooter_' . $property['property_type'];
 
  if($tabs = denali_theme::widget_area_tabs($this_widget_area)) { ?>

    <div class="denali_widget_area_tabs wpp_property_bottom_area <?php echo (count($tabs) < 2 ? 'no_tabs' : 'have_tabs'); ?>">
    
    <?php if(count($tabs) > 1) { ?>
      <ul class="attention_grabber_tabs denali_widget_tabs">
      <?php foreach($tabs as $widget) { ?>
          <li class="denali_tab"><a href="#<?php echo $widget['id'];?>" class="denali_tab_link"><?php echo $widget['title']; ?></a></li>
      <?php } ?>
      </ul>
    <?php } ?>
      
    <?php dynamic_sidebar($this_widget_area); ?>

    </div>
    
  <?php } ?>
    
   
  <?php if ( is_active_sidebar( "denali_property_footer") ) : ?>
    <div class="content_horizontal_widget widget_area clearfix">
    <?php dynamic_sidebar( "denali_property_footer"); ?>
    </div>
  <?php endif; ?>