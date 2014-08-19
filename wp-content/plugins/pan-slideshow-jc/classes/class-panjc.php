<?php


class panJc {
	function panJc() { //constructor

	}
	
	function PanJc_enqueueScripts(){
		wp_register_script( 'jqueryColorboxMin', PANJC_URL.'/js/jquery.colorbox.min.js', array('jquery') , '1.5.13' ,false);
		wp_register_script( 'jqueryColorbox', PANJC_URL.'/js/jquery.colorbox.js', array('jquery') , '1.5.13' ,false);
		wp_register_script( 'panoramioJc', PANJC_URL.'/js/panoramio-jc.js', array('jquery') , PANJC_VERSION ,false);
		
		if (function_exists('wp_enqueue_script')) {
			wp_enqueue_script('jqueryColorboxMin');
			wp_enqueue_script('jqueryColorbox');
			wp_enqueue_script('panoramioJc');
		
		}
	}
	
	function PanJc_Head(){
		wp_register_style('cssPanJc',  PANJC_URL.'/css/pan-slideshow-jc.css',false, PANJC_VERSION ,'all');
		
		if (function_exists('wp_enqueue_style')) {
			wp_enqueue_style('cssPanJc');
		}
	
	}	
	
} //End Class panJc

