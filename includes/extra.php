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
$eventSuccess = get_site_url() . '/event-confirmation/'; 
?>
<script>
var params={};location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){params[k]=v});
jQuery(document).ready(function($){
  if (window.location.href.indexOf("business-directory/add-business") > -1) {
    if( $('form[name="geodirectory-add-post"]').length ) {
      var redirectURI = '<input type="hidden" name="success_redirect" value="<?php echo $successURL; ?>">';
      $('form[name="geodirectory-add-post"]').prepend(redirectURI);
    }
  }
});
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
  $redirect_to = ( isset($post_data['success_redirect']) && $post_data['success_redirect'] ) ? $post_data['success_redirect'] : ''; // Redirect url

  ob_start();
  if($redirect_to && $post_data['ID']) { 
    //$message = '<strong>Redirecting...</strong>';
    $message = '';
  ?>
  <script type="text/javascript">
    document.getElementsByClassName('gd-notification')[0].style.display = 'none';
    window.location = '<?php echo $redirect_to; ?>';
  </script>
  <?php 
  $redirect_to_js = ob_get_contents();
  ob_end_clean();
  $message = trim($redirect_to_js);
  return $message;
  }
}
add_filter( 'geodir_ajax_save_post_message', 'gd_snippet_ajax_save_post_message', 100, 2 );

