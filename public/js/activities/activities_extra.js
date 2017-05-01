(function ( window, document, $, undefined ) {
	console.log("yip");
	console.log($('input[name^="paid"]'));

	$('input[name^="paid"]').change(function(){
		var activity_id = $(".heading").attr('activity_id');
		var user_id 	= ($(this).attr('name')).replace('paid', '');
		var is_checked 	= !($(this).attr('is_checked') == 'true');
		$(this).attr('is_checked', is_checked);
		//post request to change paid status
		$.post( location.origin + "/api/update_activity_participant_status", { activity_id: activity_id, user_id: user_id, is_checked: is_checked }, function( data ) {
			//console.log(data);
		});
	});

	var lightbox = false;
	$('.activity .delete').click(function() {
		$('#delete_activity_modal').fadeIn(350, function() {
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
})(window, window.document, window.jQuery);