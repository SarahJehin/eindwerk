(function ( window, document, $, undefined ) {

	//activities_list
	var lightbox = false;
	$('.activity .delete').click(function() {
		$activity_id_to_delete = $(this).attr('activity_id');
		$title = $(this).parent().find('.title').text();
		$('#delete_activity_modal input[name="activity_id"]').val($activity_id_to_delete);
		$('#delete_activity_modal .activity_name').text($title);
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

    //change visibility
    $('.is_visible label').click(function() {
    	var activity_id = $(this).attr('activity_id');
    	var is_visible = 0;
    	if(!$($(this).parent().find('input[type="checkbox"]:checked')).length) {
    		is_visible = 1;
    	}
    	$.post( location.origin + "/update_activity_visibility", { activity_id: activity_id, is_visible: is_visible }, function( data ) {
			//console.log(data);
		});
    });


	//activity_participants
	$('input[name^="paid"]').change(function(){
		var activity_id = $(".heading").attr('activity_id');
		var user_id 	= ($(this).attr('name')).replace('paid', '');
		var is_checked 	= !($(this).attr('is_checked') == 'true');
		$(this).attr('is_checked', is_checked);
		//post request to change paid status
		$.post( location.origin + "/update_activity_participant_status", { activity_id: activity_id, user_id: user_id, is_checked: is_checked }, function( data ) {
			//console.log(data);
		});
	});

    $('.participant_block .participant .name').click(function() {
        //
        var participant_block = $(this).parent().parent().parent();
        participant_block.find('.details').slideToggle( 250);
        if(participant_block.hasClass('opened')) {
            participant_block.removeClass('opened');
        }
        else {
            participant_block.addClass('opened');
        }
        
    });

    $('.participant_block .sign_out').click(function() {
        var participant_block = $(this).parent().parent();
        //console.log(participant_block.attr('user_id'));
        $('#sign_out_modal input[name="user_id"]').val(participant_block.attr('user_id'));
        $('#sign_out_modal .participant').text(participant_block.find('.name').text());
        $('#sign_out_modal').fadeIn(350, function() {
            lightbox = true;
        });
    });

	
})(window, window.document, window.jQuery);