jQuery( document ).ready( function( $ ) {
  var frame; 

  $( '#upload-profile-pic' ).on( 'click', function( e ) {
    e.preventDefault();

    // reuse the frame if it’s already open
    if ( frame ) {
      frame.open();
      return;
    }

    // create a new media frame
    frame = wp.media({
      title: 'Select Profile Picture',
      button: { text: 'Use this Picture' },
      multiple: false
    });

    // when a file is selected, grab the URL and ID and update the form
    frame.on( 'select', function() {
      var attachment = frame.state().get('selection').first().toJSON();
      // set hidden input
      $( '#profile_picture_id' ).val( attachment.id );
      // swap out the <img>
      $( '.profile-pic' ).attr( 'src', attachment.url );
      // add the remove button if missing
      if ( ! $( '#remove-profile-pic' ).length ) {
        $( '.profile-pic-container' )
          .append('<button type="button" id="remove-profile-pic" class="remove-pic-btn">&times;</button>');
      }
    });

    frame.open();
  });

  // handle the “remove” click
  $( document ).on( 'click', '#remove-profile-pic', function() {
    $( '#profile_picture_id' ).val( '' );
    // reset to gravatar or placeholder
    var defaultAvatar = $( '#profile_picture_id' ).data('default-avatar');
    $( '.profile-pic' ).attr( 'src', defaultAvatar );
    $( this ).remove();
  });
});
