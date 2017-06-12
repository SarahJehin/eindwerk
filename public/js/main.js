(function ( window, document, $, undefined ) {


    console.log("Welcome in the console of the TCS Dashboard!");

    //initialise the angular module
    var dashboard_sportiva = angular.module("dashboard_sportiva", []);

    //setup token to be send with all posts send from the application
    $.ajaxSetup({
        headers: {
          'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
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
    var random_nr = Math.floor(Math.random() * 3);
    $('.background_holder1').css('background-image', 'url(../images/site_images/' + images[random_nr] + ')');
    var times = 0;
    var intervalClearer = setInterval(function(){
        var random_nr = Math.floor(Math.random() * 3);
        if(isOdd(times)) {
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
        previous_clicked_step = $('.timeline .reached').text();
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
        $('.timeline .step' + step_clicked).trigger('click');
    });

    //LIGHTBOX
    var lightbox = false;
    $('.lightbox_modal .modal').click(function(event) {
        event.stopPropagation();
    });

    $(window).click(function() {
        //setTimeout to prevent closing on opening click
        setTimeout(function() {
            if($('.lightbox_modal').is(":visible")) {
                lightbox = true;
            }
        }, 100);
        close_lightbox_modal();
    });

    $('.lightbox_modal .modal .fa-times').click(function() {
        if($('.lightbox_modal').is(":visible")) {
            lightbox = true;
        }
        close_lightbox_modal();
    });

    $( window ).on( "keydown", function( event ) {
        if($('.lightbox_modal').is(":visible")) {
            lightbox = true;
        }
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

})(window, window.document, window.jQuery);