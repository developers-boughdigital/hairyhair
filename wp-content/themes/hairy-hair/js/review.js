jQuery(function($){
    // 1) Hide the comment form & enable file uploads
    var reviewForm = $('#review_form_wrapper');
    reviewForm.hide();
    reviewForm.closest('form').attr('enctype','multipart/form-data');
  
    // 2) “Write A Review” click reveals & scrolls
    $('a[href="#review_form_wrapper"]').on('click', function(e){
      e.preventDefault();
      reviewForm.slideDown(300, function(){
        $('html,body').animate({ scrollTop: reviewForm.offset().top - 80 }, 600);
      });
    });
  
    // 3) AJAX Sort
    $('#sort').on('change', function(){
      var orderby = $(this).val();
      $.post( MyTheme.ajax_url, {
        action:     'mytheme_load_reviews',
        product_id: MyTheme.product_id,
        orderby:    orderby,
        paged:      1
      }, function(res){
        if ( res.success ) {
          $('#reviews-list')
            .fadeTo(200,0.5)
            .html(res.data.html)
            .fadeTo(200,1);
        }
      });
    });
  });
  