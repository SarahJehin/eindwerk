(function ( window, document, $, undefined ) {

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

    //check if there already exists a poster (when on edit activity)
    if($('.uploaded_poster').attr('src')) {
        $('.uploaded_poster').show();
        $(".poster label").addClass("with_poster");
    }


    //wysiwyg editor for description
    //initiate editor with custom toolbar
    $('#summernote').summernote({
        placeholder: 'Type hier je beschrijving...',
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline'/*, 'clear'*/]],
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

    //Prefill placeholders
    $($('.modal.link-dialog .form-group input')[0]).attr('placeholder', "Weer te geven tekst");
    $($('.modal.link-dialog .form-group input')[1]).attr('placeholder', "URL (bvb http://google.com)");

    document.getElementsByClassName("note-editable")[0].addEventListener("input", function() {
        $description_html = $('.note-editable').html();
        $description_html = $description_html.replace('<a', '<a target="_blank"');
        $('#description').html($description_html);
    }, false);
    
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

    //check if other location was selected, on edit_activity
    if($(".loc_else .bullet").hasClass("selected")) {
        $('.google_maps').addClass('show');
    }
    
    

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


})(window, window.document, window.jQuery);
/*
//google maps
function initMap() {
    var location = {lat: 51.083253, lng: 4.805906};
    var map = new google.maps.Map(document.getElementById('sportiva_map'), {
        zoom: 12,
        center: location
    });
    var marker = new google.maps.Marker({
        position: location,
        map: map
    });
}*/