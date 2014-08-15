<!DOCTYPE html>
<html>

<?php 
	include 'get_panoramio.php';
?>
	<head>
		<meta charset='utf-8'/>
		<title>Colorbox Examples</title>
		<style>
			body{font:12px/1.2 Verdana, sans-serif; padding:0 10px;}
			a:link, a:visited{text-decoration:none; color:#416CE5; border-bottom:1px solid #416CE5;}
			h2{font-size:13px; margin:15px 0 0 0;}
		</style>
		<link rel="stylesheet" href="colorbox.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="js/jquery.colorbox.js"></script>
		<script>
			$(document).ready(function(){
				//Examples of how to assign the Colorbox event to elements
				$(".group1").colorbox({rel:'group1'});
				$(".blessington").colorbox({rel:'blessington', width:'90%', maxWidth:800 ,transition:"fade"});
				$(".callbacks").colorbox({
					onOpen:function(){ alert('onOpen: colorbox is about to open'); },
					onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
					onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
					onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
					onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
				});

				
			});
		</script>
		<style type="text/css">
		
		#cboxTitle {
			width : 800px;
		
		}
		
		#cboxCurrent {
			margin-right : 10px;
		}
		
		
		#cboxPrevious {
			position:absolute;
			top:0;
			left:0;
			height: 20px;
			margin-top: 0px; /* 1/2 the hight of the button */
		}
			
		#cboxClose {
			position: absolute;
			top: 0;
			right: 0;
		}
		
		#cboxNext {
			position:absolute;
			top:0;
			left:80px;
			height: 20px;
			margin-top: 0px; /* 1/2 the hight of the button */
		}
		#cboxContent{
			margin-top : 20px;
		}
		
		
		#cboxOverlay {
			background-color: #fff;
			position: fixed;
			width: 100%;
			height: 100%;
			top : 0px;
		}
		
		
		#the-title{
			position:absolute;
			top:0;
			left:250px;
			height: 20px;
			margin-top: 2px; /* 1/2 the hight of the button */
			font-spacing : 0.2em;
			color : gray;
		
		}
		
		
		#cboxCurrent {
			position:absolute;
			top:0;
			left:140px;
			height: 20px;
			margin-top: 2px; /* 1/2 the hight of the button */
		}

		#panoramio-logo {
			width : 100px;
			margin-right : 10px;
			vertical-align : text-top;
		}

		</style>
		
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
		
		echo $photo_list->count."=photo count<br>";
		for($i=0;$i<10;$i++)
		{
			echo $photo_list->photos[$i]->owner_name."<br>" ;
		
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