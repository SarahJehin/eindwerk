(function ( window, document, $, undefined ) {

    $('.member_block .name').click(function() {
        //
        var member_block = $(this).parent().parent();
        member_block.find('.details').slideToggle( 250);
        if(member_block.hasClass('opened')) {
            member_block.removeClass('opened');
        }
        else {
            member_block.addClass('opened');
        }
    });

    $('.open_advanced ').click(function() {
        $('.search .advanced').slideToggle( 250);
    });

    $('.searchbutton').click(function() {
        $('#search_members').submit();
    });
	
    $('.selectpicker').selectpicker();

    $('.bootstrap-select').click(function() {
        $(this).find('.dropdown-menu.open').toggle();
        $(this).removeClass('dropup');
    });


    $(window).click(function() {
        $('.bootstrap-select .dropdown-menu.open').hide();
    });


})(window, window.document, window.jQuery);