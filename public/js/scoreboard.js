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

    $('.slice-contents .real_content').click(function() {
        var member_id = $(this).attr('member_id');
        fill_member_modal(member_id);
    });
    $('.board tbody tr td:first-child').click(function() {
        var member_id = $(this).attr('member_id');
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
            $('#member_modal').show()
            lightbox = true;
        });
    }
	
})(window, window.document, window.jQuery);