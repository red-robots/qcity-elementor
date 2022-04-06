<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

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


