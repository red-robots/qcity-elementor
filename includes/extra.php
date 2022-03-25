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


function viewChurchRecords($action=null) {
  global $wpdb;
  $wp_table_churches = $wpdb->prefix."geodir_gd_churches_detail";
  if($action=='delete') {
    $query = "SELECT * FROM ".$wp_table_churches;
    $result =$wpdb->get_results($query);
    $deletedItems = '';
    if($result) {
      foreach($result as $row) {
        $id = $row->post_id;
        $wpdb->delete( $wp_table_churches, array( 'post_id' => $id ) );
        $deletedItems .= $id . '<br>';
      }
    }
    $output = 'Deleted Entries:<br>' . $deletedItems;
    return $output;
  } else {
    $query = "SELECT * FROM ".$wp_table_churches;
    $result =$wpdb->get_results($query);
    return $result;
  }
  
}


function getChurchMetaData($postID) {
  global $wpdb;
  $query = "SELECT * FROM ".$wpdb->prefix."postmeta WHERE post_id=".$postID;
  $result = $wpdb->get_results($query);
  return $result;
}

function getChurchListings($perpage=1) {
  global $wpdb;
  $args = array(
    'post_type'       => 'church_listing',
    'post_status'     => array('publish','pending'),
    'posts_per_page'  => $perpage,
  );
  $result = get_posts($args);

  $wp_post_table = $wpdb->prefix.'posts';

  //echo "<pre style='margin-left:300px;margin-top: 50px;'>";

  if($result) {
    foreach($result as $row) {
      $postID = $row->ID;
      $taxonomy = 'denomination';
      $query = "SELECT tm.*,rel.object_id,p.ID as post_id FROM ".$wpdb->prefix."term_relationships rel, ".$wpdb->prefix."terms tm, ".$wpdb->prefix."posts p WHERE rel.term_taxonomy_id=tm.term_id AND rel.object_id=".$postID." AND rel.object_id=p.ID";
      $terms = $wpdb->get_results($query);
      $post_title = $row->post_title;
      $post_name = $row->post_name;
      $post_status = $row->post_status;
      $new = array(
        'post_title' => $post_title,
        'post_name' => $post_name,
        'post_author' => $row->post_author,
        'post_date' => $row->post_date,
        'post_status' => $post_status,
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_type' => 'gd_churches'
      );

      $post_terms = array();

      if($terms) {
        foreach($terms as $term) {
          if( $cat = getChurchTerms($term->slug) ) {
            $termId = $cat->term_id;
            $post_terms[] = $termId;
          }
        }
      }

      // if($post_terms) {
      //   $new['post_category'] = $post_terms;
      // }

      // $post_meta = array(
      //   'geodir_phone'=>get_field('phone',$postID),
      //   'geodir_website'=>get_field('website',$postID),
      //   'geodir_year_founded'=>get_field('founded',$postID),
      //   'geodir_email'=>get_field('church_contact_email',$postID),
      //   'geodir_contact_name'=>get_field('church_contact_name',$post_id),
      //   'geodir_contact_phone_number'=>get_field('church_contact_phone_number',$postID)
      // );

      // $new_post_id = wp_insert_post( $new, true );
      // if($new_post_id) {
      //   if($post_terms) {
      //     wp_set_post_categories($new_post_id,$post_terms);
      //   }
        
      //   // foreach($post_meta as $metaKey=>$metaVal) {
      //   //   geodir_save_post_meta($new_post_id,$metaKey,$metaVal);
      //   // }
      // }

      // if($post_id) {
      //   foreach($post_meta as $metaKey=>$metaVal) {
      //     add_post_meta($post_id,$metaKey,$metaVal);
      //   }
      // }

      /* Insert data */
      $wpdb->insert($wp_post_table,$new);
      $new_post_id = $wpdb->insert_id;
      
      //print_r($new_post_id);

      if($new_post_id) {
        $search_title = strtolower($post_title);
        //$search_title = str_replace("'","",$search_title);
        $geo_fields = array(
          'post_id'=>$new_post_id,
          'post_title'=>$post_title,
          '_search_title'=>$search_title,
          'post_status'=>$post_status,
          'phone'=>get_field('phone',$postID),
          'website'=>get_field('website',$postID),
          'year_founded'=>get_field('founded',$postID),
          'contact_email'=>get_field('church_contact_email',$postID),
          'pastor'=>get_field('pastor',$postID),
          'contact_name'=>get_field('church_contact_name',$postID),
          'contact_phone_number'=>get_field('church_contact_phone_number',$postID)
        );
        if($post_terms) {
          $categoriesStr = implode(",",$post_terms);
          $geo_fields['post_category'] = ','.$categoriesStr.',';
        }
        $wpdb->insert($wpdb->prefix."geodir_gd_churches_detail",$geo_fields);

        /* Insert Categories */
        if($post_terms) {
          foreach($post_terms as $tax_term_id) {
            $term_fields = array(
              'object_id'=>$new_post_id,
              'term_taxonomy_id'=>$tax_term_id
            );
            $wpdb->insert($wpdb->prefix."term_relationships",$term_fields);
          }
        }

        /* Insert Post Meta */
        // if($post_meta) {
        //   foreach($post_meta as $metaKey=>$metaVal) {
        //     $meta_fields = array(
        //       'post_id'=>$new_post_id,
        //       'meta_key'=>$metaKey,
        //       'meta_value'=>$metaVal
        //     );
        //     $wpdb->insert($wpdb->prefix."postmeta",$meta_fields);
        //   }
        // }

      }

      // echo "<pre style='margin-left:300px;margin-top: 50px;'>";
      // print_r($new);
      // echo "</pre>";
      
    }
  }

  //echo "</pre>";
}

