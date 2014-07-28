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


//[get_child_url]
function child_theme_uri( $attrs = array (), $content = ''  ){
	
	$child_uri = is_child_theme() ? get_stylesheet_directory_uri()	: get_template_directory_uri();
	
	return trailingslashit($child_uri);
}
add_shortcode( 'get_child_url', 'child_theme_uri' );

?>