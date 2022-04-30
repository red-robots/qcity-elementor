jQuery(document).ready(function($){

  if( $('#site-logo').length && logoMobile!==null) {
    $('#site-logo .elementor-image a').append('<img src="'+logoMobile+'" alt="" class="mobile-site-logo" />');
  } 

  /* Mobile Menu */
  if( $('#site-custom-header ul.elementor-nav-menu li.menu-item-has-children').length ) {
    $('#site-custom-header ul.elementor-nav-menu li.menu-item-has-children').each(function(){
      var parentLink = $(this).find('a.has-submenu').text();
      var mobileParentLink = '<span class="mobileParentLink">'+parentLink+'</span>';
      $(this).prepend(mobileParentLink);
    });
    $(document).on('click','.mobileParentLink',function(){
      $(this).parent().find('ul.sub-menu').slideToggle();
    });
  }

  $(document).on('click','#site-main-nav .elementor-menu-toggle',function(){
    $('body').toggleClass('mobile-menu-open');
  });

  $('body').not('.elementor-editor-active').find('#hero-caption').appendTo('#home-slider');
    
    /* THINGS TO DO section => Make image size consistent */
    if( $('.sm-post').length ) {
      $('.sm-post .uael-post__thumbnail').each(function(){
        var target = $(this);
        target.find('a').append('<img src="'+siteThemeURL+'/assets/images/image-resizer.png" alt="" class="image-resizer" />');
        if( target.find('a img').length ) {
          var imageSrc = target.find('a img').attr('src');
          target.find('a').css('background-image','url('+imageSrc+')');
        }
      });
    } 
    
    // if( $('.featured-posts-block .uael-post-wrapper').length ) {
    //   $('.featured-posts-block').each(function(){
    //     var parent = $(this);
    //     var appendToEmptyBlock = parent.find('.firstpostBlock .elementor-column-wrap .elementor-widget-wrap');
    //     var countPost = $(this).find('.featPostsBlock .uael-post-wrapper').length;
    //     if(countPost>2) {
    //       $(this).find('.featPostsBlock .uael-post-wrapper').first().appendTo(appendToEmptyBlock);
    //     } else {
    //       parent.find('.firstpostBlock').remove();
    //       parent.find('.titleBlock').css('width','100%');
    //     }
    //   });
    // }
    
    
    /* Upcoming Events Meta Data */
    $(window).on('init', function () {
      getUpcomingEventData();
    });
    
    function getUpcomingEventData() {
      if( $('#upcoming-events-section .uael-post-wrapper a').length ) {
          $('#upcoming-events-section .uael-post-wrapper .uael-post__title a').each(function(){
            var link = $(this).attr('href');
            var parent = $(this).parents('.uael-post__inner-wrap');
            //var postTitle = parent.find('.uael-post__title').text().trim();
            var postImage = ( parent.find('.uael-post__thumbnail img').length ) ? parent.find('.uael-post__thumbnail img').attr('src'):'';
            if(postImage) {
               parent.css('background-image','url('+postImage+')');
            }

            parent.find('.uael-post__datebox').load(link+' #singlePostDataInfo',function(){
              //var postid = $(this).find('.hentry').attr('id').replace('post-','');
              var postid = $(this).find('#singlePostDataInfo').attr('data-postid');
              $.ajax({
                url : ElementorProFrontendConfig.ajaxurl,
                type : 'post',
                dataType : 'json',
                data : {
                  action : 'getPostEventMeta',
                  postid : postid
                },
                success : function( response ) {
                  if(response) {
                    var data = response.data;
                    var event = response.event;
                    //var monthNameFull = event.monthNameFull;
                    var monthNameShort = event.monthNameShort;
                    var monthNameDate = event.monthNameDate;
                    var dateNum = event.dateNum;
                    var eventHours = event.eventHours;
                    var divider = (monthNameDate && eventHours) ? ' <b>|</b> ':'';
                    var dateFullStr = monthNameDate + divider + eventHours;
                    var category = (data.categories.length) ? data.categories[0].name : '';
                    var postLink = '<a href="'+link+'" class="article-link"><span class="wrap animated fadeIn">';
                        if(category) {
                          postLink += '<span class="category">'+category+'</span>';
                        }
                        postLink += '<span class="article-title">'+data.post_title+'</span>';
                        if(dateFullStr) {
                          postLink += '<span class="article-date">'+dateFullStr+'</span>';
                        }
                        postLink += '</span></a>';
                        $(postLink).appendTo(parent);
                        parent.find('.uael-post__datebox').html('<span class="eventStartDate"><span class="evtMonth">'+monthNameShort+'</span><span class="evtDate">'+dateNum+'</span></span>').addClass('animated fadeInRight show');
                  }
                },
                complete: function() {

                }
              });


            });

          });

        }
    }
    
    
    /* featured category block */
    if( $('.featured-category-block').length ) {
      $('.featured-category-block').each(function(){
        var parentWrap = $(this);
        if( parentWrap.find('.uael-post__terms').length ) {
          parentWrap.find('.uael-post__terms').append('<span class="term-link"></span>');
        }
        if( parentWrap.find('.uael-post__title a').length ) {
          var pagelink = $(this).find('.uael-post__title a').attr('href');
          parentWrap.find('.term-link').load(pagelink+' .elementor-post-info__terms-list',function(){
            var termLink = parentWrap.find('.elementor-post-info__terms-list').find('a');
            termLink.addClass('elementor-button-link elementor-button').html('<span class="elementor-button-content-wrapper"> <span class="elementor-button-icon elementor-align-icon-right"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 88.75 44.01"><defs><style>.cls-1{fill:#231f20;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><polygon class="cls-1" points="71.11 0 71.11 8.82 79.41 18.05 0 18.05 0 25.96 79.41 25.96 71.11 35.19 71.11 44.01 88.75 23.62 88.75 20.49 71.11 0"></polygon></g></g></svg>      </span> <span class="elementor-button-text">View All</span> </span>');
          });
        }
      });
    }
    
    /* Appends date with st, nd, th */
//    function numNth(n){
//      return['st','nd','rd'][((n+90)%100-10)%10-1]||'th';
//    }
    
    /* Add Leading Zero */
//    function strPAD(num, size) {
//        num = num.toString();
//        while (num.length < size) num = "0" + num;
//        return num;
//    }
    
    $(document).on('click','.caroNavBtn',function(e){
      e.preventDefault();
      var button = $(this).attr('data-button');
      $('#upcoming-events-posts').find(button).trigger('click');
    });
    
    
    /* SMOOTH ANCHOR */
    $('a[href*="#"]')
    // Remove links that don't actually link to anything
    .not('[href="#"]')
    .not('[href="#0"]')
    .click(function(event) {
      // On-page links
      if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname
      ) {
        // Figure out element to scroll to
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        // Does a scroll target exist?
        if (target.length) {
          // Only prevent default if animation is actually gonna happen
          event.preventDefault();
          $('html, body').animate({
            scrollTop: target.offset().top
          }, 1000, function() {
            // Callback after animation
            // Must change focus!
            var $target = $(target);
            $target.focus();
            if ($target.is(':focus')) { // Checking if the target was focused
              return false;
            } else {
              $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
              $target.focus(); // Set focus again
            }
          });
        }
      }
    });
    
    
    if( $('#mc_embed_signup #newsletter-options li').length && $('#newsletter-type-info ul li').length ) {
      var newsletter_types = [];
      
      $('#newsletter-options li label').each(function(){
        var parent = $(this).parent();
        var n = $(this).text().trim().replace(/\s+/g,' ');
        var type = n.trim();
        $('#newsletter-type-info ul li').each(function(){
          var str = $(this).text().trim().replace(/\s+/g,' ');
          var parts = str.split('=>');
          if(parts.length>1) {
            var k = parts[0].trim();
            if(k===type) {
              var info = '<p class="item-info">'+parts[1]+'</p>';
              parent.append(info);
            }
          }
        });
        
      });
    }

  /* homepage > giveaways section */
  /* homepage > Food & Beverage section */
  move_stuff_on_mobile_view();
  $(window).on('resize',function(){
    move_stuff_on_mobile_view();
  });
  function move_stuff_on_mobile_view() {
    if( $(window).width() < 769 ) {
      if( $('.elementor-element.elementor-element-28fd8fb').length ) {
        $('.elementor-element.elementor-element-28fd8fb').insertAfter('.elementor-element.elementor-element-a0c763e');
      }
      if( $('.stories-two-columns .story-right-box').length ) {
        $('.stories-two-columns .story-right-box').each(function(){
          var source = $(this);
          var parentDiv = $(this).parents('.stories-two-columns');
          var firstBox = parentDiv.find('.firstpostBlock');
          source.insertBefore(firstBox);
        });
      }
      if( $('#ThingsToDo_Second').length && $('#ThingsToDo_First').length ) {
        $('#ThingsToDo_Second').insertBefore('#ThingsToDo_First');
      }
      
    } else {
      if( $('.stories-two-columns .story-right-box').length ) {
        $('.stories-two-columns .story-right-box').each(function(){
          var source = $(this);
          var parentDiv = $(this).parents('.stories-two-columns');
          var firstColumn = parentDiv.find('.featured-posts-block');
          source.insertAfter(firstColumn);
        });
      }
      if( $('#ThingsToDo_Second').length && $('#ThingsToDo_First').length ) {
        $('#ThingsToDo_Second').insertAfter('#ThingsToDo_First');
      }
      
    }
  }
  

  if (window.location.href.indexOf("business-directory/add-business") > -1) {
    if( $('form[name="geodirectory-add-post"]').length ) {
      var redirectURI = '<input type="hidden" name="success_redirect" value="'+gdSuccessURL+'">';
      $('form[name="geodirectory-add-post"]').prepend(redirectURI);
    }
  }

  /* For event single post. If only 1 meta column, make it full-width */
  if( $('.flexwrap-event-info .flexcol.second').length ) {
    var countTribeMeta = $('.flexwrap-event-info .tribe-events-meta-group').length;
    if(countTribeMeta==1) {
      $('.flexwrap-event-info .tribe-events-meta-group').addClass('full-width');
    }
  }

  $('.elementor-section.video-hero').append('<img src="'+siteThemeURL+'/assets/images/video-resizer.png" class="video-resizer" />');
  $('body').not('.elementor-editor-active').find('#hero-caption .elementor-widget-wrap').appendTo('.elementor-section.video-hero');
  
  // heroCaption();
  // $(window).resize(function(){
  //   heroCaption();
  // });
  // function heroCaption() {
  //   if( $('#hero-caption').length && $('.video-hero .elementor-widget-video').length ) {
  //     // var videoHeight = $('.video-hero .elementor-widget-video').height();
  //     // var vH = videoHeight - 50;
  //     // $('#hero-caption.elementor-section').css('height',vH+'px');

  //     //$('#hero-caption.elementor-section .elementor-container.elementor-column-gap-default').append('<img src="'+siteThemeURL+'/assets/images/video-resizer.png" class="video-resizer" />');
    
      
  //   }
  // }


  $(window).scroll(function() {    
    var scroll = $(window).scrollTop();

    if (scroll >= 100) {
      $('body').addClass("page-scroll");
    } else {
      $('body').removeClass("page-scroll");
    }
  });

  

});