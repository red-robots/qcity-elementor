jQuery(document).ready(function($){
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
});