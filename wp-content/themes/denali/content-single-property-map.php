<?php
/**
 * Content - Single Property Map.
 *
 * This can be overridden in child themes using get_template_part()
 *
 * @package Denali
 * @since Denali 3.0
 *
 */

 ?>

<?php

  //** Check if this content should be loaded at all */
  if(!$coords = WPP_F::get_coordinates( $post->ID )) { return; }

  if(!$skip_default_google_map_check && get_post_meta($post->ID, 'hide_default_google_map', true) == 'true') { return; }

  if(!isset($map_width)) {
    $map_width = '100%';
  }

  if(!isset($map_height)) {
    $map_height = '450px';
  }

  if(!isset($zoom_level)) {
    $zoom_level = (!empty($wp_properties['configuration']['gm_zoom_level']) ? $wp_properties['configuration']['gm_zoom_level'] : 13);
  }

  if(!isset($hide_infobox)) {
    $hide_infobox = false;
  } else {
    $hide_infobox = denali_theme::to_boolean($hide_infobox);
  }

  $this_map_dom_id = 'property_map_' . rand(10000,99999);

?>


<div class="property_map_wrapper"><div id="<?php echo $this_map_dom_id; ?>" class="" style="width:<?php echo $map_width; ?>; height:<?php echo $map_height; ?>"></div></div>

<?php ob_start(); ?>

<script type='text/javascript'>

  jQuery(document).ready(function() {

    if(typeof google !== 'undefined') {
      init_this_map();
    } else {
      jQuery("#<?php echo $this_map_dom_id; ?>").hide();
      jQuery("#<?php echo $this_map_dom_id; ?>").closest(".property_map_wrapper").hide();

      if(denali_config.developer) {
        console.log("Google Maps not loaded - propety map removed.");
      }
    }

    function init_this_map() {

      var these_coords = new google.maps.LatLng(<?php echo $coords['latitude']; ?>,<?php echo $coords['longitude']; ?>);

      var myOptions = {
        zoom: <?php echo $zoom_level; ?>,
        center: these_coords,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      }

      var map = new google.maps.Map(document.getElementById("<?php echo $this_map_dom_id; ?>"), myOptions);

      var marker = new google.maps.Marker({
        position: these_coords,
        map: map,
        title: '<?php echo addslashes($post->post_title); ?>',
        icon: '<?php echo apply_filters('wpp_supermap_marker', '', $post->ID); ?>'
      });

    <?php if(!$hide_infobox) { ?>
      var infowindow = new google.maps.InfoWindow({
        content: '<?php echo WPP_F::google_maps_infobox($post); ?>'
      });

      setTimeout(function(){

        infowindow.open(map,marker);

        google.maps.event.addListener(infowindow, 'domready', function() {
          document.getElementById('infowindow').parentNode.style.overflow='hidden';
          document.getElementById('infowindow').parentNode.parentNode.style.overflow='hidden';
        });

        google.maps.event.addListener(marker, 'click', function() {
          infowindow.open(map,marker);
        });

      }, 3000);
    <?php } ?>

    }

  });
</script>

<?php
  $google_map_js = ob_get_contents(); ob_end_clean();

  if(class_exists('WPP_F')) {
    try{
      echo WPP_F::minify_js($google_map_js);
    } catch(Exception $e){
      echo $google_map_js;
    }
  } else {
    echo $google_map_js;
  }