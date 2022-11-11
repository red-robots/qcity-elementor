<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define('THEMEURI',get_template_directory_uri() . '/');

/*-------------------------------------
  Custom client login, link and title.
---------------------------------------*/
function my_custom_loginlogo() { 
  $logo_url = THEMEURI . 'assets/images/logo.png';
  if($logo_url) { ?>
  <style type="text/css">
    body.login {
      background-color: #D6F8F1;
    }
    body.login div#login h1 a {
      background-image: url(<?php echo $logo_url; ?>);
      background-size: contain;
      width: 100%;
      height: 100px;
      margin-bottom: 10px;
    }
    .login #backtoblog, .login #nav {
      text-align: center;
    }
    body.login #backtoblog a, 
    body.login #nav a {
      color: #157394;
      transition: all ease .3s;
    }
    body.login #backtoblog a:hover,
    body.login #nav a:hover {
      color: #75c529;
    }
    body.login form {
      border: none;
      border-radius: 0;
    }
    body.login #login form p.submit {
      display: block;
      width: 100%;
    }
    body.login #login form p.submit input.button-primary {
      display: block;
      width: 100%;
      text-align: center;
      margin-top: 15px;
    }
    body.login.wp-core-ui .button-primary {
      background: #bb8c20;
      border-color: #b27f0a;
      font-weight: bold;
      text-transform: uppercase;
      transition: all ease .3s;
    }
    body.login.wp-core-ui .button-primary:hover {
      background: #d29a1c;
    }
    .login .message {
      border-left: 4px solid #bb8c20!important;
    }
  </style>
<?php }
}
add_action( 'login_enqueue_scripts', 'my_custom_loginlogo' );

// Change Link
function loginpage_customlink() {
  return get_site_url();
}
add_filter('login_headerurl','loginpage_customlink');

function customlogin_logo_url_title() {
    return get_bloginfo('name');
}
add_filter( 'login_headertitle', 'customlogin_logo_url_title' );


/*-------------------------------------
  Adds Options page for ACF.
---------------------------------------*/
if( function_exists('acf_add_options_page') ) {
  acf_add_options_page();
  // acf_add_options_sub_page(array(
  //   'page_title'    => 'Sponsors Options',
  //   'menu_title'    => 'Sponsors Options',
  //   'parent_slug'   => 'edit.php?post_type=sponsors'
  // ));
}

function bellaworks_body_classes( $classes ) {
    // Adds a class of group-blog to blogs with more than 1 published author.
    if ( is_multi_author() ) {
        $classes[] = 'group-blog';
    }

    // Adds a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }

    if ( is_front_page() || is_home() ) {
      $classes[] = 'homepage';
    } else {
      $classes[] = 'subpage';
      if( is_singular() || is_page() ) {
        global $post;
        $classes[] = $post->post_name;
      }
    }

    $browsers = ['is_iphone', 'is_chrome', 'is_safari', 'is_NS4', 'is_opera', 'is_macIE', 'is_winIE', 'is_gecko', 'is_lynx', 'is_IE', 'is_edge'];
    $classes[] = join(' ', array_filter($browsers, function ($browser) {
        return $GLOBALS[$browser];
    }));

    return $classes;
}
add_filter( 'body_class', 'bellaworks_body_classes' );


add_action( 'wp_ajax_nopriv_getPostEventMeta', 'getPostEventMeta' );
add_action( 'wp_ajax_getPostEventMeta', 'getPostEventMeta' );
function getPostEventMeta() {
  if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // $permalink = $_POST['permalink'];
    // $permalink = $_POST['permalink'];
    // $postid = ($permalink) ? url_to_postid($permalink) : '';
    $post = '';
    $event = '';
    $postid = $_POST['postid'];
    if($postid) {
      $taxonomy = 'tribe_events_cat';
      $post = get_post($postid);
      $terms = get_the_terms($post,$taxonomy);
      $event = tribe_get_event($post);
      if($event) {
        $event->monthNameFull = date('F',strtotime($event->start_date));
        $event->monthNameShort = date('M',strtotime($event->start_date));
        $event->monthNameDate = date('F jS',strtotime($event->start_date));
        $event->dateNum = date('d',strtotime($event->start_date));
        $schedule = (isset($event->plain_schedule_details) && $event->plain_schedule_details) ? $event->plain_schedule_details : '';
        $plain_schedule_info = '';
        if($schedule) {
          $parts = explode('@',$schedule);
          $plain_schedule_info = (isset($parts[1]) && $parts[1]) ? trim(preg_replace('/\s+/', ' ', $parts[1])): '';
        }
        $event->eventHours = $plain_schedule_info;
      }

      if($terms) {
        foreach($terms as $k=>$t) {
          $slug = $t->slug;
          if($slug=='featured') {
            unset($terms[$k]);
          }
        }
      }
      $post->categories = ($terms) ? array_values($terms) : '';
    }
    $response['data'] = $post;
    $response['event'] = $event;
    echo json_encode($response);
  }
  else {
    header("Location: ".$_SERVER["HTTP_REFERER"]);
  }
  die();
}


