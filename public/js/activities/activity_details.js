(function ( window, document, $, undefined ) {

	$('.tabs .details').click(function() {
        $('.tabs div').removeClass('active');
        $('.tabs .details').addClass('active');
        $('.activity_info .sign_up').hide();
        $('.activity_info .activity_details').show();
    });

    $('.tabs .sign_up').click(function() {
        $('.tabs div').removeClass('active');
        $('.tabs .sign_up').addClass('active');
        $('.activity_info .activity_details').hide();
        $('.activity_info .sign_up').show();
    });

    var lightbox = false;
    $('.poster').click(function() {
        $('#poster_modal').fadeIn(350, function() {
            lightbox = true;
        });
    });

    $('.me #sign_out').click(function() {
        $('#sign_out_modal').fadeIn(350, function() {
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

    //sign up others
    $('#sign_up_others').change(function() {
        if($('#sign_up_others:checked').length > 0) {
            $('.sign_up_others').show();
        }
        else {
            $('.sign_up_others').hide();
        }
    });


})(window, window.document, window.jQuery);

//google maps
function initMap() {
    var location = {lat: latitude, lng: longitude};
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 12,
        center: location
    });
    var marker = new google.maps.Marker({
        position: location,
        map: map
    });
}