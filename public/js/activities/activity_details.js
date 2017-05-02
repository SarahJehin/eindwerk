(function ( window, document, $, undefined ) {

	$('.tabs .details').click(function() {
        $('.tabs div').removeClass('active');
        $('.tabs .details').addClass('active');
        $('.activity_info .sign_up').hide();
        $('.activity_info .activity_details').show();
    });

    $('.tabs .sign_up').click(function() {
        $('.tabs div').removeClass('active');
        $('.tabs .sign_up').addClass('active');
        $('.activity_info .activity_details').hide();
        $('.activity_info .sign_up').show();
    });

    var lightbox = false;
    $('.poster').click(function() {
        $('#poster_modal').fadeIn(350, function() {
            lightbox = true;
        });
    });

    $('.me #sign_out').click(function() {
        $('#sign_out_modal').fadeIn(350, function() {
            lightbox = true;
        });
    });

    $('.lightbox_modal .modal').click(function(event) {
        event.stopPropagation();
    });

    $(window).click(function() {
        //console.log(lightbox);
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

    //sign up others
    $('#sign_up_others').change(function() {
        if($('#sign_up_others:checked').length > 0) {
            $('.sign_up_others').slideDown();
        }
        else {
            $('.sign_up_others').slideUp();
        }
    });

    $('#search_participants').keyup(function() {
        //console.log("keyup");
        $('.search_results ul').show();
        var searchstring    = $('#search_participants').val();
        var not_ids         = [];
        $.each($('.sign_up_list .people .person'), function(key, person) {
            console.log($(person));
            not_ids.push($(person).attr('user_id'));
        });
        $.each($('.added_participants .participant input'), function(key, person) {
            not_ids.push($(person).val());
        });
        console.log(not_ids);
        if(searchstring.length > 1) {
            //get 5 first search results
            $.get( location.origin + "/api/get_matching_users", {searchstring: searchstring, not_ids: not_ids}, function( data ) {
                //console.log(data);
                //console.log(data[0]['first_name']);
                $('.search_results ul').empty();
                $.each(data, function( key, result ) {
                    var id = result["id"];
                    var first_name = result["first_name"];
                    var last_name = result["last_name"];
                    $new_list_item = '<li user_id=' + id + '>' + first_name + ' ' + last_name + '</li>';
                    $('.search_results ul').append($new_list_item);
                });
                if(data.length < 1) {
                    $new_list_item = '<li>Geen leden gevonden</li>';
                    $('.search_results ul').append($new_list_item);
                }
            }, "json" );
        }
        else {
            $('.search_results ul').empty();
        }
    });

    $('.search_participants .search_results ul').on('click', 'li', function() {
        //console.log("clicked");
        $('.search_results ul').hide();
        //console.log($(this));
        var id = $(this).attr('user_id');
        var full_name = $(this).text();
        console.log(id + ': ' + full_name);
        $new_participant = $('.added_participants .template').clone();
        $new_participant.removeClass('template');
        $new_participant.find('input').val(id);
        $new_participant.find('span').text(full_name);
        //console.log($new_participant);
        $('.added_participants').append($new_participant);
        $('.added_participants').show();
    });

    $('.added_participants').on('click', '.participant .remove', function() {
        $(this).parent().remove();
        if($('.added_participants .participant').length < 1) {
            $('.added_participants').hide();
        }
    });

    $('.sign_up_info input[type="submit"]').click(function() {
        var sign_up_me = $('input[name="sign_up_me"]:checked').length > 0;
        var sign_up_others = $('input[name="sign_up_others"]:checked').length > 0;
        var form_submit_allowed = false;
        var error_msg = "";
        console.log(sign_up_others);
        if(sign_up_me) {
            form_submit_allowed = true;
        }
        else if(sign_up_others) {
            console.log($('.added_participants .participant').length);
            if($('.added_participants .participant').length > 1) {
                form_submit_allowed = true;
            }
            else {
                error_msg = "Je moet minsens 1 iemand inschrijven."
            }
        }
        else {
            error_msg = "Je moet minstens 1 optie aanvinken."
        }
        if(form_submit_allowed) {
            $( "#activity_sign_up" ).submit();
        }
        else {
            //show error message
            $( ".sign_up_info .error_msg" ).text(error_msg);
            $( ".sign_up_info .error_msg" ).slideDown( "slow", function() {
                setTimeout(function() {
                    $( ".sign_up_info .error_msg" ).slideUp();
                }, 3000);
            });
        }

    });


})(window, window.document, window.jQuery);

//google maps
function initMap() {
    var location = {lat: latitude, lng: longitude};
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 12,
        center: location
    });
    var marker = new google.maps.Marker({
        position: location,
        map: map
    });
}