function removeFormatPhoneNumber($string) {
    if(empty($string)) return '';
    $append = '';
    if (strpos($string, '+') !== false) {
        $append = '+';
    }
    $string = preg_replace("/[^0-9]/", "", $string );
    $string = preg_replace('/\s+/', '', $string);
    return $append.$string;
}

function formatPhoneNumber($str) {
  $data = removeFormatPhoneNumber($str);
  if(  preg_match( '/^\+\d(\d{3})(\d{3})(\d{4})$/', $data,  $matches ) ) {
    $result = $matches[1] . '-' .$matches[2] . '-' . $matches[3];
    return $result;
  }
}

function getFullURL($removeParam=null) {
  $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  if($removeParam) {
    $actual_link = preg_replace('~(\?|&)'.$removeParam.'=[^&]*~', '$1', $actual_link);
    $actual_link = rtrim($actual_link, "&");
  }
  return $actual_link;
}

function qct_my_custom_admin_head() { 
$successURL = get_site_url() . '/listing-confirmation/'; 
$eventSuccess = get_site_url() . '/event-confirmation/'; ?>
<script>
var params={};location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){params[k]=v});
var gdSuccessURL = '<?php echo $successURL; ?>';
</script>
<?php
}
add_action( 'wp_footer', 'qct_my_custom_admin_head' );

add_action('tribe_events_community_before_event_submission_page_template', function() {
  if ( isset( $_POST[ 'community-event' ] ) ) {
    // The url to redirect to
    $eventSuccess = get_site_url() . '/event-confirmation/'; 
    //$url = "/event-confirmation";

    if ( wp_redirect( $eventSuccess ) ) {
        exit;
    }
  }
} );


function gd_snippet_ajax_save_post_message( $message, $post_data ) {

  if( isset($post_data['action']) && $post_data['action']=='geodir_save_post' ) {
    $post_type = $post_data['post_type'];
    $confirm['gd_businesses'] = get_site_url() . '/business-listing-confirmation/'; 
    $confirm['gd_churches'] = get_site_url() . '/church-listing-confirmation/'; 
    $confirm['gd_schools'] = get_site_url() . '/school-listing-confirmation/'; 

    $redirect_to_js = '';
    $message = '';

    ob_start();
    if( isset($confirm[$post_type]) && $post_data['ID']) { 
      $redirectURL = $confirm[$post_type]; ?>
      <script type="text/javascript">
        document.getElementsByClassName('gd-notification')[0].style.display = 'none';
        window.location = '<?php echo $redirectURL; ?>';
      </script>
      <?php
    }
    $redirect_to_js = ob_get_contents();
    ob_end_clean();
    $message .= trim($redirect_to_js);
    return $message;
  }
}
add_filter( 'geodir_ajax_save_post_message', 'gd_snippet_ajax_save_post_message', 100, 2 );


add_action( 'wp_ajax_nopriv_verifyIfCommentExist', 'verifyIfCommentExist' );
add_action( 'wp_ajax_verifyIfCommentExist', 'verifyIfCommentExist' );
function verifyIfCommentExist() {
  if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $commentID = $_POST['commentid'];
    global $wpdb;
    $query = "SELECT comment_ID,comment_author,comment_content FROM " . $wpdb->prefix . "comments WHERE comment_ID=".$commentID;
    $result = $wpdb->get_row($query);
    $response['result'] = ($result) ? $result : '';
    echo json_encode($response);
  } else {
    header("Location: ".$_SERVER["HTTP_REFERER"]);
  }
  die();
}

add_shortcode( 'gd_custom_address', 'gd_custom_address_func' );
function gd_custom_address_func( $atts ) {
  $full_address = '';
  if ( is_singular() ) {
    global $post;
    $post_id = $post->ID;
    $street = get_post_meta($post_id,"geodir_street", true );
    $city = get_post_meta($post_id,"geodir_city", true );
    $state = get_post_meta($post_id,"geodir_region", true );
    $zip = get_post_meta($post_id,"geodir_zip", true );

    $a = shortcode_atts( array(
      'state' => 'short',
    ), $atts );

    if( isset($a['state']) && $a['state']=='default' ) {
      //just show whatever was entered complete name or abbreviation
    } else {
      if($state) {
        $new_str = preg_replace("/\s+/", "", $state);
        if( strlen($new_str) > 3 ) {
          $state = gdConvertState($state);
        }
      }
    }

    $arrs = array_filter(array($street,$city,$state));
    if( $arrs ) {
      $full_address .= implode(', ',$arrs);
    }
    if($full_address) {
      $full_address .= ' ' . $zip;
    }
  }
  return $full_address;
}


