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
$blurb_text = (isset($blurb) && $blurb) ? $blurb : '';
$main_position = (isset($main) && $main) ? $main : 'right';
$main_article = (isset($main) && $main) ? ' main-'.$main : ' main-right';
$category_title = (isset($custom_title) && $custom_title) ? $custom_title : '';
$termLink = '';
if(isset($category_slug) && $category_slug) { 
  if( $cat = get_category_by_slug($category_slug) ) {
    if(empty($category_title)) {
      $category_title = $cat->name;
      $termLink = get_term_link($cat);
    }
  }
}
?>

<div class="section-four-blocks special-blocks <?php echo $has_sticky.$main_article ?>">
  <div class="section-main-heading">
    <div class="titlediv-v2">
      <span class="bg" style="background-color:#FFF">
        <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
      </span>
      <div class="titleInfoWrap">
        <div class="flex-inner">
          <h2 class="elementor-heading-title"><?php echo $category_title ?></h2>
          <?php if ($blurb_text) { ?>
          <div class="blurb"><?php echo $blurb_text ?></div>    
          <?php } ?>

          <?php if ($termLink) { ?>
          <div class="elementor-button-wrapper">
            <a href="<?php echo $termLink ?>" class="elementor-button-link elementor-button elementor-size-md" role="button">
              <span class="elementor-button-content-wrapper">
                <span class="elementor-button-text">View All</span>
                <span class="elementor-button-icon elementor-align-icon-right">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 88.75 44.01"><defs><style>.cls-1{fill:#231f20;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><polygon class="cls-1" points="71.11 0 71.11 8.82 79.41 18.05 0 18.05 0 25.96 79.41 25.96 71.11 35.19 71.11 44.01 88.75 23.62 88.75 20.49 71.11 0"></polygon></g></g></svg></span>
              </span>
            </a>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>

  <div class="flex-outer-wrap">
    <div class="flexwrap">

      <?php if ( isset($records[0]) && $records[0] ) { ?>
      <div class="post-block first-block-posts">

        <?php /* TITLE BLOCK */ ?>

        <?php if ($main_position=='right') { ?>
        <div class="entry title-block-info-v2">
            <div class="titlediv-v2">
              <span class="bg" style="background-color:#FFF">
                <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
              </span>
              <div class="titleInfoWrap">
                <div class="flex-inner">
                  <h2 class="elementor-heading-title"><?php echo $category_title ?></h2>
                  <?php if ($blurb_text) { ?>
                  <div class="blurb"><?php echo $blurb_text ?></div>    
                  <?php } ?>

                  <?php if ($termLink) { ?>
                  <div class="elementor-button-wrapper">
                    <a href="<?php echo $termLink ?>" class="elementor-button-link elementor-button elementor-size-md" role="button">
                      <span class="elementor-button-content-wrapper">
                        <span class="elementor-button-text">View All</span>
                        <span class="elementor-button-icon elementor-align-icon-right">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 88.75 44.01"><defs><style>.cls-1{fill:#231f20;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><polygon class="cls-1" points="71.11 0 71.11 8.82 79.41 18.05 0 18.05 0 25.96 79.41 25.96 71.11 35.19 71.11 44.01 88.75 23.62 88.75 20.49 71.11 0"></polygon></g></g></svg></span>
                      </span>
                    </a>
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
        

        <?php $n=1; foreach ($records[0] as $p) { 
        $postid = $p->ID;
        $posttitle = $p->post_title;
        $thumbId = get_post_thumbnail_id($postid);
        $img = wp_get_attachment_image_src($thumbId,'full');
        $imgSrc = ($img) ? $img[0] : $placeholder;
        $hasImage = ($img) ? 'hasImage':'noImage';
        ?>
        <?php if ($main_position=='right') { ?>
        <div class="entry small-block <?php echo $hasImage; ?>">
          <a href="<?php echo get_permalink($postid); ?>">
            <span class="bg" style="background-image:url('<?php echo $imgSrc ?>')">
              <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
            </span>
            <span class="post-title"><?php echo $posttitle; ?></span>
          </a>
        </div>
        <?php } else { ?>

          <?php if ($n==1) { ?>
          <div class="entry small-block <?php echo $hasImage; ?>">
            <a href="<?php echo get_permalink($postid); ?>">
              <span class="bg" style="background-image:url('<?php echo $imgSrc ?>')">
                <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
              </span>
              <span class="post-title"><?php echo $posttitle; ?></span>
            </a>
          </div>
          <div class="entry title-block-info-v2">
            <div class="titlediv-v2">
              <span class="bg" style="background-color:#FFF">
                <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
              </span>
              <div class="titleInfoWrap">
                <div class="flex-inner">
                  <h2 class="elementor-heading-title"><?php echo $category_title ?></h2>
                  <?php if ($blurb_text) { ?>
                  <div class="blurb"><?php echo $blurb_text ?></div>    
                  <?php } ?>

                  <?php if ($termLink) { ?>
                  <div class="elementor-button-wrapper">
                    <a href="<?php echo $termLink ?>" class="elementor-button-link elementor-button elementor-size-md" role="button">
                      <span class="elementor-button-content-wrapper">
                        <span class="elementor-button-text">View All</span>
                        <span class="elementor-button-icon elementor-align-icon-right">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 88.75 44.01"><defs><style>.cls-1{fill:#231f20;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><polygon class="cls-1" points="71.11 0 71.11 8.82 79.41 18.05 0 18.05 0 25.96 79.41 25.96 71.11 35.19 71.11 44.01 88.75 23.62 88.75 20.49 71.11 0"></polygon></g></g></svg></span>
                      </span>
                    </a>
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
          <?php } else { ?>
          <div class="entry small-block <?php echo $hasImage; ?>">
            <a href="<?php echo get_permalink($postid); ?>">
              <span class="bg" style="background-image:url('<?php echo $imgSrc ?>')">
                <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
              </span>
              <span class="post-title"><?php echo $posttitle; ?></span>
            </a>
          </div> 
          <?php } ?>

        <?php } ?>

        <?php $n++; } ?>
      </div> 
      <?php } ?>


      <?php if ($lastPost) { ?>
      <div class="post-block large-photo-block last-block-post<?php echo ( isset($sticky_posts) && $sticky_posts ) ? ' sticky-post':'' ?>">
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
</div>

<?php } ?>