<?php
/**
 * Property Default Template for Single Property View
 *
 * Overwrite by creating your own in the theme directory called either:
 * property.php
 * or add the property type to the end to customize further, example:
 * property-building.php or property-floorplan.php, etc.
 *
 * By default the system will look for file with property type suffix first,
 * if none found, will default to: property.php
 *
 * Copyright 2010 Andy Potanin <andy.potanin@twincitiestech.com>
 *
 * @version 1.4
 * @author Andy Potanin <andy.potnain@twincitiestech.com>
 * @package WP-Property
*/


  if(denali_theme::is_active_sidebar("wpp_sidebar_" . $property['property_type'])) {
    $right_sidebar = true;
  }

  if(get_post_meta($post->ID, 'hide_sidebar', true) != 'true' && $right_sidebar) {
    $have_sidebar = true;
  }
  
?>
<?php the_post(); ?>

<?php

$_SESSION['property_price'] = $property['price'];

?>

<?php get_header(); ?>

<?php get_template_part('attention','single-property'); ?>

 <div id="content" class="inner_content_wrapper property_content <?php echo ($have_sidebar  ? 'have-sidebar' : 'no_columns'); ?>">
	<div  id="post-<?php the_ID(); ?>" <?php post_class('main property_page_post'); ?>>
		<div id="container" class="<?php echo (!empty($property['property_type']) ? $property['property_type'] . "_container" : "");?>">
			<div class="property_title_wrapper building_title_wrapper">
				<div><h1 class="property-title entry-title"><?php the_title(); ?></h1></div>
				<div > <h3 class="entry-subtitle"><?php the_tagline(); ?></h3></div>
				<div style="position : relative; float : left; width:33%;"><h1 class="property-title entry-title" style="color:#ad907c">
				 <?php if($property['status']=="Sale Agreed" ) { 
					echo $property['status'] ;
					
				} elseif($property['status']=="Sold" ) { 
					echo $property['status'];
				} else { 
					if(format_property_price($property['price']) == 1) {
						echo "Price: POA";
					}else {
						echo "Price: ".	$property['price'];
					}		
				}?>
				</div>
				<div id="forsale_menu">
					<a href="#property_photos"> Photos</a> | <a href="#property_map">Map</a> | <a href="#inquiry_form"> Enquiry</a>
				</div>
				<div style="position: relative ; float : right ; text-align : right; width : 33%; margin-top :1px"><IMG src="<?php echo get_stylesheet_directory_uri() ?>/img/ber/<?php echo $property['ber']; ?>-s.png"</IMG>
				</div>

			</div>
			
			<div style="clear : both;" class="entry-content">
				<div class="the_content">
					<?php @the_content(); ?>
					
					 <a name="property_photos"></a> 
				<?php echo do_shortcode("[property_gallery image_size=nugentslide large_size=nugentnfull   thumb_size=nugent_thumbnail carousel=false]"); ?> 
				</div>
			</div><!-- .entry-content -->
			
			   <a name="property_map"></a> 
			<?php get_template_part('content','single-property-map'); ?>
		</div><!-- #container -->
	</div>	
	
	<?php if ($have_sidebar) : ?>
    <div id="nugent-sidebar-prop">
		<div class="sidebar">
		
			<?php get_feature_list();?>
			<div  class="features_list nugent-widget" >	
				<h2>Property Information</h2>
				<ul style="background-color : #fff;  border: 1px solid #d6d6d6; margin : 5px 5px;" class="overview_stats list" id="property_stats">
					<li class="property_status wpp_stat_plain_list_status ">
					  <span class="attribute">Status<span class="wpp_colon">:</span></span>
					  <span class="value"><?php echo $property['status'] ; ?>&nbsp;</span>
					</li>
					<li class="property_type wpp_stat_plain_list_type alt">
					  <span class="attribute">Type<span class="wpp_colon">:</span></span>
					  <span class="value"><?php echo $property['type'] ; ?>&nbsp;</span>
					</li>

					<li class="property_price wpp_stat_plain_list_price">
					  <span class="attribute">Price<span class="wpp_colon">:</span></span>
					  <span class="value"><?php 
						if(format_property_price($property['price']) == 1) {
							echo "POA";
						}else {
							echo $property['price']."&nbsp;<span>";
						} ?>		
					</li>
					<li class="property_area wpp_stat_plain_list_area alt ">
					  <span class="attribute">Area<span class="wpp_colon">:</span></span>
					  <span class="value"><?php echo $property['area'] ; ?>&nbsp;</span>
					</li>
				</ul>
			</div>
			<div  class="features_list nugent-widget" >	
				<ul style="background-color : #fff;  margin : 5px 5px; border: 1px solid #d6d6d6;" class="overview_stats list" id="property_stats">
					<li class="property_bedrooms wpp_stat_plain_list_bedrooms alt">
					  <span class="attribute">Bedrooms<span class="wpp_colon">:</span></span>
					  <span class="value"><?php echo $property['bedrooms'] ; ?>&nbsp;</span>
					</li>
					<li class="property_bathrooms wpp_stat_plain_list_bathrooms">
					  <span class="attribute">Baths/WCs<span class="wpp_colon">:</span></span>
					  <span class="value"><?php echo $property['bathrooms'] ; ?>&nbsp;</span>
					</li>
					
					<?php if ($property['ber']) {					
						echo "<li class='property_ber wpp_stat_plain_list_ber alt'>
						  <span class='attribute'>BER<span class='wpp_colon'>:</span></span>
						  <span class='value'>";
						echo $property['ber']. "&nbsp;</span></li>" ;
						echo "<li class='property_ber_no wpp_stat_plain_list_ber_no'>
						  <span class='attribute'>BER No<span class='wpp_colon'>:</span></span>
						  <span class='value'>";
						echo $property['ber_no']."&nbsp;</span></li>";
						echo "<li class='property_energy_performance_indicator wpp_stat_plain_list_energy_performance_indicator alt'>
						  <span class='attribute'>Energy P.I.<span class='wpp_colon'>:</span></span>
						  <span class='value'>";
						echo $property['energy_performance_indicator']."&nbsp;</span></li>" ;
					} ?>
					
					<?php if ($property['size']) {					
						echo "<li class='property_size wpp_stat_plain_list_size_no'>
						  <span class='attribute'>Size (c.)<span class='wpp_colon'>:</span></span>
						  <span class='value'>";
						echo $property['size']. "&nbsp;</span></li>" ;
					}
					if ($property['site_size']) {
						echo "<li class='property_site_size_no wpp_stat_plain_list_size_size alt'>
						  <span class='attribute'>Site Size (c.)<span class='wpp_colon'>:</span></span>
						  <span class='value'>";
						echo $property['site_size']."&nbsp;</span></li>";
					
					} ?>
					
					
					
				</ul>
			</div>
			
			
			<div> 
				<?php if(!empty($wp_properties['taxonomies'])) foreach($wp_properties['taxonomies'] as $tax_slug => $tax_data): ?>
				<?php if(get_features("type={$tax_slug}&format=count")):  ?>
				<div  class="<?php echo $tax_slug; ?>_list features_list nugent-widget">
					<h2><?php echo $tax_data['label']; ?></h2>
					<ul style="padding-left : 5px;" class="wp_<?php echo $tax_slug; ?>s  wpp_feature_list clearfix">
						<?php get_features("type={$tax_slug}&format=list&links=false"); ?>
					</ul>
				</div>
				<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<div style="clear : both"> </div>
			
			<div  class="features_list nugent-widget" > 	
				<h2>Stamp Duty</h2>
				
				<?php  /* Calculate stamp duty */  
			
					$property_price=format_property_price($property['price']);
					if($property_price==1) { $property_price=0 ; }
					$stamp_duty=calculate_stamp_duty($property_price);
					$property_price_full=$stamp_duty['price'] + $property_price;
				?>
				
				<div style="position : relative; float : left;margin 0px 5px; padding-left : 5px; width:45%;"> <?php echo "@".$stamp_duty['rate']."%" ?><br>
				<?php echo substr( $property['price'], 0,3 ).number_format($stamp_duty['price']) ; ?>		
				</div>
				<div style="position : relative; float : left;margin 0px 5px; text-align: center;"> Total Amount <br>
					<?php echo substr( $property['price'], 0,3 ).number_format($property_price_full) ; ?>
				</div>
			</div>
			
			<div  class="features_list nugent-widget" > 
			<h2>Mortgage Repayment Estimator</h2>			
			<div style="padding-left : 5px;"><?php echo do_shortcode('[mortgage-calculator]'); ?></div>
			</div>
			
			<div  class="features_list nugent-widget" > 
			<h2>Property Enquiry</h2>
			<ul> 
				<?php  get_template_part('content','single-property-inquiry'); ?>
			</ul>
			</div>
			<ul>
			<?php dynamic_sidebar( "wpp_sidebar_" . $property['property_type'] ); ?>
			</ul>
		</div>
	</div>
	<?php endif; ?>
 </div>
  <div class="cboth"></div>
 <?php get_template_part('content','single-property-bottom'); ?>

 <div class="cboth"></div>

<?php get_footer(); ?>