function gdConvertState($name) {
   $states = array(
      array('name'=>'Alabama', 'abbr'=>'AL'),
      array('name'=>'Alaska', 'abbr'=>'AK'),
      array('name'=>'Arizona', 'abbr'=>'AZ'),
      array('name'=>'Arkansas', 'abbr'=>'AR'),
      array('name'=>'California', 'abbr'=>'CA'),
      array('name'=>'Colorado', 'abbr'=>'CO'),
      array('name'=>'Connecticut', 'abbr'=>'CT'),
      array('name'=>'Delaware', 'abbr'=>'DE'),
      array('name'=>'Florida', 'abbr'=>'FL'),
      array('name'=>'Georgia', 'abbr'=>'GA'),
      array('name'=>'Hawaii', 'abbr'=>'HI'),
      array('name'=>'Idaho', 'abbr'=>'ID'),
      array('name'=>'Illinois', 'abbr'=>'IL'),
      array('name'=>'Indiana', 'abbr'=>'IN'),
      array('name'=>'Iowa', 'abbr'=>'IA'),
      array('name'=>'Kansas', 'abbr'=>'KS'),
      array('name'=>'Kentucky', 'abbr'=>'KY'),
      array('name'=>'Louisiana', 'abbr'=>'LA'),
      array('name'=>'Maine', 'abbr'=>'ME'),
      array('name'=>'Maryland', 'abbr'=>'MD'),
      array('name'=>'Massachusetts', 'abbr'=>'MA'),
      array('name'=>'Michigan', 'abbr'=>'MI'),
      array('name'=>'Minnesota', 'abbr'=>'MN'),
      array('name'=>'Mississippi', 'abbr'=>'MS'),
      array('name'=>'Missouri', 'abbr'=>'MO'),
      array('name'=>'Montana', 'abbr'=>'MT'),
      array('name'=>'Nebraska', 'abbr'=>'NE'),
      array('name'=>'Nevada', 'abbr'=>'NV'),
      array('name'=>'New Hampshire', 'abbr'=>'NH'),
      array('name'=>'New Jersey', 'abbr'=>'NJ'),
      array('name'=>'New Mexico', 'abbr'=>'NM'),
      array('name'=>'New York', 'abbr'=>'NY'),
      array('name'=>'North Carolina', 'abbr'=>'NC'),
      array('name'=>'North Dakota', 'abbr'=>'ND'),
      array('name'=>'Ohio', 'abbr'=>'OH'),
      array('name'=>'Oklahoma', 'abbr'=>'OK'),
      array('name'=>'Oregon', 'abbr'=>'OR'),
      array('name'=>'Pennsylvania', 'abbr'=>'PA'),
      array('name'=>'Rhode Island', 'abbr'=>'RI'),
      array('name'=>'South Carolina', 'abbr'=>'SC'),
      array('name'=>'South Dakota', 'abbr'=>'SD'),
      array('name'=>'Tennessee', 'abbr'=>'TN'),
      array('name'=>'Texas', 'abbr'=>'TX'),
      array('name'=>'Utah', 'abbr'=>'UT'),
      array('name'=>'Vermont', 'abbr'=>'VT'),
      array('name'=>'Virginia', 'abbr'=>'VA'),
      array('name'=>'Washington', 'abbr'=>'WA'),
      array('name'=>'West Virginia', 'abbr'=>'WV'),
      array('name'=>'Wisconsin', 'abbr'=>'WI'),
      array('name'=>'Wyoming', 'abbr'=>'WY'),
      array('name'=>'Virgin Islands', 'abbr'=>'V.I.'),
      array('name'=>'Guam', 'abbr'=>'GU'),
      array('name'=>'Puerto Rico', 'abbr'=>'PR')
   );

   $return = false;   
   $strlen = strlen($name);

   foreach ($states as $state) :
      if ($strlen < 2) {
         return false;
      } else if ($strlen == 2) {
         if (strtolower($state['abbr']) == strtolower($name)) {
            $return = $state['name'];
            break;
         }   
      } else {
         if (strtolower($state['name']) == strtolower($name)) {
            $return = strtoupper($state['abbr']);
            break;
         }         
      }
   endforeach;
   
   return $return;
} 

