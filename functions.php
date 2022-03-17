<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '2.5.0' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_load_textdomain', [ true ], '2.0', 'hello_elementor_load_textdomain' );
		if ( apply_filters( 'hello_elementor_load_textdomain', $hook_result ) ) {
			load_theme_textdomain( 'hello-elementor', get_template_directory() . '/languages' );
		}

		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_register_menus', [ true ], '2.0', 'hello_elementor_register_menus' );
		if ( apply_filters( 'hello_elementor_register_menus', $hook_result ) ) {
			register_nav_menus( [ 'menu-1' => __( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => __( 'Footer', 'hello-elementor' ) ] );
		}

		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_add_theme_support', [ true ], '2.0', 'hello_elementor_add_theme_support' );
		if ( apply_filters( 'hello_elementor_add_theme_support', $hook_result ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);

			/*
			 * Editor Style.
			 */
			add_editor_style( 'classic-editor.css' );

			/*
			 * Gutenberg wide images.
			 */
			add_theme_support( 'align-wide' );

			/*
			 * WooCommerce.
			 */
			$hook_result = apply_filters_deprecated( 'elementor_hello_theme_add_woocommerce_support', [ true ], '2.0', 'hello_elementor_add_woocommerce_support' );
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', $hook_result ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		$enqueue_basic_style = apply_filters_deprecated( 'elementor_hello_theme_enqueue_style', [ true ], '2.0', 'hello_elementor_enqueue_style' );
		$min_suffix          = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'hello_elementor_enqueue_style', $enqueue_basic_style ) ) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

    // wp_enqueue_style(
    //   'custom-css',
    //   get_template_directory_uri() . '/assets/css/custom.css',
    //   [],
    //   HELLO_ELEMENTOR_VERSION
    // );
	}

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_register_elementor_locations', [ true ], '2.0', 'hello_elementor_register_elementor_locations' );
		if ( apply_filters( 'hello_elementor_register_elementor_locations', $hook_result ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( is_admin() ) {
	require get_template_directory() . '/includes/admin-functions.php';
}

/**
 * If Elementor is installed and active, we can load the Elementor-specific Settings & Features
*/

// Allow active/inactive via the Experiments
require get_template_directory() . '/includes/elementor-functions.php';

/**
 * Include customizer registration functions
*/
function hello_register_customizer_functions() {
	if ( hello_header_footer_experiment_active() && is_customize_preview() ) {
		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_register_customizer_functions' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check hide title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * Wrapper function to deal with backwards compatibility.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
		} else {
			do_action( 'wp_body_open' );
		}
	}
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



if( isset($_GET['run']) && $_GET['run']) {
  $current_id = ( isset($_GET['post']) && isset($_GET['action']) ) ? $_GET['post'] : 0;
  $run = $_GET['run'];
  if($run=='churchupdate') {
    $perpage = ( isset($_GET['pgnum']) ) ? $_GET['pgnum'] : 1;
    getChurchListings($perpage);
  }
  elseif($run=='churchlist') {
    echo "<pre style='margin-left:300px;margin-top: 50px;'>";
    print_r( viewChurchRecords() );
    echo "</pre>";
  }
  elseif($run=='churchdelete') {
    echo "<pre style='margin-left:300px;margin-top: 50px;'>";
    print_r( viewChurchRecords('delete') );
    echo "</pre>";
  }
  elseif($run=='geo') {
    if ( isset($_GET['post']) && isset($_GET['action']) ) {
      $postid = $_GET['post'];
      // $test = get_post_meta($postid,'geodir_condition',true);
      // print_r($test);
      $address = get_field('address',$postid);
      //print_r($address);
    }
  }
  elseif($run=='metakey') {
    $return = getChurchMetaData($current_id);
    echo "<pre style='margin-left:300px;margin-top: 50px;'>";
    print_r($return);
    echo "</pre>";
  }
  elseif($run=='tablelist') {
    $return = getWpTableList();
    echo "<pre style='margin-left:300px;margin-top: 50px;'>";
    print_r($return);
    echo "</pre>";
  }
  //doInsertTaxonomy();
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

// function getGDCPTtable($tableName) {
//   global $wpdb;
//   $query = "SELECT * FROM ".$tableName;
//   $result = $wpdb->get_results($query);
//   return $result;
// }

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

  //echo "</pre>";
}


