(function ( window, document, $, undefined ) {
    //
    var all_checkboxes = $( "input[name^='date']" );


    $('#select_all').change(function() {
        if($('input[name="select_all"]:checked').length > 0) {
            console.log('checkbox checked');
            //check all the dates
            $.each(all_checkboxes, function( index, value ) {
                $(value).attr('checked', true);
            });
        }
        else {
            console.log('checkbox not checked');
            //check off all the dates
            $.each(all_checkboxes, function( index, value ) {
                $(value).removeAttr('checked');
            });
        }
    });

})(window, window.document, window.jQuery);