/* additional scripts inside <head>  */
add_action('wp_head','front_wp_head_custom_scripts');
function front_wp_head_custom_scripts() { 
  $mobileLogo =  get_field('sitelogo_mobile','option'); 
  $logo_black =  get_field('logo_black','option'); 
  $logo_black_url = ( isset($logo_black['url']) && $logo_black['url'] ) ? $logo_black['url'] : '';
  $mobileLogoURL = ( isset($mobileLogo['url']) && $mobileLogo['url'] ) ? $mobileLogo['url'] : '';
  if($mobileLogo && is_numeric($mobileLogo)) {
    $mobileLogoURL = wp_get_attachment_url($mobileLogo);
  }
?>
<script type="text/javascript">
  var qcitySiteURL = '<?php echo get_site_url() ?>';
  var siteThemeURL='<?php echo get_stylesheet_directory_uri() ?>';
  var logoMobile = '<?php echo $mobileLogoURL ?>';
  var logoBlack = '<?php echo get_stylesheet_directory_uri() ?>/assets/images/logo.png';
  <?php if ( is_singular('tribe_events') ) { ?>
  var geodir_params='';
  <?php } ?>
</script>
<?php }


/* Obfuscate email address */
function extract_emails_from($string) {
  preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $string, $matches);
  return $matches[0];
}

function email_obfuscator($string) {
  $output = '';
  if($string) {
      $emails_matched = ($string) ? extract_emails_from($string) : '';
      if($emails_matched) {
          foreach($emails_matched as $em) {
              $encrypted = antispambot($em,1);
              $replace = 'mailto:'.$em;
              $new_mailto = 'mailto:'.$encrypted;
              $string = str_replace($replace, $new_mailto, $string);
              $rep2 = $em.'</a>';
              $new2 = antispambot($em).'</a>';
              $string = str_replace($rep2, $new2, $string);
          }
      }
      $string = apply_filters('the_content',$string);
  }
  return $string;
}


function shortenText($string, $limit, $break=".", $pad="...") {
  // return with no change if string is shorter than $limit
  if(strlen($string) <= $limit) return $string;

  // is $break present between $limit and the end of the string?
  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
    if($breakpoint < strlen($string) - 1) {
      $string = substr($string, 0, $breakpoint) . $pad;
    }
  }

  return $string;
}


add_action('init', 'js_custom_init', 1);
function js_custom_init() {
    $post_types = array(
      array(
        'post_type' => 'sponsors',
        'menu_name' => 'Sponsors',
        'plural'    => 'Sponsors',
        'single'    => 'Sponsor',
        'menu_icon' => 'dashicons-megaphone',
        'menu_position'=> 8,
        'supports'  => array('title')
      )
    );
    
    if($post_types) {
        foreach ($post_types as $p) {
            $p_type = ( isset($p['post_type']) && $p['post_type'] ) ? $p['post_type'] : ""; 
            $single_name = ( isset($p['single']) && $p['single'] ) ? $p['single'] : "Custom Post"; 
            $plural_name = ( isset($p['plural']) && $p['plural'] ) ? $p['plural'] : "Custom Post"; 
            $menu_name = ( isset($p['menu_name']) && $p['menu_name'] ) ? $p['menu_name'] : $p['plural']; 
            $menu_icon = ( isset($p['menu_icon']) && $p['menu_icon'] ) ? $p['menu_icon'] : "dashicons-admin-post"; 
            $supports = ( isset($p['supports']) && $p['supports'] ) ? $p['supports'] : array('title','editor','custom-fields','thumbnail'); 
            $taxonomies = ( isset($p['taxonomies']) && $p['taxonomies'] ) ? $p['taxonomies'] : array(); 
            $parent_item_colon = ( isset($p['parent_item_colon']) && $p['parent_item_colon'] ) ? $p['parent_item_colon'] : ""; 
            $menu_position = ( isset($p['menu_position']) && $p['menu_position'] ) ? $p['menu_position'] : 20; 
            
            if($p_type) {
                
                $labels = array(
                    'name' => _x($plural_name, 'post type general name'),
                    'singular_name' => _x($single_name, 'post type singular name'),
                    'add_new' => _x('Add New', $single_name),
                    'add_new_item' => __('Add New ' . $single_name),
                    'edit_item' => __('Edit ' . $single_name),
                    'new_item' => __('New ' . $single_name),
                    'view_item' => __('View ' . $single_name),
                    'search_items' => __('Search ' . $plural_name),
                    'not_found' =>  __('No ' . $plural_name . ' found'),
                    'not_found_in_trash' => __('No ' . $plural_name . ' found in Trash'), 
                    'parent_item_colon' => $parent_item_colon,
                    'menu_name' => $menu_name
                );
            
            
                $args = array(
                    'labels' => $labels,
                    'public' => true,
                    'publicly_queryable' => true,
                    'show_ui' => true, 
                    'show_in_menu' => true, 
                    'show_in_rest' => true,
                    'query_var' => true,
                    'rewrite' => true,
                    'capability_type' => 'post',
                    'has_archive' => false, 
                    'hierarchical' => false, // 'false' acts like posts 'true' acts like pages
                    'menu_position' => $menu_position,
                    'menu_icon'=> $menu_icon,
                    'supports' => $supports
                ); 
                
                register_post_type($p_type,$args); // name used in query
                
            }
            
        }
    }
}

