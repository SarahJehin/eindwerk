(function ( window, document, $, undefined ) {
    
    var startMonth = new Date().getMonth()+1;
    var deadlineMonth = new Date().getMonth()+1;

    var type_date = "startdate";

    $(".startdate").click(function(){
        type_date = "startdate";
        $(".date_type .bullet").removeClass("selected");
        $(".startdate .bullet").addClass("selected");

        $(".container_deadline").removeClass("front");
        $(".container_startdate").addClass("front");
    });

    $(".deadline").click(function(){
        type_date = "deadline";
        $(".date_type .bullet").removeClass("selected");
        $(".deadline .bullet").addClass("selected");

        $(".container_startdate").removeClass("front");
        $(".container_deadline").addClass("front");
    });

    var next_prev = "";
    var startdate;
    $('.container_startdate').datepicker({
        language: 'nl-BE',
        multidateSeparator: ",",
        maxViewMode: 0,
        toggleActive: true,
        startDate: 'today',
        beforeShowDay: beforeShowDateFunction
    }).on('changeDate', function (e) {
            startdate = e.format();
            var formatted_date = startdate.split("/");
            $("#startdate").val(formatted_date[2] + "-" + formatted_date[1] + "-" + formatted_date[0]);
        }

    ).on('changeMonth', function(e){
        var currMonth = new Date(e.date).getMonth() + 1;
        if(startMonth > currMonth) {
            next_prev = "previous";
        }
        else {
            next_prev = "next";
        }
        startMonth = currMonth;
        if(type_date == "startdate") {
            if(next_prev == "next") {
                $( ".container_deadline .datepicker-months tr th.next" ).trigger( "click" );
            }
            else {
                $( ".container_deadline .datepicker-months tr th.prev" ).trigger( "click" );
            }
        }
    });

    var deadline;
    $('.container_deadline').datepicker({
        language: 'nl-BE',
        multidateSeparator: ",",
        maxViewMode: 0,
        toggleActive: true,
        startDate: 'today',
        beforeShowDay: beforeShowDateFunction

    }).on('changeDate', function (e) {
            deadline = e.format();
            var formatted_date = deadline.split("/");
            $("#deadline").val(formatted_date[2] + "-" + formatted_date[1] + "-" + formatted_date[0]);
        }

    ).on('changeMonth', function(e){
        var currMonth = new Date(e.date).getMonth() + 1;
        if(deadlineMonth > currMonth) {
            next_prev = "previous";
        }
        else if(deadlineMonth < currMonth) {
            next_prev = "next";
        }
        deadlineMonth = currMonth;
        if(type_date == "deadline") {
            if(next_prev == "next") {
                $( ".container_startdate .datepicker-months tr th.next" ).trigger( "click" );
            }
            else {
                $( ".container_startdate .datepicker-months tr th.prev" ).trigger( "click" );
            }
        }
    });

    if($('input[name="startdate"]').val() != "") {
        $startdate = $('input[name="startdate"]').val();
        $startdate_arr = $startdate.split('-');
        startdate = ('0' + $startdate_arr[2]).slice(-2) + '/' + ('0' + $startdate_arr[1]).slice(-2) + '/' + $startdate_arr[0];
        startMonth = $startdate_arr[1];
        $('.container_startdate').datepicker('update', new Date($startdate_arr[0], $startdate_arr[1]-1, $startdate_arr[2]));
    }
    if($('input[name="deadline"]').val() != "") {
        $deadline = $('input[name="deadline"]').val();
        $deadline_arr = $deadline.split('-');
        //reform the deadline from yyy/mm/dd to dd/mm/yyyy
        deadline = ('0' + $deadline_arr[2]).slice(-2) + '/' + ('0' + $deadline_arr[1]).slice(-2) + '/' + $deadline_arr[0];

        $('.container_deadline').datepicker('update', new Date($deadline_arr[0], $deadline_arr[1]-1, $deadline_arr[2]));
        $('.container_startdate').datepicker('update');
        if(startdate) {
            $('.container_startdate').datepicker('setDate', startdate);
        }

        $startdate = $('input[name="startdate"]').val();
        $startdate_arr = $startdate.split('-');
        deadlineMonth = $startdate_arr[1];

        startMonth = parseInt(startMonth);
        var deadline_month_new = parseInt($deadline_arr[1]);

        while (startMonth != deadline_month_new) {
            if(startMonth > $deadline_arr[1]) {
                $( ".container_deadline .datepicker-months tr th.next" ).trigger( "click" );
                deadline_month_new++;
            }
            else if(startMonth < $deadline_arr[1]) {
                $( ".container_deadline .datepicker-months tr th.prev" ).trigger( "click" );
                deadline_month_new--;
            }
        }
    }
    else {
        //when on edit activity, and deadline = empty, make sure deadline month is set to the same as startmonth:
        //deadlinemonth will default be current month:
        startMonth = parseInt(startMonth);
        var deadline_month_new = deadlineMonth;
        while (startMonth != deadline_month_new) {
            if(startMonth > deadline_month_new) {
                $( ".container_deadline .datepicker-months tr th.next" ).trigger( "click" );
                deadline_month_new++;
            }
            else if(startMonth < deadline_month_new) {
                $( ".container_deadline .datepicker-months tr th.prev" ).trigger( "click" );
                deadline_month_new--;
            }
        }
    }

    //if the activity is in the past show another datepicker
    $('.container_dates').datepicker({
        language: 'nl-BE',
        multidateSeparator: ",",
        maxViewMode: 0,
    });
    $('.container_dates').datepicker('setDates', ['03/04/2017', '08/04/2017']);

    function beforeShowDateFunction(date) {
        //check which is the current date type and whether the current date is startdate or deadline to return an appropriate classname
        var current_date = ('0' + date.getDate()).slice(-2) + '/' + ('0' + (date.getMonth()+1)).slice(-2) + '/' + date.getFullYear();
        if(startdate && type_date == 'deadline') {
            if(startdate == current_date) {
                return 'white_color';
            }
        }
        if(deadline && type_date == 'startdate') {
            if(deadline == current_date) {
                return 'white_color';
            }
        }
        //Here for the default days
        return [true, ''];
    }

})(window, window.document, window.jQuery);