function getChurchMetaValue($metaKey,$postID) {
  global $wpdb;
  $query = "SELECT * FROM ".$wpdb->prefix."postmeta WHERE post_id=".$postID." AND meta_key='".$metaKey."'";
  $result = $wpdb->get_row($query);
  return ($result) ? $result->meta_value : '';
}

function getChurchTerms($slug) {
  global $wpdb;
  $taxonomy = 'gd_churchescategory';
  $query = "SELECT tm.*,tax.taxonomy FROM ".$wpdb->prefix."term_taxonomy tax, ".$wpdb->prefix."terms tm WHERE tax.taxonomy='".$taxonomy."' AND tax.term_id=tm.term_id AND tm.slug='".$slug."'";
  $result = $wpdb->get_row($query);
  return ($result) ? $result : '';
}

function doInsertTaxonomy() {
  global $wpdb;
  $taxonomy = 'denomination';
  $query = "SELECT tax.taxonomy,tax.term_taxonomy_id,tax.count,tm.* FROM ".$wpdb->prefix."term_taxonomy tax, ".$wpdb->prefix."terms tm WHERE tax.taxonomy='".$taxonomy."' AND tax.term_id=tm.term_id";
  $result = $wpdb->get_results($query);
  //echo "<pre style='margin-left:300px;margin-top: 50px;'>";
  // print_r($result);
  
  if($result) {
    $nexTaxonomy = 'gd_churchescategory';
    $table1 = $wpdb->prefix.'terms';
    $table2 = $wpdb->prefix.'term_taxonomy';
    foreach($result as $row) {
      $catName = $row->name;
      $catSlug = $row->slug;
      $fields1 = array('name' => $catName, 'slug' => $catSlug);
      $wpdb->insert($table1,$fields1,null);
      $termId = $wpdb->insert_id;
      if($termId) {
        $fields2 = array('term_taxonomy_id' => $termId,'term_id' => $termId, 'taxonomy' => $nexTaxonomy);
        $wpdb->insert($table2,$fields2,null);
      }
    }
  }
}

function getGDPostData($postID,$posttype,$fieldName=null) {
  global $wpdb; 
  $tableName = $wpdb->prefix . 'geodir_' . $posttype . '_detail';
  $query = "SELECT * FROM " . $tableName . " WHERE post_id=" . $postID;
  $result = $wpdb->get_row($query);
  if($fieldName) {
    return ( isset($result->$fieldName) && $result->$fieldName ) ? $result->$fieldName : '';
  } else {
    return $result;
  }
}


function getWpTableList() {
  global $wpdb;
  $tableList = array();
  $list =$wpdb->get_results("SHOW TABLES");
  if($list) {
    foreach ($list as $tbl) {
      foreach ($tbl as $t) {       
        $tableList[] = $t;
      }
    }
  }
  return $tableList;
}


