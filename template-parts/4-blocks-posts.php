<?php  
if($posts) {
$records = array();
$count = count($posts); 
$has_sticky = ( isset($sticky_posts) && $sticky_posts ) ? ' has-sticky-post':'';
$lastPost = array();
if( isset($sticky_posts) && $sticky_posts ) {
  $lastPost = $sticky_posts[0];
  $records[] = $posts;
} else {
  if($count>1) {
    $lastPost = $posts[0];
    unset($posts[0]);
  }
  $records[] = $posts;
}

$mp_id = (isset($lastPost->ID) && $lastPost->ID) ? $lastPost->ID : '';
$mp_title = (isset($lastPost->post_title) && $lastPost->post_title) ? $lastPost->post_title : '';
$mp_thumbId = ($mp_id) ? get_post_thumbnail_id($mp_id) : '';
$mp_img = ($mp_thumbId) ? wp_get_attachment_image_src($mp_thumbId,'full') : '';
$mp_imgSrc = ($mp_img) ? $mp_img[0] : $placeholder;
$mp_hasImage = ($mp_img) ? 'hasImage':'noImage';
$placeholder = THEMEURI . 'assets/images/image-resizer.png';
?>

<div class="section-four-blocks<?php echo $has_sticky ?>">
  <div class="flexwrap">

    <?php if ( isset($records[0]) && $records[0] ) { ?>
    <div class="post-block first-block-posts">

      <?php /* TITLE BLOCK */ ?>
      <div class="entry title-block-info">
        <div class="titlediv">
          <span class="bg" style="background-color:#FFF">
            <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
          </span>
        </div>
      </div>

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


    <?php if ($lastPost) { ?>
    <div class="post-block last-block-post<?php echo ( isset($sticky_posts) && $sticky_posts ) ? ' sticky-post':'' ?>">
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

  </div>
</div>

<?php } ?>