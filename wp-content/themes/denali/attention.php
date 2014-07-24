<?php
/**
 * Attention General displays the attenion grabbing element on all standard pages.
 *
 *
 * This can be overridden in child themes with loop.php or
 * attention-template.php, where 'template' is the context
 * requested by a template. For example, attention-blog-home.php would
 * be used if it exists and we ask for the attention with:
 * <code>get_template_part( 'attention', 'blog-home' );</code>
 *
 * @package Denali
 * @since Denali 3.0
 *
 */ 

  if(get_post_meta($post->ID, 'hide_header', true) == 'true' || !current_theme_supports('inner_page_slideshow_area')) {
    denali_theme::console_log('AG: Header not rendered, disabled in post settings or via theme_supports()');
    return;
  }
   
  $this_widget_area = 'inside_attention_grabber';
 
  if(!$tabs = denali_theme::widget_area_tabs($this_widget_area)) {
    denali_header_image();
    return;    
  }
  
  if(count($tabs) > 1) {
    denali_theme::console_log('AG: Rendering Tabbed attention grabber.');
    $multi_tab = true;
  } else {
    denali_theme::console_log('AG: Rendering Untabbed attention grabber.');  
  }  
  
  ?>
  
  <div class="sld-flexible denali_attention_grabber_area">
    <div class='sld-top'></div>
    <div class="denali_widget_area_tabs wpp_property_header_area <?php echo ($multi_tab ? 'have_tabs' : 'no_tabs'); ?>">
    
    <?php if($multi_tab) { ?>
      <ul class="attention_grabber_tabs denali_widget_tabs">
      <?php foreach($tabs as $widget) { ?>
          <li class="denali_tab"><a href="#<?php echo $widget['id'];?>" class="denali_tab_link"><?php echo $widget['title']; ?></a></li>
      <?php } ?>
      </ul>
    <?php } ?>
      
    <?php dynamic_sidebar($this_widget_area); ?>

    </div>
    <div class='sld-bottom'></div>
  </div>
 