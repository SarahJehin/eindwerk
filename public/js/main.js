(function ( window, document, $, undefined ) {


    console.log("Welcome in the console of the TCS Dashboard!");

    //angular
    var dashboard_sportiva = angular.module("dashboard_sportiva", []);

    
    //setup token for posts
    $.ajaxSetup({
        headers: {
          'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
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

    //different background images
    var images = ['total_img01.jpg', 'total_img03.jpg', 'total_img04.jpg'];
    /*
    var intervalClearer = setInterval(function(){
        $('.login .container .background_holder').fadeTo(1000, 0.1, function() {
            var random_nr = Math.floor(Math.random() * 3);
            $(this).css('background-image', 'url(../images/site_images/' + images[random_nr] + ')');
            $(this).css('background-position', '0px 0px');
            $(this).css('background-size', 'cover');
        }).fadeTo(1000, 1);
    }, 6000);
    */
    var times = 0;
    var intervalClearer = setInterval(function(){
        var random_nr = Math.floor(Math.random() * 3);
        if(isOdd(times)) {
            //$('.background_holder2').css('background-image', 'url(../images/site_images/' + images[random_nr] + ')');
            $('.background_holder2').fadeIn(1000, function() {
                $('.background_holder1').css('background-image', 'url(../images/site_images/' + images[random_nr] + ')');
            });
        }
        else {
            $('.background_holder2').fadeOut(1000, function() {
                $('.background_holder2').css('background-image', 'url(../images/site_images/' + images[random_nr] + ')');
            });
        }
        times++;
        /*
        $('.login .container .background_holder').fadeTo(1000, 0.1, function() {
            var random_nr = Math.floor(Math.random() * 3);
            $(this).css('background-image', 'url(../images/site_images/' + images[random_nr] + ')');
            $(this).css('background-position', '0px 0px');
            $(this).css('background-size', 'cover');
        }).fadeTo(1000, 1);
        */
    }, 6000);

    function isOdd(number) {
        return number % 2;
    }

    //HAMBURGER nav opening
    $('.hamburger .hamburger_icon i').click(function() {
        //check if the nav has the opened class or not
        if($('nav').hasClass('open')) {
            $('nav').removeClass('open');
            $(this).parent().parent().removeClass('open');
        }
        else {
            $('nav').addClass('open');
            $(this).parent().parent().addClass('open');
        }
    });


    //input anims
    $('.form_with_input_anims').find('input, textarea').on('keyup blur focus', function (e) {
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


    //TIMELINE
    //multistep form
    $(".timeline div[class^='step']").click(function () {
        //console.log($(this).attr("class"));
        previous_clicked_step = $('.timeline .reached').text();
        //console.log(previous_clicked_step);
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

    //next previous
    $(".previous_next div").click(function() {
        var step_clicked = $(this).attr('step');
        //console.log(step_clicked);
        $('.timeline .step' + step_clicked).trigger('click');
    });

    //LIGHTBOX
    /*
    var lightbox = false;
    $('.lightbox_modal .modal').click(function(event) {
        event.stopPropagation();
    });

    $(window).click(function() {
        console.log(lightbox);
        console.log("knlk");
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
*/

/*
    // ADD ACTIVITY
    //multistep form
    $(".timeline div[class^='step']").click(function () {
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
        update_title();
    });
    $( "#title" ).change(function() {
        update_title();
    });

    function update_title() {
        $title = $("#title").val();
        if($title != "") {
            $(".add_activity .heading").text($title);
        }
        else {
            $(".add_activity .heading").text('Nieuwe activiteit');
        }
    }

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

    //wysiwyg editor for description
    //initiate editor with custom toolbar
    $('#summernote').summernote({
        placeholder: 'Type hier je beschrijving...',
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['insert', ['link']],
            ['para', ['ul', 'ol', 'paragraph']]
      ]
    });
    //make this editor use bootstrap
    $('.apply_bootstrap + div').addClass('apply_bootstrap');

    //check whether old input exists, if yes, fill in
    if($('#description').val()) {
        $('.note-editable').html($('#description').val());
        $('.note-editing-area .note-placeholder').hide();
    }

    //Focus when description label is clicked
    $('h3 label').click(function() {
        $('.note-editable').focus();
    });

    $('.note-editable').focus(function() {
        $(this).parent().parent().addClass('focus');
    });
    $('.note-editable').focusout(function() {
        $(this).parent().parent().removeClass('focus');
    });

    //on focus immediately remove placeholder
    /*
    $('.note-editable').focus(function() {
        $('.note-editing-area .note-placeholder').hide();
    });
    $('.note-editable').focusout(function() {
        if($('.note-editable').html($('#description').val() == '')) {
            $('.note-editing-area .note-placeholder').show();
        }
    });
    */
/*
    //Prefill placeholders
    $($('.modal.link-dialog .form-group input')[0]).attr('placeholder', "Weer te geven tekst");
    $($('.modal.link-dialog .form-group input')[1]).attr('placeholder', "URL (bvb http://google.com)");

    document.getElementsByClassName("note-editable")[0].addEventListener("input", function() {
        $description_html = $('.note-editable').html();
        $description_html = $description_html.replace('<a', '<a target="_blank"');
        $('#description').html($description_html);
    }, false);
    
/*
    //timepicker
    $(".timepicker .arrow_up").click(function () {

        $timepicker = $(this).parent().find('ul');
        $current_top = $timepicker.css("top");
        $current_top = $current_top.replace("px", "");
        $next_top = parseInt($current_top)+50;
        console.log($current_top);
        if($next_top < 0) {
            $timepicker.css("top", $next_top);
        }
        else {
            $timepicker.css("top", 0);
        }
    });
    $(".timepicker .arrow_down").click(function () {
        $timepicker = $(this).parent().find('ul');
        $current_top = $timepicker.css("top");
        $current_top = $current_top.replace("px", "");
        $next_top = $current_top-50;
        console.log($current_top);
        if($current_top > (-400)) {
            $timepicker.css("top", $next_top);
        }
    });
    */
    /*
    $('#starttime').timepicker({ 
        'timeFormat': 'H:i',
        'scrollDefault': 'now',
        'step': 30
    });
    $('#starttime').on('changeTime', function() {
        //console.log("starttime changed");
    });
    $('#endtime').timepicker({ 
        'timeFormat': 'H:i',
        'scrollDefault': 'now',
        'step': 30
    });
    $('#endtime').on('changeTime', function() {
        //console.log("starttime changed");
    });

    
    //location info
    $(".loc_sportiva").click(function(){
        $(".location_type .bullet").removeClass("selected");
        $(".loc_sportiva .bullet").addClass("selected");
        $('.google_maps').removeClass('show');
    });
    $(".loc_else").click(function(){
        $(".location_type .bullet").removeClass("selected");
        $(".loc_else .bullet").addClass("selected");
        $('.google_maps').addClass('show');
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
    $(".select_toggler").click(function (event) {
        event.stopPropagation();
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
    
    $(window).click(function() {
        $(".owner ul").hide();
        $show_owner_select = false;
    });
*/

})(window, window.document, window.jQuery);