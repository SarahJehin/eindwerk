(function ( window, document, $, undefined ) {
    $('.winterhour_group h3').click(function() {
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
    $('.winterhour_group .participants h4').click(function() {
        //open/close details
        $(this).parent().find('.participants_block').slideToggle();
        if($(this).find('i').hasClass('fa-angle-down')) {
            $(this).find('i').removeClass('fa-angle-down');
            $(this).find('i').addClass('fa-angle-right');
        }
        else {
            $(this).find('i').removeClass('fa-angle-right');
            $(this).find('i').addClass('fa-angle-down');
        }
    });

    var lightbox = false;
    $('.day_time .delete').click(function() {
        $winterhour_id_to_delete = $(this).attr('winterhour_id');
        $title = $($(this).parent().parent().parent().find('h3')[0]).text();
        $title = $title.trim();
        $('#delete_winterhour_modal input[name="winterhour_id"]').val($winterhour_id_to_delete);
        $('#delete_winterhour_modal .winterhour_name').text($title);
        $('#delete_winterhour_modal').fadeIn(350, function() {
            lightbox = true;
        });
    });

    //drag 'n drop for switches
    jQuery.fn.swap = function(b){ 
        // https://stackoverflow.com/questions/7625401/swap-elements-when-you-drag-one-onto-another-using-jquery-ui
        b = jQuery(b)[0]; 
        var a = this[0]; 
        var t = a.parentNode.insertBefore(document.createTextNode(''), a); 
        b.parentNode.insertBefore(a, b); 
        t.parentNode.insertBefore(b, t); 
        t.parentNode.removeChild(t); 
        return this; 
    };

    $( ".dragdrop.active" ).draggable({ revert: true, helper: "clone", cursor: "move" });

    $( ".dragdrop" ).droppable({
        accept: ".dragdrop",
        activeClass: "ui-state-hover",
        hoverClass: "ui-state-active",
        drop: function( event, ui ) {
            var draggable = ui.draggable;
            var droppable = $(this);
            var dragPos = draggable.position(), dropPos = droppable.position();
            var user_id1 = $(draggable[0]).attr('user_id');
            var date_id1 = $(draggable[0]).attr('date_id');
            var user_id2 = $(droppable[0]).attr('user_id');
            var date_id2 = $(droppable[0]).attr('date_id');
            var swapdata = { swap1 : {user_id : user_id1, date_id : date_id1}, swap2 : {user_id : user_id2, date_id : date_id2} };

            //check if participants can be swapped
            $.post(location.origin +  "/swap_places", swapdata, function( data ) {
                $('.swap_message').removeClass('success failed');
                $('.swap_message').addClass(data.status);
                $('.swap_message').text(data.message);
                $('.swap_message').slideDown( "slow", function() {
                    // Animation complete.
                    setTimeout(function() {
                        $('.swap_message').slideUp();
                    }, 5000);
                });
                //only if the swap ws succesful, the swap may take place on the front-end as well
                if(data.status == 'success') {
                    draggable.swap(droppable);
                    $(draggable[0]).attr('date_id', date_id2);
                    $(droppable[0]).attr('date_id', date_id1);
                }
            }, "json");
        }
    });

})(window, window.document, window.jQuery);