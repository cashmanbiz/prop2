<?php

/*

Plugin Name: Panoramio Slideshow Plugin

Plugin URI: https://www.cashman.biz/plugins/wordpress

Description: Viewer for a Panoramio slideshow. 

Version: 1.0

Author: John cashman

Author URI: http://cashman.biz

License: GPL v3


Copyright (C) 2014, John Cashman, john@cashman.biz

This program is free software: you can redistribute it and/or modify

it under the terms of the GNU General Public License as published by

the Free Software Foundation, either version 3 of the License, or

(at your option) any later version.


This program is distributed in the hope that it will be useful,

but WITHOUT ANY WARRANTY; without even the implied warranty of

MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

GNU General Public License for more details.


You should have received a copy of the GNU General Public License

along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/



// Prevent direct file access

if( ! defined( 'ABSPATH' ) ) {

	header( 'Status: 403 Forbidden' );

	header( 'HTTP/1.1 403 Forbidden' );

	exit;

}


//###############################################################
define('PANJC_VERSION', '1.0');
define('PANJC_SITEBASE_URL', get_option('siteurl'));
define('PANJC_PLUGINNAME', trim(plugin_basename(dirname(__FILE__))));
define('PANJC_URL', WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)));
define('PANJC_PLUGIN_DIR', WP_PLUGIN_DIR.'/'.dirname(plugin_basename(__FILE__)));
define('PANJC_PLUGIN_FILE', plugin_basename(__FILE__));
define('PANJC_PLUGIN_CLASSPATH',PANJC_PLUGIN_DIR.'/classes');
define('PANJC_PLUGIN_URL', plugins_url()."/".dirname(plugin_basename(__FILE__)));
//###############################################################



	require_once ( PANJC_PLUGIN_CLASSPATH.'/class-panjc.php' );
	
	$obj_panJc =new panJc();
	
	if(isset($obj_panJc)){
		
		register_activation_hook(__FILE__,array($obj_panJc,'PanJc_activate'));
		register_deactivation_hook(__FILE__,array($obj_panJc,'PanJc_deactivate'));
		
		add_action('wp_head',array($obj_panJc,'PanJc_Head'),1);
		add_action('wp_enqueue_scripts', array($obj_panJc,'PanJc_enqueueScripts'));
		
	
	}



/* add_action( 'init', 'load_panjc');

function load_panjc(){
	
	$dl_panoramiojc = new PANJC;
	
//	if (class_exists("PANJC")) {
//		$dl_panoramiojc = new PANJC();
//	}
	
	//Actions and Filters
	if (isset($dl_panoramiojc)) {
	
		//Actions
		add_action('wp_head', array(&$dl_panoramiojc, 'addHeaderCode'), 1);
		//Filters
	}
	
	
	//if( defined( 'PANJC_VERSION' ) ) {
		// don't load plugin if user has the premium version installed and activated
	//	return false;
	//}
	
	
	// setup the plugin defs
	
	//define('PANJC_PLUGIN_DIR', WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__)));
//	define('PANJC_PLUGIN_URL', plugins_url()."/".dirname(plugin_basename(__FILE__)));
	//define( 'PANJC_VERSION', '1.0' );
	//define( 'PANJC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );	
	//define( 'PANJC_PLUGIN_URL', plugins_url( '/' , __FILE__ ) );
	//define( 'PANJC_PLUGIN_FILE', __FILE__ );
	
	//$my_val=PANJC_PLUGIN_DIR.'/includes/functions/control.php';
	//error_log(__METHOD__ . ": value = $myval");
	
	//require_once PANJC_PLUGIN_DIR . 'includes/functions/get_panoramio.php';
	//require PANJC_PLUGIN_DIR.'/includes/functions/control.php';
	// require_once PANJC_PLUGIN_DIR . 'includes/class-panjc.php';
//	 echo "the class dir=".PANJC_PLUGIN_DIR.'includes/class-panjc.php';
	 
//	 echo "<br>exiting <br>"
	// exit();
	
	
	//$GLOBALS['panjc'] = new PANJC();
	
	
	//Administration
	//if( is_admin() && ( false === defined( 'DOING_AJAX' ) || false === DOING_AJAX ) ) {
	//	require_once PANJC_PLUGIN_DIR . 'includes/class-admin.php';	
	//	new PANJC_Admin();
	//}
	
	
}
	



/*
class PANJC {
	function PANJC() { //constructor

	}

	function addHeaderCode() {
		?><!-- JC Was Here -->
				<?php 
	}

} //End Class PANJC


if (class_exists("PANJC")) {
	$dl_panoramiojc = new PANJC();
}

//Actions and Filters
if (isset($dl_panoramiojc)) {

	//Actions
	add_action('wp_head', array(&$dl_panoramiojc, 'addHeaderCode'), 1);
	//Filters
}

	
	


if (!class_exists("Panoramiojc")) {
	class Panoramiojc {
		function Panoramiojc() { //constructor

		}
		
		function addHeaderCode() {
			?><!-- JC Was Here -->
					<?php 
		}

	} //End Class Panaromiajc
} 
	
	
	if (class_exists("Panoramiojc")) {
		$dl_panoramiojc = new Panoramiojc();
	}
	
	//Actions and Filters
	if (isset($dl_panoramiojc)) {
	
		//Actions
		//add_action('wp_head', array(&$dl_panoramiojc, 'addHeaderCode'), 1);
		//Filters
	}
	
	

	

	

  
 	include 'get_panoramio.php';
	
	add_action( 'wp_print_scripts', 'enqueue_my_scripts' );
	add_action( 'wp_print_styles', 'enqueue_my_styles' );
	
	
	
	wp_enqueue_script( 'panoramio-jc-script', plugins_url( '/js/panoramio-jc.js' , __FILE__ ), array( 'jquery' ));
	wp_enqueue_style( 'panoramio-jc-style',  plugins_url( '/js/panoramio-jc.css' , __FILE__ ), array( 'jquery' ));
	



		<link rel="stylesheet" href="colorbox.css" />
		<link rel="stylesheet" href="css/panoramio-jc.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="js/jquery.colorbox.js"></script>
		<script src="js/panoramio-jc.js"></script>
		 
		
    	$photo_list=get_panoramio_rest("GET",10, "http://www.panoramio.com/map/get_panoramas.php?set=full&from=0&to=10&minx=-6.579544&miny=53.07&maxx=-6.47&maxy=53.219&size=original&mapfilter=false");
		?>	
		<script>
		var $gallery = $("a[rel=blessington]").colorbox();
		$("a#openGallery").click(function(e){
			e.preventDefault();
			$gallery.eq(0).click();
		});
		</script>
		
		<p><a class="blessington" href="http://mw2.google.com/mw-panoramio/photos/medium/10509862.jpg" title="Lake">Images of</a></p>
		
		<?php
		
		//echo $photo_list->count."=photo count<br>";
		for($i=0;$i<10;$i++)
		{
			$owner="<img id='panoramio-logo' src='images/logo-panoramio-google.png'></img>Photos provided by Panoramio are under the copyright of their owners<br>";
			$owner.="Author: <a href=".$photo_list->photos[$i]->owner_url.">".$photo_list->photos[$i]->owner_name."</a> ";
			$owner.=" link: <a href=".$photo_list->photos[$i]->photo_url.">".$photo_list->photos[$i]->photo_url."</a><br>";
			$owner.="<div id='the-title'>".$photo_list->photos[$i]->photo_title."</div>";
			
		?>
		<p><a style="display : none;"  class="blessington" href="<?php echo   $photo_list->photos[$i]->photo_file_url ; ?>" title="<?php echo $owner ; ?>"><?php echo $photo_list->photos[$i]->photo_title ;?></a></p>	
		<?php 
		} 
		?>
	*/

