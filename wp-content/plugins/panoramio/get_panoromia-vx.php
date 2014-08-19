<?php
/*
Plugin Name: Get Panoramio Rest
Plugin URI: http://www.cashman.biz
Description: Imports a Panaromia view into Wordpress
Author: John Cashman
Version: 1.0
Author URI: http://www.cashman.biz
*/

function get_panoramio_rest( $method, $url, $data = false) {
	//$curl = curl_init();	
	
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$curl_response = curl_exec($curl);
	if ($curl_response === false) {
		$info = curl_getinfo($curl);
		curl_close($curl);
		die('error occured during curl exec. Additioanl info: ' . var_export($info));
	}
	

	/*	switch ($method)
		{
			case "POST":
				curl_setopt($curl, CURLOPT_POST, 1);
	
				if ($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				break;
			case "PUT":
				curl_setopt($curl, CURLOPT_PUT, 1);
				break;
			default:
				if ($data)
					$url = sprintf("%s?%s", $url, http_build_query($data));
		}
	
		// Optional Authentication:
		//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//curl_setopt($curl, CURLOPT_USERPWD, "username:password");
	
		//curl_setopt($curl, CURLOPT_URL, $url);
		//curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	*/
		$curl_response = curl_exec($curl);
	
		curl_close($curl);
	
 		//if($curl_response){
 			//echo "has curl response<br>";
 			//var_dump($curl_response);
 		//}
 		
		
		$decoded = json_decode($curl_response);
		
		//var_dump($decoded);
		if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
			die('error occured: ' . $decoded->response->errormessage);
		}
		echo 'response ok!';
		
		//foreach($decoded AS $prop => $val) {echo '<br/>'. $prop .' - '. $val;}
		
		for($i=0;$i<20;$i++)
		{	
			echo $decoded->photos[$i]->photo_id."<br>"; 
			echo $decoded->photos[$i]->photo_title."<br>";
			?>
			<img src="<?php echo $decoded->photos[$i]->photo_url ?>" ></img><br>";
			
			 
			<img src="<?php echo $decoded->photos[$i]->photo_file_url ?>" ></img><br>";
				
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
		*/ 
			
		}
		
		//var_export($decoded->response);
		
		//return $result;
	


}
	
	get_panoramio_rest("GET","http://www.panoramio.com/map/get_panoramas.php?set=public&from=0&to=20&minx=53.176303&miny=-6.552686&maxx=53.152169&maxy=-6.510285&size=medium&mapfilter=true2");


//add_filter( 'the_content' , 'pdf_add_link' );
//add_shortcode('pdf_link', 'pdf_add_link' );

	?>
	
	
	
	