// Add the custom columns to the position post type:
add_filter( 'manage_posts_columns', 'set_custom_cpt_columns' );
function set_custom_cpt_columns($columns) {
  global $wp_query;
  $query = isset($wp_query->query) ? $wp_query->query : '';
  $post_type = ( isset($query['post_type']) ) ? $query['post_type'] : '';
  
  if($post_type=='sponsors') {
    unset($columns['date']);
    $columns['title'] = __( 'Name', 'hello-elementor' );
    $columns['logo'] = __( 'Logo', 'hello-elementor' );
    $columns['date'] = __( 'Date', 'hello-elementor' );
  }
  
  return $columns;
}

//Add the data to the custom columns for the book post type:
add_action( 'manage_posts_custom_column' , 'custom_post_column', 10, 2 );
function custom_post_column( $column, $post_id ) {
  global $wp_query;
  $query = isset($wp_query->query) ? $wp_query->query : '';
  $post_type = ( isset($query['post_type']) ) ? $query['post_type'] : '';
  
  if($post_type=='sponsors') {
      switch ( $column ) {
        case 'logo' :
            $img = get_field('sponsor_logo',$post_id);
            $img_src = ($img) ? $img['sizes']['medium'] : '';
            $the_photo = '<span class="tmphoto" style="display:inline-block;width:50px;height:50px;text-align:center;overflow:hidden;">';
            if($img_src) {
               $the_photo .= '<span style="display:block;width:100%;height:100%;background:url('.$img_src.') center center no-repeat;background-size:contain;transform:scale(1.2)"></span>';
            } else {
                $the_photo .= '<i class="dashicons dashicons-format-image" style="font-size:25px;position:relative;top:13px;left: -3px;opacity:0.3;"></i>';
            }
            $the_photo .= '</span>';
            echo $the_photo;
            break;
      }
  }
}



/* To remove Gutenberg Editor */
function elem_disable_gutenberg( $can_edit, $post_type ) {

  if( isset($_GET['post_type']) && $_GET['post_type']=='sponsors' )
    $can_edit = false;

  if( ! ( is_admin() && !empty( $_GET['post'] ) ) )
    return $can_edit;

  if( get_post_type($_GET['post'])=='sponsors' )
    $can_edit = false;

  return $can_edit;

}
add_filter( 'gutenberg_can_edit_post_type', 'elem_disable_gutenberg', 10, 2 );
add_filter( 'use_block_editor_for_post_type', 'elem_disable_gutenberg', 10, 2 );


add_action('admin_head', 'site_admin_custom_styles');
function site_admin_custom_styles() { ?>
  <style type="text/css">
	  .block-editor-block-list__block.wp-block-separator.is-style-default {
		  max-width: 300px;
		  width: 40%;
		  margin: 2rem auto;
		  border-top: 2px solid #ababab;
		  border-bottom: none;
	  }
	  .block-editor-block-list__block.wp-block-separator.is-style-wide {
		  width: 100%;
		  margin: 2rem auto;
		  border-top: 2px solid #ababab;
		  border-bottom: none;
	  }
	  .acf-field label[for="acf-field_62f5d9df14b76"] {
		  display:none!important;
	  }
  </style>
<?php
}


/*====== NEW HOME LAYOUT ======*/
add_shortcode('top_section_articles', 'top_section_latest_articles');
function top_section_latest_articles() {
  return '<div class="top_section_articles"></div>';
}

