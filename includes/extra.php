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

