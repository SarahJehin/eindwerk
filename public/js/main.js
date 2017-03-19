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
    $(".timeline div[class^='step']").click(function () {
        console.log("what");
        //console.log($(this).attr("class"));
        $('div[class^="step"]').removeClass('reached');
        //get current step to display correct content
        var step = $(this).attr("class").replace("step", "");
        var left = 100*step-100;
        $(".total").css("left", -left + "%");

        //add reached class to all previous steps
        for(var i = 1; i <= step; i++) {
            $('.step' + i).addClass('reached');
        }

        $filled_percentage = (step-1)*33;

        $('.filled_line').css('width', $filled_percentage + "%");

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

    //if old values in inputs move title up
    $add_activity_inputs = $('#add_activity').find('input[type="text"], textarea');
    $add_activity_inputs.each(function() {
        var label = $(this).prev('label');
        if($(this).val()) {
            label.addClass('active highlight');
        }
    });


    //autocomplete title in heading
    $( "#title" ).keyup(function() {
        $title = $("#title").val();
        if($title != "") {
            $(".add_activity .heading").text($title);
        }

    });

    //poster upload
    $('#poster').on('change', function(e){

        if (this.files && this.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('.uploaded_poster').attr('src', e.target.result);
                $('.uploaded_poster').show();
                $(".poster label").addClass("with_poster");
            }

            reader.readAsDataURL(this.files[0]);
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
    
    
    //location info
    $(".loc_sportiva").click(function(){
        $(".location_type .bullet").removeClass("selected");
        $(".loc_sportiva .bullet").addClass("selected");
    });
    $(".loc_else").click(function(){
        $(".location_type .bullet").removeClass("selected");
        $(".loc_else .bullet").addClass("selected");
    });
    
    

    //range slider (moet nog gezet worden enkel voor add activity pagina)
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

    $("#price_slider").slider({});
    $( "#price_slider" ).change(function() {
        $(".price_amount").html($("#price_slider").val());
    });

    $("#helpers_slider").slider({});
    $( "#helpers_slider" ).change(function() {
        $(".helpers_amount").html($("#helpers_slider").val());
    });

    $show_owner_select = false;
    $(".select_toggler").click(function () {
        if($show_owner_select) {
            $(".owner ul").hide();
            $show_owner_select = false;
        }
        else {
            $(".owner ul").show();
            $show_owner_select = true;
        }
    });

    $(".owner ul li").click(function () {
        //console.log($(this).text());
        $clicked_owner_id = $(this).attr("owner-id");
        $clicked_owner_name = $(this).text();
        $("#owner").val($clicked_owner_id);
        $("#owner_name").val($clicked_owner_name);
        $(".owner .select_title").text($clicked_owner_name);
        $(".owner ul").hide();
        $show_owner_select = false;
    });
    


})(window, window.document, window.jQuery);