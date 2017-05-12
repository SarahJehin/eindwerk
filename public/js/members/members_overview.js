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

    var lightbox = false;
    $('.import_members').click(function(){
        $('#import_members_modal').fadeIn(350, function() {
            lightbox = true;
        });
    });
    if(errors) {
        $('#import_members_modal').fadeIn(350, function() {
            lightbox = true;
        });
    }

    $('#import_members').change(function() {
        var filename = $(this).val();
        var lastIndex = filename.lastIndexOf("\\");
        if (lastIndex >= 0) {
            filename = filename.substring(lastIndex + 1);
        }
        $('#import_members_modal label').html('<i class="fa fa-file-excel-o" aria-hidden="true"></i> ' + filename);
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


})(window, window.document, window.jQuery);