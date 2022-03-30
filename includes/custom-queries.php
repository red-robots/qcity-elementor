<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// echo "<pre style='padding: 50px 0 0 350px'>";
// echo "</pre>";

//getUpcomingEventsFunc(3,0);
function getUpcomingEventsFunc($perpage=10,$offset=0) {
  global $wpdb;
  $datenow = strtotime( date('Y-m-d H:i:s') );
  $offset_perpage = $offset . ',' . $perpage;
  $query = "SELECT p.ID,p.post_title,m.*, UNIX_TIMESTAMP(m.meta_value) AS start_date  FROM " . $wpdb->prefix . "posts p,".$wpdb->prefix."postmeta m WHERE p.ID=m.post_id AND p.post_status='publish' AND p.post_type='tribe_events' AND m.meta_key='_EventStartDate' AND UNIX_TIMESTAMP(m.meta_value) >= " . $datenow . " ORDER BY start_date ASC LIMIT " . $offset_perpage;
  $result = $wpdb->get_results($query);

  $query_total = "SELECT count(*) AS total FROM " . $wpdb->prefix . "posts p,".$wpdb->prefix."postmeta m WHERE p.ID=m.post_id AND p.post_status='publish' AND p.post_type='tribe_events' AND m.meta_key='_EventStartDate' AND UNIX_TIMESTAMP(m.meta_value) >= " . $datenow;
  $result_total = $wpdb->get_row($query_total);

  $res['records'] = '';
  $res['total'] = 0;

  if($result) {
    $res['records'] = ($result) ? $result : '';
    $res['total'] = $result_total->total;
  }
  return $res;
}


// add_shortcode( 'upcoming_events', 'qct_upcoming_events_func' );
// function qct_upcoming_events_func( $atts ) {
//   $a = shortcode_atts( array(
//     'perpage' => 10,
//     'offset'  => 0
//   ), $atts );
//   $output = '';
//   $res = getUpcomingEventsFunc($a['perpage'], $a['offset']);
//   if( isset($res['records']) && $res['records'] ) {
//     ob_start();
//     $records = $res['records'];
//     include( locate_template('template-parts/upcoming_events.php') );
//     $output = ob_get_contents();
//     ob_end_clean();
//   }
//   return $output;
// }


add_shortcode( 'upcoming_events', 'qct_upcoming_events_func' );
function qct_upcoming_events_func( $atts ) {
  $a = shortcode_atts( array(
    'show' => 10,
    'perpage' => 10,
    'offset'  => 0
  ), $atts );
  $output = '';
  // $events = tribe_get_events( array(
  //   'posts_per_page' => $a['perpage'],
  //   'start_date' => new DateTime(),
  //   'tribe_events_cat' => 'featured'
  // ) );
  $perpage = 10;
  $perpage = (isset($a['perpage']) && $a['perpage']) ? $a['perpage'] : 10;
  if(isset($a['show']) && $a['show']) {
    $perpage = $a['show'];
  }

  $events = tribe_get_events( array(
    'posts_per_page' => $perpage,
    'start_date' => new DateTime(),
    'meta_query'    => array(
      array(
        'key'   => '_tribe_featured',
        'compare' => '=',
        'value'   => 1,
      ),    
    ),
  ) );

  

  //_tribe_featured

  if($events) {
    ob_start();
    $records = $events;
    include( locate_template('template-parts/upcoming_events.php') );
    $output = ob_get_contents();
    ob_end_clean();
  }

  return $output;
}

add_shortcode( 'get_posts_left', 'custom_category_posts_left_func' );
function custom_category_posts_left_func( $atts ) {
  $a = shortcode_atts( array(
    'category' => 'things-to-do',
    'show'  => 4
  ), $atts );

  $output = '';
  $perpage = ( isset($a['show']) && $a['show'] ) ? $a['show'] : 2;
  $term_slug = ( isset($a['category']) && $a['category'] ) ? $a['category'] : 'uncategorized';

  $args = array(
    'post_type'     =>'post',
    'post_status'   =>'publish',
    'posts_per_page'  => $perpage,
    'order'       => 'DESC',
    'orderby'     => 'date',
    'tax_query' => array(
      array(
        'taxonomy'  => 'category', 
        'field'   => 'slug',
        'terms'   => array($term_slug) 
      )
    )
  );

  $posts = get_posts($args);
  if( $posts ) {
    ob_start();
    $count = count($posts);
    if($count>2) {
      $items = array_chunk($posts,2);
      $n=1; foreach($items as $entries) { ?>
        <?php if ($n==1) { ?><div id="c-feat-post-left" class="term_<?php echo $term_slug; ?>"><?php } ?>
        <div class="c-feat-post-group <?php echo ($n==1) ? 'left':'right'; ?>">
          <?php foreach ($entries as $e) { 
            $id = $e->ID;
            $thumbId = get_post_thumbnail_id($id);
            $img = wp_get_attachment_image_src($thumbId,'full');
            $bg = ($img) ? ' style="background-image:url('.$img[0].')"':'';
            $permalink = get_permalink($id);
            $post_title = $e->post_title;
          ?>
          <article class="c-feat-post <?php echo ($img) ? 'hasImage':'noImage'; ?>">
            <a href="<?php echo $permalink ?>" class="c-pagelink">
              <div class="c-image"<?php echo $bg ?>>
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/image-resizer.png" alt="" class="helper">
              </div>
              <div class="c-post-title">
                <h3><?php echo $post_title ?></h3>
              </div>
            </a>
          </article>
          <?php } ?>
        </div>
        <?php if ($n==1) { ?></div><?php } ?>
      <?php
        $n++;
      } ?>
      <script>
      jQuery(document).ready(function($){
        if( $('#c-feat-post-right').length && $('.c-feat-post-group.right').length ) {
          $('.c-feat-post-group.right').appendTo('#c-feat-post-right');
        }
      });
      </script>
    <?php }
    $output = ob_get_contents();
    ob_end_clean();
  }
  
  // echo "<pre>";
  // print_r($items);
  // echo "</pre>";

  return $output;
}

add_shortcode( 'get_posts_right', 'custom_category_posts_right_func' );
function custom_category_posts_right_func( $atts ) {
  $a = shortcode_atts( array(
    'category' => 'things-to-do'
  ), $atts );
  $term_slug = ( isset($a['category']) && $a['category'] ) ? $a['category'] : 'uncategorized';

  $output = '';
  ob_start();
  echo '<div id="c-feat-post-right" class="term_'.$term_slug.'"></div>';
  $output = ob_get_contents();
  ob_end_clean();
  return $output;
}




