<?php
/*
 * This file is an example of a child theme.
 * The functions in this file sill be loaded before functions.php file from the Denali theme
 * You can setup hooks and filters in here that will be used by the Denali theme.
 * 
 */

// Uncomment to use, this is just for example
//add_action('denali_post_theme_support', 'denali_remove_custom_background');

/**
 * Remove custom background.
 *
 */
function denali_remove_custom_background() {
	global $_wp_theme_features;	
	unset($_wp_theme_features['custom-background']);	
	
	
	remove_theme_support('header-property-search');
	remove_theme_support('header-property-contact');
	remove_theme_support('header-login');
	remove_theme_support('header-card');
}



/* Calculate unformatted property price */
	function format_property_price($property_price_formatted){
		
		$property_price=0;
		
		if($property_price_formatted) {
			$property_price_lesscurrency=substr( $property_price_formatted, 3 ) ;  // remove currency identifier
			$property_price=(int)intval(str_ireplace(",","",$property_price_lesscurrency));  // get integer val for property price
			
		}else {	
			$property_price=0;
		}
		
		return $property_price;
	}
	
	/* Calculate stamp duty */ 
	function calculate_stamp_duty($property_price){
		$stamp_duty['rate']=1;
		$stamp_duty_ceiling=1000000;
		if($property_price > $stamp_duty_ceiling) $stamp_duty['rate']=2 ;
		$stamp_duty['price']=$property_price * $stamp_duty['rate']/100;
		
		return $stamp_duty;
	}

	
	function get_feature_list(){
	 	if ( empty($wp_properties['property_groups']) || $wp_properties['configuration']['property_overview']['sort_stats_by_groups'] != 'true') : ?>
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
	<?php 
	}
?>