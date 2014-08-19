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


class PANJC {
	function PANJC() { //constructor

	}

	function addHeaderCode() {
		?><!-- JC Was Here -->
				<?php 
	}

} //End Class PANJC




if (!class_exists("PANJC")) {
	class PANJC {
		function PANJC() { //constructor

		}
		
		function addHeaderCode() {
			?><!-- JC Was Here -->
					<?php 
		}

	} //End Class PANJC
} 
	
	
	if (class_exists("PANJC")) {
		$dl_panoramiojc = new PANJC();
	}
	
	//Actions and Filters
	if (isset($dl_panoramiojc)) {
	
		//Actions
		add_action('wp_head', array(&$dl_panoramiojc, 'addHeaderCode'), 1);
		//Filters
	}
	
	

	
/*
	

  
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

