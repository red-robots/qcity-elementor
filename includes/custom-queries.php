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


add_shortcode( 'upcoming_events', 'qct_upcoming_events_func' );
function qct_upcoming_events_func( $atts ) {
  $a = shortcode_atts( array(
    'perpage' => 10,
    'offset'  => 0
  ), $atts );
  $res = getUpcomingEventsFunc($a['perpage'], $a['offset']);
  $output = '';
  if( isset($res['records']) && $res['records'] ) {
    ob_start();
    $records = $res['records'];
    include( locate_template('template-parts/upcoming_events.php') );
    $output = ob_get_contents();
    ob_end_clean();
  }
  return $output;
}


