jQuery(function($){
    // bind only once, on document
    $(document).on('click', '.qty-button', function() {
      var $btn   = $(this),
          $input = $btn.siblings('.qty'),
          step   = parseFloat( $input.attr('step') ) || 1,
          min    = parseFloat( $input.attr('min') )  || 0,
          max    = parseFloat( $input.attr('max') ),
          val    = parseFloat( $input.val() )        || 0,
          newVal = $btn.hasClass('plus') ? val + step : val - step;
  
      if ( newVal < min ) newVal = min;
      if ( max && newVal > max ) newVal = max;
  
      $input.val( newVal ).trigger('change');
    });
  });
  