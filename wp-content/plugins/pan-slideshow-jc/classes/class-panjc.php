<?php


class panJc {
	function panJc() { //constructor

	}

	public function wpdocs_dequeue_script() {
		wp_dequeue_script( 'cssBMoExpo' );
		wp_dequeue_script( 'cssBMoExpoDesignDefault');
		wp_dequeue_script( 'sG_cssBMoExpoDesign');
		wp_dequeue_script( 'slG_cssBMoExpoDesign');
		
		
		
	}
	
	public function PanJc_remove_styles(){
		add_action( 'wp_enqueue_scripts', 'wpdocs_dequeue_script', 300 );
	}
	
	
	function PanJc_enqueueScripts(){
		
		if(is_page('blessington')){

			wp_register_script( 'jqueryMin', 'https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js','1.10.2',false);
			wp_register_script( 'jqueryColorboxMin', PANJC_URL.'/js/jquery.colorbox.min.js', array('jquery') , '1.5.13' ,false);
			wp_register_script( 'jqueryColorbox', PANJC_URL.'/js/jquery.colorbox.js', array('jquery') , '1.5.13' ,false);
			wp_register_script( 'panoramioJc', PANJC_URL.'/js/panoramio-jc.js', array('jquery') , PANJC_VERSION ,false);
			
			
			
			if (function_exists('wp_enqueue_script')) {
				wp_enqueue_script('jqueryMin');
				wp_enqueue_script('jqueryColorboxMin');
				wp_enqueue_script('jqueryColorbox');
				wp_enqueue_script('panoramioJc');
		
		}
		

		
		}
	}
	
	public function PanJc_Head(){
		if(is_page('blessington')){
			wp_register_style('cssPanJc',  PANJC_URL.'/css/pan-slideshow-jc.css',false, PANJC_VERSION ,'all');
		
			if (function_exists('wp_enqueue_style')) {
				wp_enqueue_style('cssPanJc');
			}
		}
	
	}	
	public function PanJc_CreateGallery($number_of_images){
		
		
		?><script>	
			// $(".container").css( "zIndex", "0" );
		</script>	
		<?php	
		$photo_list=$this->get_panoramio_rest("GET",$number_of_images, "http://www.panoramio.com/map/get_panoramas.php?set=full&from=0&to=$number_of_images&minx=-6.579544&miny=53.07&maxx=-6.47&maxy=53.219&size=original&mapfilter=false");
		?>

			<!--  	<p><a class="blessington" href="http://mw2.google.com/mw-panoramio/photos/medium/10509862.jpg" title="Lake">Images of Blessington</a></p> -->
				
				<script>
				$('a.blessington').click( function() {
    				$('.container').css( "zIndex", "0" );
				});

				</script>
				
				
				
				<div style="display : inline;">
				<?php
				
				//echo $photo_list->count."=photo count<br>";
				
				
				for($i=0;$i<$number_of_images;$i++)
				{
					$owner="<div id='the-title'>".$photo_list->photos[$i]->photo_title."</div>";
					$owner.="<div id='image-footer'><img id='panoramio-logo' src='".PANJC_PLUGIN_URL."/images/logo-panoramio-google.png'></img>Photos provided by Panoramio are under the copyright of their owners<br>";
					$owner.="Author: <a href=".$photo_list->photos[$i]->owner_url.">".$photo_list->photos[$i]->owner_name."</a> ";
					$owner.=" link: <a href=".$photo_list->photos[$i]->photo_url.">".$photo_list->photos[$i]->photo_url."</a></div>";
					
				?>
				<div style="position: relative; float : left ; height : 100px;"><a class="blessington" href="<?php echo   $photo_list->photos[$i]->photo_file_url ; ?>" title="<?php echo $owner ; ?>"><img style="width :100px; height: 100px ;" src="<?php echo   $photo_list->photos[$i]->photo_file_url ; ?>"></img></a></p>	
				</div>
				
				<?php 
				} 
				?>
				</div>
				<?php 
		
	}
	
	public function PanJc_activate(){	
	}
	
	
	public function PanJc_deactivate(){
	}
		
	
	public function get_panoramio_rest( $method, $count, $url, $data = false) {
	
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$curl_response = curl_exec($curl);
		if ($curl_response === false) {
			$info = curl_getinfo($curl);
			curl_close($curl);
			die('error occured during curl exec. Additioanl info: ' . var_export($info));
		}
	
		$curl_response = curl_exec($curl);
	
		curl_close($curl);
	
		$decoded = json_decode($curl_response);
	
		//var_dump($decoded);
		if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
			die('error occured: ' . $decoded->response->errormessage);
		}
		/*	echo 'response ok!';
	
		echo $decoded->count."=photo count<br>";
		for($i=0;$i<$count;$i++)
		{
		echo $decoded->photos[$i]->photo_id."<br>";
		echo $decoded->photos[$i]->photo_title."<br>";
		echo $decoded->photos[$i]->photo_url."<br>" ;
		echo $decoded->photos[$i]->owner_id."<br>" ;
		echo $decoded->photos[$i]->owner_name."<br>" ;
		echo $decoded->photos[$i]->owner_url."<br>" ;
	
		/*	?><br>
		<!--	<img width="800" src="<?php // echo $decoded->photos[$i]->photo_file_url ?>" ></img><br> -->
	
		<?php
		/*
		"longitude": 11.280727,
		"latitude": 59.643198,
		"width": 500,
		"height": 333,
		"upload_date": "22 January 2007",
		"owner_id": 39160,
		"owner_name": "Snemann",
		"owner_url": "http://www.panoramio.com/user/39160",
	
	
		}
		*/
	
		return $decoded;
	
	
	}
	
	
	
} //End Class panJc

