<?php
/*
Plugin Name: Link PDF Attachment
Plugin URI: http://premium.wpmudev.org
Description: Adds a link to the top of a WordPress post to the first PDF attachment
Author: Chris Knowles
Version: 1.0
Author URI: http://twitter.com/ChrisKnowles
*/

function pdf_add_link( $content ) {

	global $post;
	
	if ( !is_single() ) return $content;

	$args = array(
		'numberposts' => 1,
		'order' => 'ASC',
		'post_mime_type' => 'application/pdf',
		'post_parent' => $post->ID,
		'post_status' => null,
		'post_type' => 'attachment',
	);

	$attachments = get_children( $args );

	if ( $attachments ) {
		foreach ( $attachments as $attachment ) {
			$content ='| <a class="pdf-icon" href="' . wp_get_attachment_url( $attachment->ID ) . '" target="_blank" >PDF</a>' . $content;
			
		}
	}
	
	return $content;
	
}

//add_filter( 'the_content' , 'pdf_add_link' );
add_shortcode('pdf_link', 'pdf_add_link' );