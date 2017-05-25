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


    //drag 'n drop for switches
    jQuery.fn.swap = function(b){ 
        // method from: http://blog.pengoworks.com/index.cfm/2008/9/24/A-quick-and-dirty-swap-method-for-jQuery
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
            console.log('test');
            var draggable = ui.draggable;
            var droppable = $(this);
            var dragPos = draggable.position(), dropPos = droppable.position();
            console.log(draggable[0]);
            console.log(droppable[0]);
            var user_id1 = $(draggable[0]).attr('user_id');
            var date_id1 = $(draggable[0]).attr('date_id');
            var user_id2 = $(droppable[0]).attr('user_id');
            var date_id2 = $(droppable[0]).attr('date_id');
            var swapdata = { swap1 : {user_id : user_id1, date_id : date_id1}, swap2 : {user_id : user_id2, date_id : date_id2} };
            console.log(swapdata);

            //check if participants can be swapped
            
            $.post(location.origin +  "/api/swap_places", swapdata, function( data ) {
                console.log( data );
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