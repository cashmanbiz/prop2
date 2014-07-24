<?php
/**
 * Attention Home displays the attenion grabbing element on the page (post)
 * or the Header Image (Slideshow)
 *
 *
 * This can be overridden in child themes.
 * For example, attention-page.php would
 * be used if it exists and we ask for the attention with:
 * <code>get_template_part( 'attention', 'page' );</code>
 *
 * @package Denali
 * @since Denali 3.0
 *
 */

  $this_widget_area = 'inside_attention_grabber';

  if(get_post_meta($post->ID, 'hide_header', true) == 'true') {
    denali_theme::console_log('AG: Header not rendered, disabled in post settings.');
    return;
  }

  if(!current_theme_supports('inside_attention_grabber_area')) {
    return;
  }
  elseif($ds['inside_attention_grabber_area_hide'] == 'true') {
    denali_theme::console_log('AG: Header not rendered, Inside Pages Attention Grabber is disabled in Theme Options.');
    return;
  }
  elseif(!$tabs = denali_theme::widget_area_tabs($this_widget_area)) {
    denali_header_image();
    return;
  }

  if(count($tabs) > 1) {
    denali_theme::console_log('AG: Rendering Inside Pages Tabbed Attention Grabber.');
    $multi_tab = true;
  } else {
    denali_theme::console_log('AG: Rendering Inside Pages Untabbed Attention Grabber.');
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