function top_section_content(WP_REST_Request $request) {
  $exclude = $request->get_param( 'exclude' );
  $exclude_ids = ($exclude) ? explode(',',$exclude) : '';

  /* Get all sticky posts */
  $sticky = get_option( 'sticky_posts' );
  rsort( $sticky );

  $args = array(
    'post_type'     => 'post',
    'post_status'   => 'publish',
    'orderby'       => 'date',
    'order'         => 'DESC'
  );

  if($sticky) {
    if($exclude_ids) {
      $sticky = array_merge($sticky,$exclude_ids);
    }
    $args['posts_per_page'] = 2;
    $args['post__not_in'] = $sticky;
  } else {
    $args['posts_per_page'] = 3;
    if($exclude_ids) {
      $args['post__not_in'] = $exclude_ids;
    }
  }



  $postIDs = array();
  $posts = get_posts($args);
  $output = '';
  $placeholder = get_stylesheet_directory_uri() . '/assets/images/rectangle.png';
  ob_start();
  if($sticky) {
    $stickyPost = get_post($sticky[0]); 
    $stickyID = $stickyPost->ID;
    $post_thumbnail_id = get_post_thumbnail_id( $stickyID );
    $img = wp_get_attachment_image_src($post_thumbnail_id,'full');
    $imgStyle = ($img) ? ' style="background-image:url('.$img[0].')"':'';
    $sticky_category = get_the_category($stickyID);
    $postIDs[$stickyID] = $stickyID; ?>

    <div data-post="<?php echo $stickyID ?>" class="sticky-post <?php echo ($img) ? 'has-image':'no-image' ?>">
      <a id="stickypost" href="<?php echo get_permalink($stickyID); ?>"<?php echo $imgStyle ?>>
        <figure class="hide-desktop" <?php echo $imgStyle ?>>
          <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
        </figure>
        
        <span class="caption">
          <?php if ($sticky_category) { ?>
          <span class="category"><b><?php echo $sticky_category[0]->name ?></b></span> 
          <?php } ?>
          <span class="title"><b><?php echo $stickyPost->post_title ?></b></span>
        </span>
      </a>
    </div>

    <?php if ($posts) { ?>
    <div class="latest-posts-blocks">
      <?php foreach ($posts as $post) { 
        $postID = $post->ID;
        $thumbnailID = get_post_thumbnail_id( $postID );
        $img = wp_get_attachment_image_src($thumbnailID,'full');
        $imgStyle = ($img) ? ' style="background-image:url('.$img[0].')"':'';
        $category = get_the_category($postID);
        $postIDs[$postID] = $postID;
        ?>
        <div data-post="<?php echo $postID ?>" class="post <?php echo ($img) ? 'has-image':'no-image' ?>">
          <div class="inner">
            <a href="<?php echo get_permalink($post->ID) ?>" class="postlink"<?php echo $imgStyle ?>>
              <figure class="hide-desktop" <?php echo $imgStyle ?>>
                <img src="<?php echo $placeholder ?>" alt="">
              </figure>
              <span class="caption">
                <?php if ($category) { ?>
                <span class="category"><b><?php echo $category[0]->name ?></b></span> 
                <?php } ?>
                <span class="title"><b><?php echo $post->post_title ?></b></span>
              </span>
            </a>
          </div>
        </div>
      <?php } ?>
    </div>
    <?php } ?>
  
    <?php
  } else { ?>

    <?php if ($posts) { ?>

        <!-- STICKY POST -->
        <?php 
          $sticky = $posts[0]; 
          $sticky_postID = $sticky->ID;
          $sticky_thumbnailID = get_post_thumbnail_id( $sticky_postID );
          $sticky_img = wp_get_attachment_image_src($sticky_thumbnailID,'full');
          $imgStyle = ($sticky_img) ? ' style="background-image:url('.$sticky_img[0].')"':'';
          $sticky_category = get_the_category($sticky_postID);
          $postIDs[$sticky_postID] = $sticky_postID;
        ?>
        <div data-post="<?php echo $sticky_postID ?>" class="sticky-post <?php echo ($sticky_img) ? 'has-image':'no-image' ?>">
          <a id="stickypost" href="<?php echo get_permalink($sticky_postID); ?>"<?php echo $imgStyle ?>>
            <figure class="hide-desktop" <?php echo $imgStyle ?>>
              <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
            </figure>
            <span class="caption">
              <?php if ($sticky_category) { ?>
              <span class="category"><b><?php echo $sticky_category[0]->name ?></b></span> 
              <?php } ?>
              <span class="title"><b><?php echo $sticky->post_title ?></b></span>
            </span>
          </a>
        </div>

        <div class="latest-posts-blocks">
        <?php unset($posts[0]);
          foreach ($posts as $post) { 
            $postID = $post->ID;
            $thumbnailID = get_post_thumbnail_id( $postID );
            $img = wp_get_attachment_image_src($thumbnailID,'full');
            $imgStyle = ($img) ? ' style="background-image:url('.$img[0].')"':'';
            $category = get_the_category($postID); 
            $postIDs[$postID] = $postID;
            ?>
            <div data-post="<?php echo $postID ?>" class="post <?php echo ($img) ? 'has-image':'no-image' ?>">
              <div class="inner">
                <a href="<?php echo get_permalink($post->ID) ?>" class="postlink"<?php echo $imgStyle ?>>
                  <figure class="hide-desktop" <?php echo $imgStyle ?>>
                    <img src="<?php echo $placeholder ?>" alt="">
                  </figure>
                  <span class="caption">
                    <?php if ($category) { ?>
                    <span class="category"><b><?php echo $category[0]->name ?></b></span> 
                    <?php } ?>
                    <span class="title"><b><?php echo $post->post_title ?></b></span>
                  </span>
                </a>
              </div>
            </div>
          <?php } ?>
        </div>

    <?php } ?>

  <?php }

  $output = ob_get_contents();
  ob_end_clean();

  $respond['output'] = $output;
  return $respond;
}


