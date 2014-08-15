<?php
/*
Plugin Name: Get Panoramio Rest
Plugin URI: http://www.cashman.biz
Description: Imports a Panaromia view into Wordpress
Author: John Cashman
Version: 1.0
Author URI: http://www.cashman.biz
*/

function get_panoramio_rest( $method, $count, $url, $data = false) {

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
	
	//get_panoramio_rest("GET","http://www.panoramio.com/map/get_panoramas.php?set=full&from=0&to=5&userid=8399522");
//	get_panoramio_rest("GET",10, "http://www.panoramio.com/map/get_panoramas.php?set=public&from=0&to=10&minx=-6.579544&miny=53.06993&maxx=-6.483413&maxy=53.218815&size=medium&mapfilter=true");
	
//	get_panoramio_rest("GET",10, "http://www.panoramio.com/map/get_panoramas.php?set=full&from=0&to=10&minx=-6.579544&miny=53.07&maxx=-6.47&maxy=53.219&size=original&mapfilter=false");
	
	
	//get_panoramio_rest("GET","http://www.panoramio.com/map/get_panoramas.php?set=public&from=0&to=10&minx=4.709988&miny=52.018064&maxx=5.285053&maxy=52.218125&size=small&mapfilter=true");
	//get_panoramio_rest("GET",10, "http://www.panoramio.com/map/get_panoramas.php?set=public&from=0&to=10&minx=4.709988&miny=52.018064&maxx=5.285053&maxy=52.218125&size=small&mapfilter=true");
	
	//53.218815, -6.579544 Co. Wicklow
//53.069932, -6.483413
	
	//get_panoramio_rest("GET","http://www.panoramio.com/map/get_panoramas.php?order=popularity&set=public&from=0&to=10&minx=-124.29382324218749&miny=36.089060460282006&maxx=-119.8773193359375&maxy=38.724090458956965");
	//get_panoramio_rest("GET","http://www.panoramio.com/map/get_panoramas.php?order=popularity&set=public&from=0&to=10&minx=-124.29382324218749&miny=36.089060460282006&maxx=-119.8773193359375&maxy=38.724090458956965");
	
//add_filter( 'the_content' , 'pdf_add_link' );
//add_shortcode('pdf_link', 'pdf_add_link' );
	?>
