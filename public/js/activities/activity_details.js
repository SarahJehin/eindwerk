(function ( window, document, $, undefined ) {

	$('.tabs .details').click(function() {
        $('.tabs div').removeClass('active');
        $('.tabs .details').addClass('active');
        $('.activity_info .sign_up').hide();
        $('.activity_info .info').show();
    });

    $('.tabs .sign_up').click(function() {
        $('.tabs div').removeClass('active');
        $('.tabs .sign_up').addClass('active');
        $('.activity_info .info').hide();
        $('.activity_info .sign_up').show();
    });

    var lightbox = false;
    $('.poster').click(function() {
        $('.lightbox_modal').fadeIn(350, function() {
            lightbox = true;
        });
    });

    $('.lightbox_modal .modal').click(function(event) {
        event.stopPropagation();
    });

    $(window).click(function() {
        //console.log(lightbox);
        close_lightbox_modal();
    });

    $('.lightbox_modal .modal .fa-times').click(function() {
        close_lightbox_modal();
    });

    $( window ).on( "keydown", function( event ) {
        //if esc key is pressed, close modal
        if(event.which == 27) {
            close_lightbox_modal();
        }
    });

    function close_lightbox_modal() {
        if(lightbox) {
            $('.lightbox_modal').fadeOut(350, function() {
                lightbox = false;
            });
        }
    }

})(window, window.document, window.jQuery);