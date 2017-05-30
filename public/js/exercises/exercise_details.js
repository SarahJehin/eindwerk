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

})(window, window.document, window.jQuery);