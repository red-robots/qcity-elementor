<?php
/**
 * The template for displaying listing content within loops
 *
 * This template can be overridden by copying it to yourtheme/geodirectory/map-popup.php.
 *
 * HOWEVER, on occasion GeoDirectory will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.wpgeodirectory.com/article/346-customizing-templates/
 * @author  AyeCode
 * @package GeoDirectory/Templates
 * @version 1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $gd_post;
$post_id = $gd_post->ID;
$posttype = $gd_post->post_type;
$post_title = $gd_post->post_title;
$taxonomy = 'gd_businessescategory';
$terms = get_the_terms($gd_post,$taxonomy);
$category = ( isset($terms[0]) && $terms[0] ) ? $terms[0]->name : '';
$termLink = ($terms) ?  get_term_link($terms[0],$taxonomy) : '';
$fields = ['street','city','region','zip'];
$address = '';
foreach($fields as $k=>$field) {
  $comma = '';
  if($k>0) {
    $comma = ($field=='zip') ? '':', ';
  }
  $val = getGDPostData($post_id,$posttype,$field);
  if($val) {
    $address .= $comma . ' ' . getGDPostData($post_id,$posttype,$field);
  }
}
$website = getGDPostData($post_id,$posttype,'website');
$phone = getGDPostData($post_id,$posttype,'phone');
?>
<div class="gd-bubble gd-bubble-custom bsui" style="">
	<div class="gd-bubble-inside">
		<div class="geodir-bubble_desc">
      <?php if ($category) { ?>
       <div class="gd-category"><a href="<?php echo $termLink ?>" class="gdcat"><?php echo $category ?></a></div> 
      <?php } ?>
			<?php if ($terms) { ?>
        <h4 class="geodir-entry-title"><a href="<?php echo $website ?>" target="_blank"><?php echo $post_title ?></a></h4>
      <?php } else { ?>
        [gd_post_title tag='h4']
      <?php } ?>
			<div class="gd-full-address">
        <?php echo $address ?>  
        <?php if ($phone) { ?>
        <div class="gd-phone">Phone: <?php echo $phone; ?></div>
        <?php } ?>
      </div>
      <?php if ($website) { ?>
      <a href="<?php echo $website ?>" target="_blank" class="gd-website">Website</a>
      <?php } ?>
		</div>
	</div>
</div>