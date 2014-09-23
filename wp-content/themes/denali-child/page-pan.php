<?php
/**
 * Template Name: Panoramio JC
 *
 */
?>

<?php get_header() ?>
<?php 

function remove_unwanted_css(){
//		wp_dequeue_style( 'cssBMoExpoDesignDefault' );
//		wp_dequeue_style( 'cssBMoExpo' );		
}

//add_action('wp_enqueue_scripts','remove_unwanted_css', 100);

?>
<?php // get_template_part('attention', 'page'); ?> 

  <div id="nocolumns" class="inner_content_wrapper no_columns">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      <div id="post-<?php the_ID(); ?>" <?php post_class('main-no-sidebar main'); ?>>
        
      <?php if(!hide_page_title()) {?>
        <h1 class="entry-title"><?php the_title();?></h1>
      <?php } ?>

        <div class="entry-content">
       
               
        <?php the_content('More Info'); ?>
        
        <?php $obj_panJc->PanJc_CreateGallery('45') ;
        ?>
          <?php //comments_template(); ?>
        </div>
      </div>
    <?php endwhile; endif; ?>
  
  <div class="cboth"></div>
  
  </div>
  
<?php get_footer() ?>

 
