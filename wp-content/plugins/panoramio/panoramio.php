<!DOCTYPE html>
<html>

<?php 
	include 'get_panoramio.php';
?>
	<head>
		<meta charset='utf-8'/>
		<title>Blessington Slide Show</title>
		<link rel="stylesheet" href="colorbox.css" />
		<link rel="stylesheet" href="css/panoramio-jc.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="js/jquery.colorbox.js"></script>
		<script src="js/panoramio-jc.js"></script>
		
	</head>
	<body>
		<?php 
		
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

		
	</body>
</html>