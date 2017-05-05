(function ( window, document, $, undefined ) {


    $('.tabs .adults').click(function() {
        $('.tabs div').removeClass('active');
        $('.tabs .adults').addClass('active');
        $('.scoreboard.adults').show();
        $('.scoreboard.youth').hide();
    });

    $('.tabs .youth').click(function() {
        $('.tabs div').removeClass('active');
        $('.tabs .youth').addClass('active');
        $('.scoreboard.youth').show();
        $('.scoreboard.adults').hide();
    });
	
})(window, window.document, window.jQuery);