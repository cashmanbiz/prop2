<?php

  if(denali_theme::is_active_sidebar('right_sidebar')) {
    $right_sidebar = true;
  }  
      
  if($right_sidebar) {
    $have_sidebar = true;
  }

?>

<?php get_header() ?>

<?php get_template_part('attention', 'category'); ?> 

  <div id="content" class="inner_content_wrapper <?php echo ($have_sidebar  ? 'have-sidebar' : 'no_columns'); ?>">
  
    <div class="blog_categories main">    
      <h1 class="entry-title"><?php echo single_cat_title( '', false ); ?></h1>
      <?php echo (category_description() != '' ? '<div class="category_description">' . category_description() . '</div>' : ''); ?>
      <?php get_template_part( 'loop', 'blog' ); ?>
    </div>
    

  <?php if ($have_sidebar) : ?>
    <div class="sidebar">
        <?php dynamic_sidebar( 'right_sidebar' ); ?>
    </div>
  <?php endif; ?>
  
  <div class="cboth"></div>
    
</div>

<?php get_footer() ?>
