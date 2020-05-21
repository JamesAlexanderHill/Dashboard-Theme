<?php get_template_part( 'parts/header' ); ?>
<!-- START index.php -->
<main>
  <?php get_template_part( 'parts/sidebar' ); ?>
  <section id="content">
    <?php
    if ( have_posts() ) :
      while ( have_posts() ) : the_post();
        the_content();
      endwhile;
    else :
      _e( 'Sorry, no posts matched your criteria.', 'textdomain' );
    endif;
    ?>
  </section>
</main>
<!-- END index.php -->
<?php get_template_part( 'parts/footer' );?>
