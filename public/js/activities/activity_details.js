(function ( window, document, $, undefined ) {

	$('.tabs .details').click(function() {
        $('.tabs div').removeClass('active');
        $('.tabs .details').addClass('active');
        $('.activity_info .sign_up').removeClass('active');
        $('.activity_info .activity_details').show();
    });

    $('.tabs .sign_up').click(function() {
        $('.tabs div').removeClass('active');
        $('.tabs .sign_up').addClass('active');
        $('.activity_info .activity_details').hide();
        $('.activity_info .sign_up').addClass('active');
    });

    $('.poster').click(function() {
        $('#poster_modal').fadeIn(350, function() {
        });
    });
    $('.me #sign_out').click(function() {
        $('#sign_out_modal').fadeIn(350, function() {
        });
    });

    $('.sign_up_list .person').click(function() {
        var member_id = $(this).attr('user_id');
        fill_member_modal(member_id);
    });

    function fill_member_modal(id) {
        $.get(location.origin + '/get_member_details/' + id, function( data ) {
            $('#member_modal .image img').attr('src', location.origin + '/images/profile_pictures/' + data.image).attr('alt', data.first_name + ' ' + data.last_name);
            $('#member_modal .name h2').text(data.first_name + ' ' + data.last_name);
            var birth_date = new Date(data.birth_date);
            birth_date = ('0' + birth_date.getDate()).slice(-2) + '/' + ('0' + (birth_date.getMonth()+1)).slice(-2) + '/' + birth_date.getFullYear();
            $('#member_modal .birth_date span:nth-child(2)').text(birth_date);
            $('#member_modal .ranking_singles span:nth-child(2)').text(data.ranking_singles);
            $('#member_modal .ranking_doubles span:nth-child(2)').text(data.ranking_doubles);
            $('#member_modal').fadeIn(350)
            lightbox = true;
        });
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

    var search_results = $('.search_results ul');
    var current_child = null;
    $('#search_participants').keyup(function(e) {
        if(e.which != 38 && e.which != 40 && e.which != 13) {
            current_child = null;
            $('.search_results ul').show();
            var searchstring    = $('#search_participants').val();
            var not_ids         = [];
            $.each($('.sign_up_list .people .person'), function(key, person) {
                not_ids.push($(person).attr('user_id'));
            });
            $.each($('.added_participants .participant input'), function(key, person) {
                not_ids.push($(person).val());
            });
            if(searchstring.length > 1) {
                //get 5 first search results
                $.get( location.origin + "/get_matching_users", {searchstring: searchstring, not_ids: not_ids}, function( data ) {
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
        }
        if(e.which == 40) {
            search_results.children().removeClass('hover');
            if (search_results.children('li').length > 0) {
                if(!current_child) {
                current_child = 1;
                }
                else if(current_child == search_results.children('li').length) {
                current_child = current_child;
                }
                else {
                current_child++;
                }
                search_results.children(":nth-child(" + current_child + ")").addClass('hover');
            }
        }
        if (e.keyCode == 38) {
            search_results.children().removeClass('hover');
            if (search_results.children('li').length > 0) {
                if(!current_child) {
                current_child = 1;
                }
                else {
                current_child--;
                }
                search_results.children(":nth-child(" + current_child + ")").addClass('hover');
            }
        }
        if (e.keyCode == 13) {
            var selected_li = search_results.children('.hover');
            $(selected_li).trigger('click');
        }
    });

    search_results.on('mouseover', 'li', function() {
        $('.search_results ul li').removeClass('hover');
        current_child = $(this).index() + 1;
        search_results.children(":nth-child(" + current_child + ")").addClass('hover');
    });

    $('.search_participants .search_results ul').on('click', 'li', function() {
        $('.search_results ul').hide();
        var id = $(this).attr('user_id');
        var full_name = $(this).text();
        $new_participant = $('.added_participants .template').clone();
        $new_participant.removeClass('template');
        $new_participant.find('input').val(id);
        $new_participant.find('span').text(full_name);
        $('.added_participants').append($new_participant);
        $('.added_participants').show();
        $('#search_participants').val('');
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
        if(sign_up_me) {
            form_submit_allowed = true;
        }
        else if(sign_up_others) {
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