add_shortcode('single_post', 'get_single_post_shortcode');
function get_single_post_shortcode($atts) {
  $a = shortcode_atts( array(
    'id' => '',
  ), $atts );
  $id = $a['id'];
  return '<div data-post="'.$id.'" id="single-post-'.$id.'" class="single-post-restapi"></div>';
}


function get_single_post_func(WP_REST_Request $request) {
  $output = '';
  $id = $request->get_param( 'pid' );
  if( $post = get_post($id) ) { 
    ob_start(); 
    $category = get_the_category($id);
    $excerpt = get_the_excerpt($id);
    //$excerpt = ($excerpt) ? shortenText($excerpt,100,'.','...') : '';
    $thumbnailID = get_post_thumbnail_id($id);
    $img = wp_get_attachment_image_src($thumbnailID,'full');
    $termlink = ($category) ? get_term_link( $category[0], 'category') : '';
    $imgStyle = ($img) ? ' style="background-image:url('.$img[0].')"':'';
    $placeholder = get_stylesheet_directory_uri() . '/assets/images/rectangle-lg.png'; ?>
    <div data-post="<?php echo $id ?>" class="home-single-post-block <?php echo ($img) ? 'has-image':'no-image' ?>">
      <div class="inner">
        <figure<?php echo $imgStyle ?>>
          <img src="<?php echo $placeholder ?>" alt="">
        </figure>
        <div class="text">
          <?php if ($category) { ?>
          <a class="category" href="<?php echo $termlink ?>"><span><?php echo $category[0]->name ?></span></a>  
          <?php } ?>
          <h3 class="posttitle"><a href="<?php echo get_permalink($id) ?>"><?php echo $post->post_title ?></a></h3>
          <?php if ($excerpt) { ?>
          <div class="excerpt"><a href="<?php echo get_permalink($id) ?>"><?php echo $excerpt ?></a></div> 
          <?php } ?>
        </div>
      </div>
    </div>
  <?php }

  $output = ob_get_contents();
  ob_end_clean();

  $respond['output'] = $output;
  return $respond;
}

function store_existing_posts(WP_REST_Request $request) {
  $ids = $request->get_param( 'pids' );
  $upload = wp_upload_dir();
  $filepath = $upload['basedir'];
  $filepath = $filepath . '/home-posts.json';
  $post_ids = ($ids) ? explode(',',$ids) : '[]';
  $content = ($post_ids) ? json_encode(array_unique($post_ids)) : '';
  $fp = fopen($filepath,"wb");
  fwrite($fp,$content);
  fclose($fp);
  return $ids;
}


/* Get Latest Post */
add_shortcode('show_recent_posts', 'get_recent_posts_shortcode');
function get_recent_posts_shortcode() {
  return '<div class="recent-posts-restapi"></div>';
}

function get_recent_posts_func(WP_REST_Request $request) {
  //$exclude_ids = $request->get_param( 'exclude' );
  // $exclude = @file_get_contents(get_site_url() . '/wp-content/uploads/home-posts.json');
  // $exclude_ids = ($exclude) ? json_decode($exclude,true) : '';
  $pg = $request->get_param( 'pg' );
  $perpage = $request->get_param( 'perpage' );
  $exclude = $request->get_param( 'exclude' );
  $exclude_ids = ($exclude) ? explode(',',$exclude) : '';
  $paged = ($pg) ? $pg : 1;
  $posts_per_page = ($perpage) ? $perpage  : 5;
  $args = array(
    'posts_per_page'  => $posts_per_page,
    'paged'           => $pg,
    'post_type'       => 'post',
    'post_status'     => 'publish',
    'orderby'         => 'date',
    'order'           => 'DESC',
  );

  if($exclude_ids) {
    $args['post__not_in'] = $exclude_ids;
  }

  $posts = new WP_Query($args);
  $total_pages = 0;
  $output = '';
  ob_start();
  if ( $posts->have_posts() ) { ?>
    <div class="articles-wrapper">
      <?php 
      $count = $posts->found_posts;
      $total_pages = $posts->max_num_pages;
      while ( $posts->have_posts() ) : $posts->the_post(); 
        $id = get_the_ID();
        $placeholder = get_stylesheet_directory_uri() . '/assets/images/rectangle-lg.png';
        $category = get_the_category($id);
        //$excerpt = get_the_content();
        //$excerpt = ($excerpt) ? shortenText(strip_tags($excerpt),90,'.','...') : '';
        $excerpt = get_the_excerpt($id);
        $thumbnailID = get_post_thumbnail_id($id);
        $img = wp_get_attachment_image_src($thumbnailID,'full');
        $termlink = ($category) ? get_term_link( $category[0], 'category') : '';
        $imgStyle = ($img) ? ' style="background-image:url('.$img[0].')"':'';
      ?>
      <article id="post-<?php the_ID() ?>" class="recent-post animated fadeIn <?php echo ($img) ? 'has-image':'no-image'?>">
        <figure<?php echo $imgStyle ?>>
          <a href="<?php echo get_permalink($id) ?>"><img src="<?php echo $placeholder ?>" alt="" aria-hidden="true"></a>
        </figure>
        <div class="text">
          <?php if ($category) { ?>
          <a class="category" href="<?php echo $termlink ?>"><span><?php echo $category[0]->name ?></span></a>  
          <?php } ?>
          <h3 class="posttitle"><a href="<?php echo get_permalink($id) ?>"><?php echo get_the_title() ?></a></h3>
          <div class="postdate"><?php echo get_the_date('F j, Y') ?></div>
          <?php if ($excerpt) { ?>
          <div class="excerpt"><?php echo $excerpt ?></div> 
          <?php } ?>
        </div>
      </article>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
  <?php
  }
  $output = ob_get_contents();
  ob_end_clean();
  $response['button'] = '<div class="paginate-button"><a href="javascript:void(0)" id="morePostBtn" data-totalpages="'.$total_pages.'" data-page="1" data-records="'.$count.'">Load More</a></div>';
  $response['output'] = $output;
  return $response;
}

