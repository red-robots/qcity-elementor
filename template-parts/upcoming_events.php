<section class="upcoming-events-slides">
  <div class="upcoming-events-inner">
    <?php foreach ($records as $row) { 
      $id = $row->ID;
      $thumb_id = get_post_thumbnail_id($id);
      $img = wp_get_attachment_image_src($thumb_id,'full');
      $imgSrc = ( isset($img[0]) && $img[0] ) ? $img[0] : '';
      $style = ($imgSrc) ? ' style="background-image:url('.$imgSrc.')"' : '';
      $class= ($imgSrc) ? 'hasImage':'noImage';
      $taxonomy = 'tribe_events_cat';
      $terms = get_the_terms($row,$taxonomy);
      $category = ( isset($terms[0]) && $terms[0] ) ? $terms[0] : '';
      $termLink = ($terms) ? get_term_link($terms[0],$taxonomy) : '';
      $placeholder = get_stylesheet_directory_uri() . '/assets/images/portrait.png';
      ?>
      <article class="<?php echo $class ?>">
        <div class="inner"<?php echo $style ?>>
          <div class="text">
            
          </div>
        </div>
      </article>
    <?php } ?>
  </div>
</section>

<script>
jQuery(document).ready(function($){
  $('.upcoming-events-inner').slick({
    infinite: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 2000,
    arrows: false,
    dots: false,
  });
});
</script>