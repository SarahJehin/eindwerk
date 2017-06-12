(function ( window, document, $, undefined ) {

    $('.main_image iframe').hide();
    //image clicks
    $('.exercise .other_images .image').click(function() {
        //set this image to be the main image
        var new_main_image = $(this).find('img');
        var new_main_image_src = new_main_image.attr('src');
        var new_main_image_alt = new_main_image.attr('alt');
        $('.exercise .main_image img').attr('src', new_main_image_src);
        $('.exercise .main_image img').attr('alt', new_main_image_alt);
        if($(this).hasClass('video')) {
            $('.main_image img').hide();
            $('.main_image iframe').show();
        }
        else {
            $('.main_image img').show();
            $('.main_image iframe').hide();
        }
    });

    //make sure thumbails are in 4:3 ratio
    var image_width = parseInt($('.other_images .image').css('width'));
    console.log(image_width);
    var image_height = image_width/4*3;
    console.log(image_height);
    $('.other_images .image').css('height', image_height + 'px');

    //set iframe height
    var iframe_width = parseInt($('.video_block iframe').css('width'));
    var ifram_height = iframe_width/17*10;
    $('.video_block iframe').css('height', ifram_height + 'px');

    //enlarge image
    $('.exercise .main_image').click(function() {
        $('#exercise_image_modal img').attr('src', $('.main_image img').attr('src'));
        $('#exercise_image_modal').fadeIn(350, function() {
        });
    });

    //set lightbox modal bg to full page height
    $('.lightbox_modal').css('height', $('.page_container').height() + 'px');

})(window, window.document, window.jQuery);