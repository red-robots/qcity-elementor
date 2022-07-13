<?php  
if($posts) {
$records = array();
$count = count($posts); 
$has_sticky = ( isset($sticky_posts) && $sticky_posts ) ? ' has-sticky-post':'';
$middlePost = array();
if( isset($sticky_posts) && $sticky_posts ) {
  $middlePost = $sticky_posts[0];
  if($count>1) {
    $slice = round($count/2);
    $records = array_chunk($posts,$slice);
  } else {
    $records[] = $posts;
  } 
} else {

  if($count>1) {
    $middlePost = $posts[0];
    unset($posts[0]);
    $new_count = count($posts);
    if($new_count>1) {
      $slice = round($new_count/2);
      $records = array_chunk($posts,$slice);
    } else {
      $records[] = $posts;
    }
  } else {
    $records[] = $posts;
  }  
}

$mp_id = (isset($middlePost->ID) && $middlePost->ID) ? $middlePost->ID : '';
$mp_title = (isset($middlePost->post_title) && $middlePost->post_title) ? $middlePost->post_title : '';
$mp_thumbId = ($mp_id) ? get_post_thumbnail_id($mp_id) : '';
$mp_img = ($mp_thumbId) ? wp_get_attachment_image_src($mp_thumbId,'full') : '';
$mp_imgSrc = ($mp_img) ? $mp_img[0] : $placeholder;
$mp_hasImage = ($mp_img) ? 'hasImage':'noImage';
$placeholder = THEMEURI . 'assets/images/image-resizer.png';
?>

<div class="section-five-blocks section-posts-large-middle<?php echo $has_sticky ?>">
  <div class="flexwrap">

    <?php if ( isset($records[0]) && $records[0] ) { ?>
    <div class="postscolumn first-group">
      <?php foreach ($records[0] as $p) { 
      $postid = $p->ID;
      $posttitle = $p->post_title;
      $thumbId = get_post_thumbnail_id($postid);
      $img = wp_get_attachment_image_src($thumbId,'full');
      $imgSrc = ($img) ? $img[0] : $placeholder;
      $hasImage = ($img) ? 'hasImage':'noImage';
      ?>
      <div class="entry small-block <?php echo $hasImage; ?>">
        <a href="<?php echo get_permalink($postid); ?>">
          <span class="bg" style="background-image:url('<?php echo $imgSrc ?>')">
            <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
          </span>
          <span class="post-title"><?php echo $posttitle; ?></span>
        </a>
      </div>
      <?php } ?>
    </div> 
    <?php } ?>

    <?php if ($middlePost) { ?>
    <div class="middle-post<?php echo ( isset($sticky_posts) && $sticky_posts ) ? ' sticky-post':'' ?>">
      <div class="entry <?php echo $mp_hasImage ?>">
        <a href="<?php echo get_permalink($mp_id); ?>">
          <span class="bg" style="background-image:url('<?php echo $mp_imgSrc ?>')">
            <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
          </span>
          <span class="post-title"><span><?php echo $mp_title; ?></span></span>
        </a>
      </div>
    </div>
    <?php } ?>

    <?php if ( isset($records[1]) && $records[1] ) { ?>
    <div class="postscolumn second-group">
      <?php foreach ($records[1] as $p) { 
      $postid = $p->ID;
      $posttitle = $p->post_title;
      $thumbId = get_post_thumbnail_id($postid);
      $img = wp_get_attachment_image_src($thumbId,'full');
      $imgSrc = ($img) ? $img[0] : $placeholder;
      $hasImage = ($img) ? 'hasImage':'noImage';
      ?>
      <div class="entry small-block <?php echo $hasImage; ?>">
        <a href="<?php echo get_permalink($postid); ?>">
          <span class="bg" style="background-image:url('<?php echo $imgSrc ?>')">
            <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
          </span>
          <span class="post-title"><?php echo $posttitle; ?></span>
        </a>
      </div>
      <?php } ?>
    </div> 
    <?php } ?>
  </div>
</div>

<?php } ?>