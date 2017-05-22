(function ( window, document, $, undefined ) {
    $('.winterhour_group h4').click(function() {
        //open/close details
        $(this).parent().find('.details').slideToggle();
        if($(this).find('i').hasClass('fa-angle-down')) {
            $(this).find('i').removeClass('fa-angle-down');
            $(this).find('i').addClass('fa-angle-right');
        }
        else {
            $(this).find('i').removeClass('fa-angle-right');
            $(this).find('i').addClass('fa-angle-down');
        }
    });
})(window, window.document, window.jQuery);