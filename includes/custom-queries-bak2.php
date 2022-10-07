<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


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




add_shortcode( 'upcoming_events', 'qct_upcoming_events_func' );
function qct_upcoming_events_func( $atts ) {
  $a = shortcode_atts( array(
    'show' => 10,
    'perpage' => 10,
    'offset'  => 0
  ), $atts );
  $output = '';
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
    'offset'=>'',
    'show'  => 4
  ), $atts );

  $output = '';
  $offset = ( isset($a['offset']) && $a['offset'] ) ? true : false;
  $show = ( isset($a['show']) && $a['show'] ) ? $a['show'] : 4;
  $perpage = $show;
  $term_slug = ( isset($a['category']) && $a['category'] ) ? $a['category'] : 'uncategorized';

  if($offset) {
    $perpage = $perpage + 1;
  }

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
    $count = count($posts);
    $final_posts = $posts;
    $first_post = array();
    if($offset && $count>2) {
      foreach($posts as $k=>$p) {
        if($k==0) {
          $first_post = $p;
          unset( $posts[$k] );
        } 
      }
    }
    

    ob_start();
    if($count>2) { 

      if($first_post) { 
        $fp_thumbId = get_post_thumbnail_id($first_post->ID);
        $fp_img = wp_get_attachment_image_src($fp_thumbId,'full');
        $fp_img_src = ($fp_img) ? $fp_img[0] : '';
        ?>
      <div class="latest-post-info" data-image="<?php echo $fp_img_src ?>" data-term-slug="<?php echo $term_slug ?>" data-post-id="<?php echo $first_post->ID ?>" data-post-title="<?php echo $first_post->post_title ?>" data-post-link="<?php echo get_permalink($first_post->ID) ?>"></div>
      <?php }

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
          <article data-post-id="<?php echo $id ?>" class="c-feat-post <?php echo ($img) ? 'hasImage':'noImage'; ?>">
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
        var imageResizerURL = '<?php echo get_stylesheet_directory_uri() ?>/assets/images/rectangle-lg.png';
        if( $('#c-feat-post-right').length && $('.c-feat-post-group.right').length ) {
          $('.c-feat-post-group.right').appendTo('#c-feat-post-right');
        }
        if( $('.elementor-shortcode .latest-post-info').length ) {
          $('.elementor-shortcode .latest-post-info').each(function(){
            var source = $(this);
            var parentDiv = $(this).parents('section.elementor-section');
            if( parentDiv.find('.latest-post-box').length ) {
              var imageSrc = source.attr('data-image');
              var post_title = source.attr('data-post-title');
              var post_link = source.attr('data-post-link');
              var image_style = (imageSrc) ? ' style="background-image:url('+imageSrc+')"':'';
              var latestPost = '<article class="special-latest-post"><a href="'+post_link+'" class="latest-article"'+image_style+'>';
                  latestPost += '<img src="'+imageResizerURL+'" alt="" class="helper">';
                  latestPost += '<span class="post-title"><span>'+post_title+'</span></span>';
                  latestPost += '</a></article>';
              parentDiv.find('.latest-post-box .elementor-widget-container').html(latestPost);
            }
          });
        }
      });
      </script>
    <?php }
    $output = ob_get_contents();
    ob_end_clean();
  }

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


add_shortcode( 'display_story_here', 'display_story_here_func' );
function display_story_here_func( $atts ) {
  $output = '';
  $imageResize = get_template_directory_uri() . '/assets/images/image-resizer.png';
  ob_start(); ?>
  <div class="moved-article"></div>
  <script>
    jQuery(document).ready(function($){
      var imageResizer = '<?php echo $imageResize ?>';
      $('.moved-article').each(function(){
        var source = $(this);
        var iiparentDiv = source.parents('section.elementor-section.elementor-top-section.elementor-element');
        var iiTarget = iiparentDiv.find('.elementor-widget-uael-posts');
        var iiPost = iiTarget.find('.uael-post-grid__inner .uael-post-wrapper');
        if(iiPost.length>2) {
          var firstArticle = iiPost.eq(0); /* LATEST ARTICLE */
          var secondArticle = iiPost.eq(1);
          var imageThumbBox = secondArticle.find('.uael-post__thumbnail a');
          if( imageThumbBox.find('img').not('.image-resizer').length ) {
            var imageURL = imageThumbBox.find('img').not('.image-resizer').attr('src');
            imageThumbBox.attr('style','background-image:url('+imageURL+')');
          }
          imageThumbBox.append('<img src="'+imageResizer+'" alt="" class="image-resizer">');
          $(secondArticle).appendTo(source);
          iiparentDiv.find('.elementor-widget-shortcode').addClass('top-story-placeholder');
          iiparentDiv.find('.elementor-widget-shortcode').html(source.html());
          if( iiparentDiv.find('.story-right-box').length ) {
            var bigRightBox = iiparentDiv.find('.story-right-box .elementor-widget-wrap');
            if( firstArticle.find('.uael-post__thumbnail a img').not('.image-resizer').length ) {
              var firstArticleImageURL = firstArticle.find('.uael-post__thumbnail a img').not('.image-resizer').attr('src');
              firstArticle.find('.uael-post__bg-wrap').attr('style','background-image:url('+firstArticleImageURL+')');
            }
            $(firstArticle).appendTo(bigRightBox);
          }
        }
      });
    });
  </script>
  <?php
  $output = ob_get_contents();
  ob_end_clean();
  return $output;
}


