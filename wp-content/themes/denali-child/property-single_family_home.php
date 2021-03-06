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
  
  /* set sidebar always on */
  $right_sidebar=true;
  $have_sidebar=true;


?>

<?php the_post(); ?>


<?php

$_SESSION['property_price'] = $property['price'];

?>

<?php get_header(); ?>

<?php get_template_part('attention','single-property'); ?>

  <div id="content" class="inner_content_wrapper property_content <?php echo ($have_sidebar  ? 'have-sidebar' : 'no_columns'); ?>">

  <div id="post-<?php the_ID(); ?>" <?php post_class('main property_page_post'); ?>>
 		<div id="container" class="<?php echo (!empty($property['property_type']) ? $property['property_type'] . "_container" : "");?>">
			<div class="property_title_wrapper building_title_wrapper">
				<div><h1 class="property-title entry-title"><?php the_title(); ?></h1></div>
				<div > <h3 class="entry-subtitle"><?php the_tagline(); ?></h3></div>
				<div class="property-price-ber clearfix">
				<div class="property-title-price"><h2> 
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
				</h2></div>
				<div class="property-title-menu">
					 <a href="#property_map">Map</a> | <a href="#enquiry_form"> Enquire</a> <?php echo do_shortcode('[pdf_link]') ; ?> 
				</div>
				<div class="property-title-ber"><IMG src="<?php echo get_stylesheet_directory_uri() ?>/img/ber/<?php echo $property['ber']; ?>-s.png"</IMG>
				</div>
				</div>
				
				<?php if($property['on_view']){
					
				?>
					<div id="on-view" >
					
					<?php 
					
					$onviewdata = explode(" ", $property['on_view']);
					$onviewdata_date=explode("-",$onviewdata[1]);
					
					if(!$onviewdata[0]){
						$onviewdata[0]="-";
					}
					if(!$onviewdata_date[0]){
						$onviewdata_date[0]="-";
						$onviewdata_date[1]="-";
					}
					
					$daynames = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday","Saturday","Sunday");
					if (!in_array($onviewdata[0], $daynames)) {
						$onviewdata[0]="";
						$onviewdata_date[0]="-";
						$onviewdata_date[1]="-";
					}
					
				/*	$onview_date = new DateTime('2014-10-23');
					$formatted_dayname = $onview_date->format('l'); 
					$formatted_date = $onview_date->format('d'); 
					$formatted_month = $onview_date->format('M'); 
					$formatted_year = $onview_date->format('Y'); 
				*/
				?>
					
					<div class="onview-date">
						<span class="onview-dayname"><?php echo $onviewdata[0]; ?></span>
					  	<span class="onview-day"><?php echo $onviewdata_date[0]; ?></span>
  					  	<span class="onview-month"><?php echo substr($onviewdata_date[1],0,3); ?></span>
  					<!--    	<span class="onview-year"><?php // echo $formatted_year; ?></span> -->
					</div>
					
					<div class="onview-text">
						This Property is open for viewing <strong><?php echo $property['on_view'];?></strong>
						<br> <a href="#enquiry_form" style="text-decoration : underline ;">Click here to register your interest</a>
					</div>
				</div>
				<?php } ?>			
			</div>
			
			<div class="entry-content">
			
				<div class="the_content">
					<?php @the_content(); ?>
					
					 <a name="property_photos"></a> 
				<?php echo do_shortcode("[property_gallery image_size=nugentslide large_size=nugentnfull   thumb_size=nugent_thumbnail carousel=false]"); ?> 
				</div>
			</div><!-- .entry-content -->
			
			   <a name="property_map"></a> 
			   <h3 class="sub-heading" style="margin-left : 4px;">Map</h3>
			<?php get_template_part('content','single-property-map'); ?>
		</div><!-- #container -->

  </div>

  <?php if ($have_sidebar) : ?>
    <div class="sidebar">
    	<?php get_feature_list();?>
    	<br>
    	
		<div class="features_list nugent-widget" > 
			<?php if(!empty($wp_properties['taxonomies'])) foreach($wp_properties['taxonomies'] as $tax_slug => $tax_data): ?>
			<?php if(get_features("type={$tax_slug}&format=count")):  ?>
			<div  class="<?php echo $tax_slug; ?>_list features_list">
				<h2><?php echo $tax_data['label']; ?></h2>
				<ul style="padding : 5px;" class="wp_<?php echo $tax_slug; ?>s  wpp_feature_list clearfix">
					<?php get_features("type={$tax_slug}&format=list&links=false"); ?>
				</ul>
			</div>
			<?php endif; ?>
		<?php endforeach; ?>
		</div>
		<br>
		
		<div class="features_list nugent-widget stamp-duty"  > 	
			<h2>Stamp Duty</h2>
			
			<?php  /* Calculate stamp duty */  
		
				$property_price=format_property_price($property['price']);
				if($property_price==1) { $property_price=0 ; }
				$stamp_duty=calculate_stamp_duty($property_price);
				$property_price_full=$stamp_duty['price'] + $property_price;
			?>
			
			<div id="stampduty-rate"> 
			<?php echo "@".$stamp_duty['rate']."%" ?><br>
			<?php echo substr( $property['price'], 0,3 ).number_format($stamp_duty['price']) ; ?>		
			</div>
			<div id="stampduty-value"> Total Amount <br>
				<?php echo substr( $property['price'], 0,3 ).number_format($property_price_full) ; ?>
			</div>
		</div>
		
		<div  class="features_list nugent-widget mortgage-repayment" > 
			<h2>Mortgage Repayment Estimator</h2>			
			<div style="padding-left : 5px;"><?php echo do_shortcode('[mortgage-calculator]'); ?></div>
		</div>
			
		<div class="features_list nugent-widget qr-code">
	    	<?php echo do_shortcode('[qr-code]') ; ?>
      	</div>
			
		<div id="enquiry_form" class="features_list nugent-widget property-enquiry" > 
			<h2>Property Enquiry</h2>
			<div style="text-align : center; border-left : dashed 1px gray ; border-right : dashed 1px gray;  "><?php echo get_the_title();?></div>
			
			  <?php // get_template_part('content','single-property-inquiry-sidebar'); ?>
			
		<?php
		$my_shortcode = '[si-contact-form form=\'4\' hidden=\'nugent_ref=' . $property['nugent_ref'].";".get_the_title().'\']';
		?>
			<?php echo do_shortcode($my_shortcode); ?>
	    </div>
	    
	    <div class="features_list nugent-widget" style="background :  none;">
	    <h2>Other Properties</h2>
	    <br>
	   <?php 
	   	
	     $otherprop_shortcode = '[global_slideshow image_size=nugent_medium caption_opacity=0 effect=fade show_pagination_buttons=false ]' ;	    
	    echo do_shortcode($otherprop_shortcode); 
	    ?>
	    
	    </div>

  
      <ul>
        <?php dynamic_sidebar( "wpp_sidebar_" . $property['property_type'] ); ?>
      </ul>
    </div>
  <?php endif; ?>

 <div class="cboth"></div>
 
 <?php get_template_part('content','single-property-bottom'); ?>
  </div>

<?php get_footer(); ?>