(function ( window, document, $, undefined ) {


    console.log("Welcome in the console of the TCS Dashboard!");

    // LOGIN
    //login anims (code from: http://codepen.io/ehermanson/pen/KwKWEv)
    $('#login_form').find('input, textarea').on('keyup blur focus', function (e) {

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



    // ADD ACTIVITY
    //multistep form
    $("div[class^='step']").click(function () {
        //console.log($(this).attr("class"));
        var step = $(this).attr("class").replace("step", "");
        //console.log(step);
        //var left = 500*step-500;
        var left = 100*step-100;
        console.log(left);
        $(".total").css("left", -left + "%");
    });
    
    
    //input anims
    $('#add_activity').find('input, textarea').on('keyup blur focus', function (e) {

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
    
    
    //timepicker
    $(".timepicker .arrow_up").click(function () {
        $current_top = $(".timepicker ul").css("top");
        $current_top = $current_top.replace("px", "");
        $next_top = parseInt($current_top)+50;
        if($current_top != 0) {
            $(".timepicker ul").css("top", $next_top);
        }
    });
    $(".timepicker .arrow_down").click(function () {
        $current_top = $(".timepicker ul").css("top");
        $current_top = $current_top.replace("px", "");
        $next_top = $current_top-50;
        console.log($current_top);
        if($current_top > (-350)) {
            $(".timepicker ul").css("top", $next_top);
        }
    });

    //range slider
    $("#participants_slider").slider({});
    $( "#participants_slider" ).change(function() {
        var value = $( "#participants_slider" ).val();
        value = value.split(",");
        var min = value[0];
        var max = value[1]
        //console.log("min is " + min + " and max: " + max);
        $(".min_participants").html(min);
        $(".max_participants").html(max);
    });


})(window, window.document, window.jQuery);