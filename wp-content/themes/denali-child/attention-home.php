<?php
/**
 * Attention Home displays the attenion grabbing element on the homage page.
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



  if(!current_theme_supports('home_page_attention_grabber_area')) {
    return;
  }

  if(get_post_meta($post->ID, 'hide_header', true) == 'true') {
    denali_theme::console_log('AG: Home Header not rendered, disabled in post settings.');
    return;
  }

  if($ds['home_page_attention_grabber_area_hide'] == 'true') {
    denali_theme::console_log('AG: Header not rendered, Home Page Attention Grabber is disabled in Theme Options.');
    return;
  }

  $this_widget_area = 'home_page_attention_grabber';

  if(!$tabs = denali_theme::widget_area_tabs($this_widget_area)) {
    denali_header_image();
    return;
  }

  if(count($tabs) > 1) {
    denali_theme::console_log('AG: Rendering Home Tabbed attention grabber.');
    $multi_tab = true;
  } else {
    denali_theme::console_log('AG: Rendering Home Untabbed attention grabber.');
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
    
    <div style="width : 100%;"><h2 class="entry-content recent-properties">Recent Properties</h2></div>
    <?php    
     $args = array( 'post_type' => 'property', 'posts_per_page'   => 10);
     $posts_array = get_posts($args);
     
     foreach ($posts_array as $prop):
     	if($prop->featured=='true'):
     		
     		setlocale(LC_MONETARY, 'en_IE');
		
	     	if($prop->property_type=='to_rent'):
	     		$property_price = money_format('%.0n', (double) $prop->rent);
	     	else:
	     		$property_price = money_format('%.0n', (double) $prop->price);
	     	endif;
		     
	     	
		    $property_price=str_replace('EUR','&euro;',$property_price);
	     
     ?>
     	    
		    <div class="home-rollover">
		    
		   <a ontouchstart="" href="<?php echo get_permalink($prop->ID); ?>">
		   <?php echo get_the_post_thumbnail($prop->ID, 'nugentx8'); ?>
		   <span class="property-imagetext"><span>
		   <?php 
		   		echo $prop->post_title."<br>";
		   		echo $property_price."<br>";
		   		echo $prop->status."<br>";
		   		?>
		   		<IMG width=32 src="<?php echo get_stylesheet_directory_uri() ?>/img/ber/<?php echo $prop->ber; ?>-s.png"</IMG>
		   		<?php echo "<br>click image to view";
		   ?>
		   
		   </span></span>
		     <span class="property-imagetextover"><span>
		       
		   <?php 
		   		echo $prop->area." - ";
		   		echo $prop->bedrooms." Bed"."  - ";
		   		echo $property_price." ";
		   	?>
		   		  
		   
		   		</span></span></a>
    
    	</div>
    
    <?php 
       endif;
      endforeach;
    ?>
    <?php // dynamic_sidebar($this_widget_area); ?>

    </div>
    <div class='sld-bottom'></div>
  </div>
