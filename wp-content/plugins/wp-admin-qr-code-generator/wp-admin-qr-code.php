<?php
/*
Plugin Name: WP Admin QR Code Generator
Plugin URI: http://wordpress.org/plugins/wp-admin-qr-code-generator/
Description: This is an extremely simple plugin that generates QR codes for posts and pages. The QR code is added as a metabox in the page/post editor screen. It doesn't do ANYTHING else!
Version: 1.0.1
Author: ThemeAvenue
Author URI: http://themeavenue.net/
License: GPL2
*/

/* Define all the plugin constants */
define( 'WPAQR_VERSION', '1.0.1' );
define( 'WPAQR_NAME', 'WP Admin QR Code Generator' );
define( 'WPAQR_URL', plugin_dir_url( __FILE__ ) );
define( 'WPAQR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPAQR_PREFIX', 'wpaqr_' );
define( 'WPAQR_BASENAME', plugin_basename(__FILE__) );

if( !class_exists( 'WPA_QR_Code' ) ) {

	class WPA_QR_Code {

		public function __construct() {

			/* Register the metaboxes */
			add_action( 'add_meta_boxes', array( $this, 'RegisterMetabox' ) );

			/* Register the dashboard widget */
			add_action( 'wp_dashboard_setup', array( $this, 'RegisterDashboardWidget' ) );

		}

		public function getPostTypes() {

			/* Define the post types */
			$post_types = array(
				'page',
				'post',									'property',
			);

			return apply_filters( 'wpaqr_post_types', $post_types );
		}

		public function RegisterMetabox() {

			$post_types = $this->getPostTypes();

			foreach( $post_types as $pt ) {
        		add_meta_box( 'wpaqr_qr_code', __( 'QR Code', 'wpaqr' ), array( $this, 'GenerateQrCode'), $pt, 'side', 'default' );
    		}
		}

		public function GenerateQrCode() {

			global $post;

			?><img src="https://chart.googleapis.com/chart?cht=qr&amp;chs=200x200&amp;chl=<?php echo get_permalink( $post->ID ); ?>" width="200" height="200" /><?php
		}

		public function RegisterDashboardWidget() {

			/* Register the widget */
			wp_add_dashboard_widget( 'wpaqr_site_qr_code', __( 'Homepage QR Code Link', 'wpaqr' ), array( $this, 'DashboardWidget' ) );

		}

		public function DashboardWidget() {

			?><img src="https://chart.googleapis.com/chart?cht=qr&amp;chs=200x200&amp;chl=<?php echo home_url(); ?>" width="200" height="200" /><?php

		}
	}
}

/* Instanciate the class if we are
 * in the WordPress admin only.
 */
if( is_admin() )
	$wpaqr = new WPA_QR_Code();
?>