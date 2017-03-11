(function ( window, document, $, undefined ) {
    
    var startMonth = new Date().getMonth()+1;
    var deadlineMonth = new Date().getMonth()+1;

    var type_date = "startdate";

    $(".startdate").click(function(){
        //console.log("startdate called");
        type_date = "startdate";
        //console.log(type_date);
        $(".date_type .bullet").removeClass("selected");
        $(".startdate .bullet").addClass("selected");

        $(".container_deadline").removeClass("front");
        $(".container_startdate").addClass("front");



    });

    $(".deadline").click(function(){
        //console.log("deadline called");
        type_date = "deadline";
        //.log(type_date);
        $(".date_type .bullet").removeClass("selected");
        $(".deadline .bullet").addClass("selected");

        $(".container_startdate").removeClass("front");
        $(".container_deadline").addClass("front");
    });

    var next_prev = "";
    //console.log(startMonth);

    $('.container_startdate').datepicker({
        language: 'nl-BE',
        multidateSeparator: ",",
        maxViewMode: 0,
        toggleActive: true
    }).on('changeDate', function (e) {
            //console.log(e.format());
            var startdate = e.format();
            var formatted_date = startdate.split("/");
            $("#startdate").val(formatted_date[2] + "-" + formatted_date[1] + "-" + formatted_date[0]);
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
    });


    $('.container_deadline').datepicker({
        language: 'nl-BE',
        multidateSeparator: ",",
        maxViewMode: 0,
        toggleActive: true
    }).on('changeDate', function (e) {
            console.log(e.format());
            var deadline = e.format();
            var formatted_date = deadline.split("/");
            $("#deadline").val(formatted_date[2] + "-" + formatted_date[1] + "-" + formatted_date[0]);
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
    });

})(window, window.document, window.jQuery);