/* THINGS TO DO */
add_shortcode( 'get_post_style1', 'get_post_style1_func' );
function get_post_style1_func($atts) {
  global $post;
  $a = shortcode_atts( array(
    'category' => '',
  ), $atts );

  $output = '';
  $category_slug = (isset($a['category']) && $a['category']) ? $a['category'] : '';
  if( empty($category_slug) ) return '';

  $args = array(
    'post_type'     =>'post',
    'post_status'   => 'publish',
    'posts_per_page'=> 3,
    'tax_query' => array(
      array(
        'taxonomy'  => 'category', 
        'field'     => 'slug',
        'terms'     => array($category_slug) 
      )
    )
  );

  $posts = new WP_Query($args);
  $placeholder = THEMEURI . 'assets/images/image-resizer.png';
  ob_start();
  if ($posts->have_posts()) { $total = $posts->found_posts; ?>
  <div class="section-large-small-grid">
    <div class="flexwrap">
    <?php $i=1; while ($posts->have_posts()) : $posts->the_post(); 
      $thumbId = get_post_thumbnail_id(get_the_ID());
      $img = wp_get_attachment_image_src($thumbId,'full');
      $imgSrc = ($img) ? $img[0] : $placeholder;
      $hasImage = ($img) ? 'hasImage':'noImage';
      $firstCol = ($i==1) ? ' first':'';
      ?>

      <?php if ($total>1) { ?>

        <?php if ($i==1) { ?>
        <div class="post-block-column first">
          <div class="entry <?php echo $hasImage; ?>">
            <a href="<?php echo get_permalink(); ?>">
              <span class="bg" style="background-image:url('<?php echo $imgSrc ?>')">
                <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
              </span>
              <span class="post-title"><strong><?php echo get_the_title(); ?></strong></span>
            </a>
          </div>
        </div><div class="post-block-column second">

        <?php } else { ?>

          <div class="entry <?php echo $hasImage; ?>">
            <a href="<?php echo get_permalink(); ?>">
              <span class="bg" style="background-image:url('<?php echo $imgSrc ?>')">
                <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
              </span>
              <span class="post-title"><strong><?php echo get_the_title(); ?></strong></span>
            </a>
          </div>

        <?php } ?>
        
      <?php } else { ?>
      <div class="entry <?php echo $hasImage.$firstCol; ?>">
        <a href="<?php echo get_permalink(); ?>">
          <span class="bg" style="background-image:url('<?php echo $imgSrc ?>')">
            <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
          </span>
          <span class="post-title"><?php echo get_the_title(); ?></span>
        </a>
      </div>
      <?php } ?>

    <?php $i++; endwhile; wp_reset_postdata(); ?>

      <?php if ($total>1) { ?>
      </div> <?php //close div SECOND COLUMN ?>
      <?php } ?>
    </div>
  </div>  
  <?php }
  $output = ob_get_contents();
  ob_end_clean(); 

  return $output;
}



/* REST API 
 * URL=> https://livincharlotte.com/wp-json/wp/v2/sponsored-events?perpage=10
*/
add_action( 'rest_api_init', function () {
  // register a new endpoint
  register_rest_route( 'wp/v2', '/sponsored-events/', array(
    'methods' => 'GET',
    'callback' => 'rest_api_sponsored_events', // that calls this function
  ) );

} );

function rest_api_sponsored_events( WP_REST_Request $request ) {
  
  $perpage = ($request->get_param( 'perpage' )) ? $request->get_param( 'perpage' ) : 3;
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

  if($events) {
    foreach($events as $e) {
      $id = $e->ID;
      $attachment_id = get_post_thumbnail_id($id);
      $img = wp_get_attachment_image_src($attachment_id,'full');
      $image_info = [];
      if($img) {
        $image_info['url'] = $img[0];
        $image_info['title'] = get_the_title($attachment_id);
        $image_info['alt'] = get_post_meta($attachment_id, '_wp_attachment_image_alt', TRUE);
        $image_info['meta'] = wp_get_attachment_metadata($attachment_id);
      }
      

      $taxonomy = 'tribe_events_cat';
      $event_date['start_date_format1'] = tribe_get_start_date($id,false,'l, F d, Y');
      $event_date['start_date_format2'] = tribe_get_start_date($id,false,'l, jS F Y');
      $event_date['start_date_format3'] = tribe_get_start_date($id,false,'F d, Y | h:i a');
      $event_date['start_date_format4'] = tribe_get_start_date($id,false,'F d, Y');

      /* additional info */
      $e->pagelink = get_permalink($id);
      $e->featured_image = $image_info;
      $e->terms = get_the_terms($e,$taxonomy);
      $e->event_date = $event_date;

    }
  }

  return $events;
}


