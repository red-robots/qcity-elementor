<?php
get_header();
?>

<?php
while ( have_posts() ) :
  the_post();
  ?>

<main id="content" <?php post_class( 'site-main' ); ?> role="main">

  <?php if ( apply_filters( 'hello_elementor_page_title', true ) ) : ?>
    <header class="page-header">
      <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
    </header>
  <?php endif; ?>
  <div class="page-content">
    <?php the_content(); ?>
    <div class="post-tags">
      <?php the_tags( '<span class="tag-links">' . __( 'Tagged ', 'hello-elementor' ), null, '</span>' ); ?>
    </div>
    <?php wp_link_pages(); ?>
  </div>
</main>

<?php endwhile; ?>



<?php
get_footer();