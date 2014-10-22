<?php
/**
 * WP-Property Overview Nugent Main List Template
 *
 * To customize this file, copy it into your theme directory, and the plugin will
 * automatically load your version.
 *
 * You can also customize it based on property type.  For example, to create a custom
 * overview page for 'building' property type, create a file called property-overview-building.php
 * into your theme directory.
 *
 * @version 1.0
 * @author John Cashman <john@cashman.biz>
 * @package WP-Property
*/
global $ds;
?>

<?php if (have_properties()) { ?>
  <?php $thumbnail_dimentions = WPP_F::get_image_dimensions($wpp_query['thumbnail_size']); ?>
  <div class="wpp_row_view all-properties wpp_property_view_result">
    <?php foreach (returned_properties('load_gallery=false') as $property) { ?>

	 
      <?php //  $image = property_overview_image('return=true'); ?>
      <div class="property_div property_div_nugent div_block <?php echo $property['post_type']; ?> <?php echo (empty($image) ? 'wpp_no_image' : ''); ?> clearfix">

        <div class="wpp_overview_right_column_nugent div_block" >

	<div id="nugent-area" class="nugent-listprop clearfix"><?php echo $property['area']; ?></div>
	<div id="nugent-link" class="nugent-listprop">
              <a <?php echo $in_new_window; ?> href="<?php echo $property['permalink']; ?>"><?php echo $property['post_title']; ?></a>
              <?php if ($property['is_child']): ?> <?php _e('of','denali'); ?> <a <?php echo $in_new_window; ?> href='<?php echo $property['parent_link']; ?>'><?php echo $property['parent_title']; ?></a><?php endif; ?>

	</div>
	<div id="nugent-type" class="nugent-listprop"><?php echo $property['type']; ?></div>
	<div id="nugent-bedrooms" class="nugent-listprop"><?php if($property['bedrooms']){ echo $property['bedrooms']." bed" ; }?></div>
	<div id="nugent-status" class="nugent-listprop" > <?php echo $property['status']; ?></div>
	
	
	<?php 

	if ($property['property_type']=="to_rent"){
		$property_price=$property['rent'];
	}else{
		$property_price=$property['price'];
	}
	
	
	
	if ($property['property_type']=="to_rent"):
	if(substr($property['rent'],3,8)=='1' )	{
			$property_price="POA";
		}else{
			$property_price = $property['rent'];
		}
	else:
		if(substr($property['price'],3,8)=='1' )	{
			$property_price="POA";
		}else{
			$property_price=$property['price'];
		}
	endif;
	
	
	
?> 
	
	<div id="nugent-price" class="nugent-listprop" <?php if($property['new_property']){ ?> style="background : url('<?php echo get_stylesheet_directory_uri(); ?>/img/icons/new-32.png') no-repeat left 0px ;" <?php } ?> ><?php echo $property_price; ?></div>
	

          <?php
          unset($overview_attributes);
          if ($ds['property_overview_attributes']['detail'])
            foreach ($ds['property_overview_attributes']['detail'] as $attribute) {



              $attribute_title = ($wp_properties['property_stats'][$attribute] ? $wp_properties['property_stats'][$attribute] : UD_F::de_slug($attribute) ) ;

              $attribute_data = WPP_F::get_attribute_data($attribute);

              $attribute_classes = array('property_'.$attribute);

              if (!empty($attribute_data['data_input_type'])) {
                $attribute_classes[] = 'type_'.$attribute_data['data_input_type'];
              }

              if ($attribute == 'property_type') {
                $attribute = 'property_type_label';
              }

              if ($attribute == 'post_content' || $attribute == 'post_excerpt')
                $attribute_title = '';

              if ($property[$attribute]) {

                if ($attribute == 'post_content' || $attribute == 'post_description') {
                  $property[$attribute] = nl2br($property[$attribute]);
                }

                $property[$attribute] = do_shortcode(html_entity_decode($property[$attribute]));

                if (( $attribute_data['data_input_type'] == 'checkbox' && ($property[$attribute] == 'true' || $property[$attribute] == 1) ) || (!$attribute_data['numeric'] && empty($attribute_data['data_input_type']) && ( $property[$attribute] === 1 || $property[$attribute] === '1' ) )) {
                  if ($wp_properties['configuration']['property_overview']['show_true_as_image'] == 'true') {
                    $attribute_classes[] = 'checkbox-as-image';
                    $property[$attribute] = '<div class="true-checkbox-image"></div>';
                  } else {
                    $property[$attribute] = __('Yes','denali');
                  }
                } else if ($property[$attribute] == 'false') {
                  if ($wp_properties['configuration']['property_overview']['show_true_as_image'] == 'true')
                    continue;
                  $property[$attribute] = __('No','denali');
                }




                $overview_attributes[] = "<li class='".  implode(' ',apply_filters($attribute.'attribute_classes',$attribute_classes))."'>" . ($attribute_title ? "<span class='wpp_attribute_icon icon_{$attribute}'></span><span class='attribute'>{$attribute_title}</span>" . ('<span class="colon">:</span>') : "") . " <span class='value'>{$property[$attribute]}</span></li>";
              }
            }
          ?>





        </div><?php // .wpp_right_column ?>

      </div><?php // .property_div  ?>

  <?php } ?>
  </div><?php // .wpp_row_view  ?>
  <?php } else { ?>
  <div class="wpp_nothing_found">
  <?php // echo sprintf(__('Sorry, no properties available  - try expanding your search, or <a href="%s">view all</a>.','denali'), site_url() . '/' . $wp_properties['configuration']['base_slug']); ?>
  
  </div>
  <?php } ?>
<br class="cb" />