function myfunc_register_rest_fields(){
  register_rest_route( 'wp/v2', '/getpost/top', array(
    'methods' => 'GET',
    'callback' => 'top_section_content',
  ));
  register_rest_route( 'wp/v2', '/getpost/single', array(
    'methods' => 'GET',
    'callback' => 'get_single_post_func',
  ));
  register_rest_route( 'wp/v2', '/getpost/existing', array(
    'methods' => 'GET',
    'callback' => 'store_existing_posts',
  ));
  register_rest_route( 'wp/v2', '/getpost/recent', array(
    'methods' => 'GET',
    'callback' => 'get_recent_posts_func',
  ));
  register_rest_route( 'wp/v2', '/getpost/imagemeta', array(
    'methods' => 'GET',
    'callback' => 'get_image_metadata_func',
  ));
  register_rest_route( 'wp/v2', '/getpost/session', array(
    'methods' => 'GET',
    'callback' => 'store_data_session_func',
  ));
}
add_action('rest_api_init','myfunc_register_rest_fields');

function store_data_session_func(WP_REST_Request $request) {
  $promote = $request->get_param( 'promote' );
  $cookie_name = "promote_event";
  $cookie_value = $promote;
  setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
  return $cookie_value;
}


add_action( 'wp_enqueue_scripts', 'myfunc_styles_scripts' );
function myfunc_styles_scripts(){
  global $wp_query; 

  wp_enqueue_script( 
    'slickjs', 
    'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', 
    array(), '1.8.1', 
    true 
  );

  wp_enqueue_script( 
    'myfunc', 
    get_template_directory_uri() . '/assets/js/custom.js', 
    array(), '20220710', 
    true 
  );

  wp_localize_script( 'myfunc', 'frontajax', array(
    'jsonUrl' => rest_url('wp/v2/getpost')
  ));

}

function get_image_metadata_func(WP_REST_Request $request) {
  $output['photo_caption'] = '';
  $pid = $request->get_param( 'pid' );
  $thumbnail_id = get_post_thumbnail_id($pid);
  $data = get_post($pid);
  $thumbnail_image = get_posts(array('p' => $thumbnail_id, 'post_type' => 'attachment'));
  if ($thumbnail_image && isset($thumbnail_image[0])) {
    if (! empty($thumbnail_image[0]->post_excerpt)) {
      $caption = $thumbnail_image[0]->post_excerpt;
      //$caption = $thumbnail_image[0]->post_content;
      $output['photo_caption'] = $caption;
    }
  }
  return $output;
}


function getQcityMetroLogo() {
  global $wpdb;
  $query = "SELECT option_value FROM ".$wpdb->prefix."options WHERE option_name='options_qcity_logo_header'";
  $result = $wpdb->get_row($query);
  $logoURL = '';
  if($result) {
    $postid = ($result->option_value) ? $result->option_value : '';
    if($postid) {
      $res = $wpdb->get_row("SELECT guid FROM ".$wpdb->prefix."posts WHERE ID=".$postid);
      $logoURL = ($res) ? $res->guid : '';
    }
  }
  return $logoURL;
}

// function get_the_feature_caption() {
//   global $post;
//   $thumbnail_id = get_post_thumbnail_id($post->ID);
//   $thumbnail_image = get_posts(array('p' => $thumbnail_id, 'post_type' => 'attachment'));
//   if ($thumbnail_image && isset($thumbnail_image[0])) {
//     if (! empty($thumbnail_image[0]->post_excerpt)) {
//       $caption = $thumbnail_image[0]->post_excerpt;
//     }
//   }
//   return $caption;
// }




