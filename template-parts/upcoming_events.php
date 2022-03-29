<section class="upcoming-events-slides count-<?php echo count($records) ?>">
  <div class="upcoming-events-inner">
    <?php foreach ($records as $row) { 
      $id = $row->ID;
      $eventName = $row->post_title;
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
      $start_date_month = tribe_get_start_date($id,false,'M');
      $start_date_day = tribe_get_start_date($id,false,'d');
      $permalink = get_permalink($id);

      $bottom_start_date_month = tribe_get_start_date($id,false,'F jS');
      $start_and_end_time = '';
      //$schedule_detail = tribe_events_event_schedule_details();
      $start = tribe_get_start_date($id,false,'h:i a');
      $end = tribe_get_end_date($id,false,'h:i a');
      if( $start ) {
        $start_and_end_time .= $start;
      }
      if( $end ) {
        if($start!=$end) {
          $start_and_end_time .= ' &ndash; ' . $end;
        }
      }
      if($start_and_end_time) {
        $bottom_start_date_month .= ' <b>|</b> ' . $start_and_end_time;
      }
      ?>
      <article class="event-info <?php echo $class ?>">
        <a href="<?php echo $permalink ?>" class="inner"<?php echo $style ?>>
          <div class="text">
            <div class="date">
              <?php if ($start_date_month && $start_date_day) { ?>
              <div class="wrap">
                <span class="month"><?php echo $start_date_month ?></span>
                <span class="day"><?php echo $start_date_day ?></span>
              </div>
              <?php } ?>
            </div>
            <div class="titlediv">
              <?php if ($category) { ?>
              <div class="category"><span><?php echo $category->name ?></span></div>
              <?php } ?>
              <h2 class="event-title"><?php echo $eventName ?></h2>

              <?php if ($bottom_start_date_month || $start_and_end_time) { ?>
              <div class="schedule"><?php echo $bottom_start_date_month ?></div>
              <?php } ?>
            </div>
          </div>
        </a>
      </article>
    <?php } ?>
  </div>
</section>

<script>
jQuery(document).ready(function($){
  var eventsCount = '<?php echo count($records) ?>';
  if(eventsCount>3) {
    $('.upcoming-events-inner').slick({
      infinite: true,
      slidesToShow: 3,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 2000,
      arrows: false,
      dots: false,
    });
  }
});
</script>