(function ( window, document, $, undefined ) {
    
    var startMonth = new Date().getMonth()+1;
    var deadlineMonth = new Date().getMonth()+1;

    var type_date = "startdate";

    $(".startdate").click(function(){
        //console.log("startdate called");
        type_date = "startdate";
        //console.log(type_date);

        $(".container_deadline").removeClass("front");
        $(".container_startdate").addClass("front");

        $('.container_deadline').removeClass("bg");

        if(startMonth == deadlineMonth) {
            $('.container_startdate').removeClass("bg");
            $('.container_deadline').removeClass("bg");
        }
        else {
            $('.container_startdate').addClass("bg");
        }

    });

    $(".deadline").click(function(){
        //console.log("deadline called");
        type_date = "deadline";
        //.log(type_date);

        $(".container_startdate").removeClass("front");
        $(".container_deadline").addClass("front");

        //console.log("deadlinemonth: " + deadlineMonth);
        //console.log("startmonth: " + startMonth);
        $('.container_startdate').removeClass("bg");
        if(startMonth == deadlineMonth) {
            $('.container_deadline').removeClass("bg");
            $('.container_startdate').removeClass("bg");
        }
        else {
            $('.container_deadline').addClass("bg");
        }
    });

    var next_prev = "";
    //console.log(startMonth);

    $('.container_startdate').datepicker({
        language: 'nl-BE',
        multidateSeparator: ",",
        toggleActive: true
    }).on('changeDate', function (e) {
            //console.log(e.format());
            var startdate = e.format();
        }

    ).on('changeMonth', function(e){
        var currMonth = new Date(e.date).getMonth() + 1;
        if(startMonth > currMonth) {
            //console.log("prev");
            next_prev = "previous";
        }
        else {
            //console.log("next");
            next_prev = "next";
        }
        //console.log(currMonth);
        startMonth = currMonth;
        if(type_date == "startdate") {
            if(next_prev == "next") {
                $( ".container_deadline .datepicker-months tr th.next" ).trigger( "click" );
            }
            else {
                $( ".container_deadline .datepicker-months tr th.prev" ).trigger( "click" );
            }
        }
        if(currMonth == deadlineMonth) {
            $('.container_startdate').removeClass("bg");
        }
        else {
            $('.container_startdate').addClass("bg");
        }
    });


    $('.container_deadline').datepicker({
        language: 'nl-BE',
        multidateSeparator: ",",
        toggleActive: true
    }).on('changeDate', function (e) {
            console.log(e.format());
            var deadline = e.format();
        }

    ).on('changeMonth', function(e){
        var currMonth = new Date(e.date).getMonth() + 1;
        if(deadlineMonth > currMonth) {
            //console.log("prev");
            next_prev = "previous";
        }
        else {
            //console.log("next");
            next_prev = "next";
        }
        //console.log(currMonth);
        deadlineMonth = currMonth;
        if(type_date == "deadline") {
            if(next_prev == "next") {
                $( ".container_startdate .datepicker-months tr th.next" ).trigger( "click" );
            }
            else {
                $( ".container_startdate .datepicker-months tr th.prev" ).trigger( "click" );
            }
        }
        if(currMonth == startMonth) {
            $('.container_deadline').removeClass("bg");
        }
        else {
            $('.container_deadline').addClass("bg");
        }
    });

})(window, window.document, window.jQuery);