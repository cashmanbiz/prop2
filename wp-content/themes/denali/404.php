<?php get_header() ?>

<?php get_template_part('attention', '404'); ?> 

  <div id="content" class="inner_content_wrapper">

    <div id="post-404" class="main page type-page hentry main is_404">
      <h1 class="entry-title">Error 404 - Not Found</h1>
      <div class="entry-content">
        <p>Apologies, but the page you requested could not be found. </p>
        <p>You may navigate our site by using the links above, or by taking a look at some of our properties below.</p>
        <?php echo do_shortcode("[property_overview per_page=5]"); ?>
      </div>
    </div>

    <div class="sidebar">
      <?php dynamic_sidebar( 'right_sidebar' ); ?>
    </div>

    <div class="cboth"></div>
  
  </div>

<?php get_footer() ?>