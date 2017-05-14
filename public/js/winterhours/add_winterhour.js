(function ( window, document, $, undefined ) {

    //input anims
    $('#add_winterhour').find('input, textarea').on('keyup blur focus', function (e) {
        var $this = $(this),
            label = $this.prev('label');

        if (e.type === 'keyup') {
            if ($this.val() === '') {
                label.removeClass('active highlight');
            } else {
                label.addClass('active highlight');
            }
        } else if (e.type === 'blur') {
            if( $this.val() === '' ) {
                label.removeClass('active highlight');
            } else {
                label.removeClass('highlight');
            }
        } else if (e.type === 'focus') {

            if( $this.val() === '' ) {
                label.removeClass('highlight');
            }
            else if( $this.val() !== '' ) {
                label.addClass('highlight');
            }
        }

    });

    //if old values in inputs move title up
    $add_winterhour_inputs = $('#add_winterhour').find('input[type="text"], textarea');
    $add_winterhour_inputs.each(function() {
        var label = $(this).prev('label');
        if($(this).val()) {
            label.addClass('active highlight');
        }
    });


    //autocomplete title in heading
    $( "#groupname" ).keyup(function() {
        update_title();
    });
    $( "#groupname" ).change(function() {
        update_title();
    });

    function update_title() {
        $title = $("#groupname").val();
        if($title != "") {
            $(".add_winterhour .heading").text($title);
        }
        else {
            $(".add_winterhour .heading").text('Nieuwe winteruur groep');
        }
    }


    //day select
    $('.selectpicker').selectpicker();

    $('.bootstrap-select').click(function() {
        $(this).find('.dropdown-menu.open').toggle();
        $(this).removeClass('dropup');
    });


    //datepicker
    $('.container_date').datepicker({
        language: 'nl-BE',
        multidateSeparator: ",",
        maxViewMode: 0,
        multidate: true,
        toggleActive: true,
        startDate: 'today',
        //disabled days are necessary to make only selected day clickable (0 = sunday, 6 = saturday)
        daysOfWeekDisabled: [0,6]
    }).on('changeDate', function (e) {
            //console.log(e.format());
            var startdate = e.format();
            console.log(startdate);
        }

    );


})(window, window.document, window.jQuery);