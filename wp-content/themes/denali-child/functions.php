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
		
		?>
		<div class="nugent-feature-widget">
	 	
	 	<?php if (empty($wp_properties['property_groups']) || $wp_properties['configuration']['property_overview']['sort_stats_by_groups'] != 'true') : ?>
	          <ul id="property_stats" class="overview_stats list">
	            <?php  @draw_stats("display=list"); ?>
	          </ul>
	        <?php 
	        	else:
	            	@draw_stats("display=list&sort_by_groups=true");
	         endif; 
			?>	

	     </div>
	<?php 
	}
	
	
	
function draw_stats( $args = false, $property = false ) {
		global $wp_properties, $post;
	
		if ( !$property ) {
			$property = $post;
		}
	
		$property = prepare_property_for_display( $property );
	
		if ( is_array( $property ) ) {
			$property = WPP_F::array_to_object( $property );
		}
	
		$defaults = array(
				'sort_by_groups' => $wp_properties[ 'configuration' ][ 'property_overview' ][ 'sort_stats_by_groups' ],
				'display' => 'dl_list',
				'show_true_as_image' => $wp_properties[ 'configuration' ][ 'property_overview' ][ 'show_true_as_image' ],
				'make_link' => 'true',
				'hide_false' => 'false',
				'first_alt' => 'false',
				'return_blank' => 'false',
				//** Args below are related to WPP 2.0. but it's needed to have the compatibility with new Denali versions */
				'include_clsf' => 'all', // The list of classifications separated by commas or array which should be included. Enabled values: all|[classification,classification2]
				'title' => 'true',
				'stats_prefix' => sanitize_key( WPP_F::property_label( 'singular' ) )
		);
	
		extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );
	
		$property_stats = array();
		$groups = $wp_properties[ 'property_groups' ];
	
		/**
		 * Determine if we should draw meta data.
		 * The functionality below is related to WPP2.0
		 * Now it just adds compatibility with new Denali versions
		*/
		if ( $include_clsf == 'detail' ) {
			$sort_by_groups = 'false';
			foreach ( $wp_properties[ 'property_meta' ] as $k => $v ) {
				if ( $k == 'tagline' ) {
					continue;
				}
				if ( !empty( $property->$k ) ) {
					$property_stats[ $v ] = $property->$k;
				}
			}
		} else {
			$property_stats = WPP_F::get_stat_values_and_labels( $property, $args );
		}
	
		if ( empty( $property_stats ) ) {
			return;
		}
	
		//* Prepare values before display */
		$stats = array();
		$labels_to_keys = array_flip( $wp_properties[ 'property_stats' ] );
	
		foreach ( $property_stats as $attribute_label => $value ) {
	
			$tag = $labels_to_keys[ $attribute_label ];
	
			if ( empty( $value ) ) {
				continue;
			}
	
			$attribute_data = WPP_F::get_attribute_data( $tag );
	
			//** Do not show attributes that have value of 'value' if enabled */
			if ( $hide_false == 'true' && $value == 'false' ) {
				continue;
			}
	
			//* Skip blank values (check after filters have been applied) */
			if ( $return_blank == 'false' && empty( $value ) ) {
				continue;
			}
	
			$value = html_entity_decode( $value );
	
			//** Single "true" is converted to 1 by get_properties() we check 1 as well, as long as it isn't a numeric attribute */
			if ( ( $attribute_data[ 'data_input_type' ] == 'checkbox' && in_array( strtolower( $value ), array( 'true', '1', 'yes' ) ) ) ) {
				if ( $show_true_as_image == 'true' ) {
					$value = '<div class="true-checkbox-image"></div>';
				} else {
					$value = __( 'Yes', 'wpp' );
				}
			} else if ( $value == 'false' ) {
				if ( $wp_properties[ 'configuration' ][ 'property_overview' ][ 'show_true_as_image' ] == 'true' )
					continue;
				$value = __( 'No', 'wpp' );
			}
	
			//* Make URLs into clickable links */
			if ( $make_link == 'true' && WPP_F::isURL( $value ) ) {
				$value = str_replace( '&ndash;', '-', $value );
				$value = "<a href='{$value}' title='{$label}'>{$value}</a>";
			}
	
			//* Make emails into clickable links */
			if ( $make_link == 'true' && WPP_F::is_email( $value ) ) {
				$value = "<a href='mailto:{$value}'>{$value}</a>";
			}
	
			$stats[ $attribute_label ] = $value;
		}
	
		if ( $display == 'array' ) {
			if( $sort_by_groups == 'true' && is_array( $groups ) ) {
				$stats = sort_stats_by_groups( $stats, array( 'includes_values' => true ) );
			}
			return $stats;
		}
	
		$alt = $first_alt == 'true' ? "" : "alt";
	
		//** Disable regular list if groups are NOT enabled, or if groups is not an array */
		if ( $sort_by_groups != 'true' || !is_array( $groups ) ) {
	
			foreach ( $stats as $label => $value ) {
				$tag = $labels_to_keys[ $label ];
	
				$alt = ( $alt == "alt" ) ? "" : "alt";
				switch ( $display ) {
					case 'dl_list':
						?>
	            <dt class="<?php echo $stats_prefix; ?>_<?php echo $tag; ?> wpp_stat_dt_<?php echo $tag; ?>"><?php echo $label; ?>
	              <span class="wpp_colon">:</span></dt>
	            <dd class="<?php echo $stats_prefix; ?>_<?php echo $tag; ?> wpp_stat_dd_<?php echo $tag; ?> <?php echo $alt; ?>"><?php echo $value; ?>
	              &nbsp;</dd>
	            <?php
	            break;
	          case 'list':
	            ?>
	            <li class="<?php echo $stats_prefix; ?>_<?php echo $tag; ?> wpp_stat_plain_list_<?php echo $tag; ?> <?php echo $alt; ?>">
	              <span class="attribute"><?php echo $label; ?><span class="wpp_colon">:</span></span>
	              <span class="value"><?php echo $value; ?>&nbsp;</span>
	            </li>
	            <?php
	            break;
	          case 'plain_list':
	            ?>
	            <span class="<?php echo $stats_prefix; ?>_<?php echo $tag; ?> attribute"><?php echo $label; ?>:</span>
	            <span class="<?php echo $stats_prefix; ?>_<?php echo $tag; ?> value"><?php echo $value; ?>&nbsp;</span>
	            <br/>
	            <?php
	            break;
	          case 'detail':
	            ?>
	            <h4 class="wpp_attribute"><?php echo $label; ?><span class="separator">:</span></h4>
	            <p class="value"><?php echo $value; ?>&nbsp;</p>
	            <?php
	            break;
	        }
	      }
	    } else {
	
	      $stats_by_groups = sort_stats_by_groups( $stats, array( 'includes_values' => true ) );
	      $main_stats_group = $wp_properties[ 'configuration' ][ 'main_stats_group' ];
	      $labels_to_keys = array_flip( $wp_properties[ 'property_stats' ] );
	
	      foreach ( $stats_by_groups as $gslug => $gstats ) {
	        ?>
	        <div class="wpp_feature_list">
	        <?php
	        if ( $main_stats_group != $gslug || !@key_exists( $gslug, $groups ) ) {
	          $group_name = ( @key_exists( $gslug, $groups ) ? $groups[ $gslug ][ 'name' ] : __( 'Other', 'wpp' ) );
	          ?>
	          <h2 class="wpp_stats_group"><?php echo $group_name; ?></h2>
	        <?php
	        }
	
	        switch ( $display ) {
	          case 'dl_list':
	            ?>
	            <dl class="wpp_property_stats overview_stats">
	            <?php foreach ( $gstats as $label => $value ) : ?>
	              <?php
	              $tag = $labels_to_keys[ $label ];
	              ?>
	              <?php $alt = ( $alt == "alt" ) ? "" : "alt"; ?>
	              <dt class="<?php echo $stats_prefix; ?>_<?php echo $tag; ?> wpp_stat_dt_<?php echo $tag; ?>"><?php echo $label; ?></dt>
	              <dd class="<?php echo $stats_prefix; ?>_<?php echo $tag; ?> wpp_stat_dd_<?php echo $tag; ?> <?php echo $alt; ?>"><?php echo $value; ?>
	                &nbsp;</dd>
	            <?php endforeach; ?>
	            </dl>
	            <?php
	            break;
	          case 'list':
	            ?>
	            <ul class="overview_stats wpp_property_stats list">
	            <?php foreach ( $gstats as $label => $value ) : ?>
	              <?php
	              $tag = $labels_to_keys[ $label ];
	              $alt = ( $alt == "alt" ) ? "" : "alt";
	              ?>
	              <li class="<?php echo $stats_prefix; ?>_<?php echo $tag; ?> wpp_stat_plain_list_<?php echo $tag; ?> <?php echo $alt; ?>">
	                <div class="attribute"><?php echo $label; ?>:</div>
	                <div class="value"><?php echo $value; ?></div>
	              </li>
	            <?php endforeach; ?>
	            </ul>
	            <?php
	            break;
	          case 'plain_list':
	            foreach ( $gstats as $label => $value ) {
	              $tag = $labels_to_keys[ $label ];
	              ?>
	              <span class="<?php echo $stats_prefix; ?>_<?php echo $tag; ?> attribute"><?php echo $label; ?>:</span>
	              <span class="<?php echo $stats_prefix; ?>_<?php echo $tag; ?> value"><?php echo $value; ?>&nbsp;</span>
	              <br/>
	            <?php
	            }
	            break;
	        }
	        ?>
	        </div>
	      <?php
	      }
	    }
	  }

	?>