add_shortcode( 'five_blocks_posts', 'five_blocks_posts_func' );
function five_blocks_posts_func($atts) {
  $a = shortcode_atts( array(
    'category' => '',
  ), $atts );

  $output = '';
  $category_slug = (isset($a['category']) && $a['category']) ? $a['category'] : '';
  if( empty($category_slug) ) return '';

  /* Get all sticky posts */
  $sticky = get_option( 'sticky_posts' );
  $sticky_posts = array();
  if($sticky) {
    $args = array(
      'posts_per_page'=> 1,
      'ignore_sticky_posts' => 0,
      'post__in' => $sticky,
      'tax_query' => array(
          array(
            'taxonomy'  => 'category', 
            'field'     => 'slug',
            'terms'     => array($category_slug) 
          )
        )
    );
    $sticky_posts = get_posts($args);
  }
  if($sticky_posts) {

    $args2 = array(
      'post_type'     =>'post',
      'post_status'   => 'publish',
      'posts_per_page'=> 4,
      'post__not_in'  => $sticky,
      'tax_query' => array(
        array(
          'taxonomy'  => 'category', 
          'field'     => 'slug',
          'terms'     => array($category_slug) 
        )
      )
    );

    $posts = get_posts($args2);
    ob_start();
    if ($posts) { 
      include( locate_template('template-parts/5-blocks-posts.php') );
    }
    $output = ob_get_contents();
    ob_end_clean(); 

  } else {

    $args2 = array(
      'post_type'     =>'post',
      'post_status'   => 'publish',
      'posts_per_page'=> 5,
      'tax_query' => array(
        array(
          'taxonomy'  => 'category', 
          'field'     => 'slug',
          'terms'     => array($category_slug) 
        )
      )
    );

    $posts = get_posts($args2);
    $firstpost = array();
    ob_start();
    if ($posts) { 
      include( locate_template('template-parts/5-blocks-posts.php') );
    }
    $output = ob_get_contents();
    ob_end_clean(); 

  }

  return $output;
}


add_shortcode( 'four_blocks_posts', 'four_blocks_posts_func' );
function four_blocks_posts_func($atts) {
  $a = shortcode_atts( array(
    'category' => '',
  ), $atts );

  $output = '';
  $category_slug = (isset($a['category']) && $a['category']) ? $a['category'] : '';
  if( empty($category_slug) ) return '';

  /* Get all sticky posts */
  $sticky = get_option( 'sticky_posts' );
  $sticky_posts = array();
  if($sticky) {
    $args = array(
      'posts_per_page'=> 1,
      'ignore_sticky_posts' => 0,
      'post__in' => $sticky,
      'tax_query' => array(
          array(
            'taxonomy'  => 'category', 
            'field'     => 'slug',
            'terms'     => array($category_slug) 
          )
        )
    );
    $sticky_posts = get_posts($args);
  }
  if($sticky_posts) {

    $args2 = array(
      'post_type'     =>'post',
      'post_status'   => 'publish',
      'posts_per_page'=> 3,
      'post__not_in'  => $sticky,
      'tax_query' => array(
        array(
          'taxonomy'  => 'category', 
          'field'     => 'slug',
          'terms'     => array($category_slug) 
        )
      )
    );

    $posts = get_posts($args2);
    ob_start();
    if ($posts) { 
      include( locate_template('template-parts/4-blocks-posts.php') );
    }
    $output = ob_get_contents();
    ob_end_clean(); 

  } else {

    $args2 = array(
      'post_type'     =>'post',
      'post_status'   => 'publish',
      'posts_per_page'=> 4,
      'tax_query' => array(
        array(
          'taxonomy'  => 'category', 
          'field'     => 'slug',
          'terms'     => array($category_slug) 
        )
      )
    );

    $posts = get_posts($args2);
    $firstpost = array();
    ob_start();
    if ($posts) { 
      include( locate_template('template-parts/4-blocks-posts.php') );
    }
    $output = ob_get_contents();
    ob_end_clean(); 

  }

  return $output;
}


function get_category_info($term_slug,$field=null) {
  global $wpdb;
  if( empty($term_slug) ) return '';
  $term_slug = trim($term_slug);
  $query = "SELECT * FROM " . $wpdb->prefix . "terms WHERE slug='".$term_slug."'";
  $result = $wpdb->get_row($query);
  if($field) {
    return ($result) ? $result->$field : '';
  } else {
    return ($result) ? $result : '';
  }
}





