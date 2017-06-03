(function ( window, document, $, undefined ) {

    //image clicks
    $('.exercise .other_images .image').click(function() {
        //set this image to be the main image
        var new_main_image = $(this).find('img');
        var new_main_image_src = new_main_image.attr('src');
        var new_main_image_alt = new_main_image.attr('alt');
        $('.exercise .main_image img').attr('src', new_main_image_src);
        $('.exercise .main_image img').attr('alt', new_main_image_alt);
    });

    //make sure thumbails are in 4:3 ratio
    var image_width = parseInt($('.other_images .image').css('width'));
    console.log(image_width);
    var image_height = image_width/4*3;
    console.log(image_height);
    $('.other_images .image').css('height', image_height + 'px');

    //set iframe height
    var iframe_width = parseInt($('.video_block iframe').css('width'));
    //console.log(iframe_width);
    var ifram_height = iframe_width/17*10;
    //console.log(ifram_height);
    $('.video_block iframe').css('height', ifram_height + 'px');

    //enlarge image
    var lightbox = false;
    $('.exercise .main_image').click(function() {
        $('#exercise_image_modal img').attr('src', $('.main_image img').attr('src'));
        $('#exercise_image_modal').fadeIn(350, function() {
            lightbox = true;
        });
    });
    $('.lightbox_modal .modal').click(function(event) {
        event.stopPropagation();
    });
    $(window).click(function() {
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
    //set lightbox modal bg to full page height
    $('.lightbox_modal').css('height', $('.page_container').height() + 'px');

})(window, window.document, window.jQuery);