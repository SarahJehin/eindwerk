(function ( window, document, $, undefined ) {

    //gsm
    var gsm_old_value;
    $('.gsm .edit_button').click(function() {
        var gsm_input = $(this).parent().find('.value input');
        gsm_old_value = gsm_input.val();
        gsm_input.removeAttr('disabled').removeAttr('readonly').focus();
        $(this).parent().find('.save_button').show();
    });

    $('.gsm input').keyup(function() {
        var gsm_new_value = $(this).val();
        gsm_new_value = gsm_new_value.replace(/\s/g,'');
        var valid_format = /^\d{10}$/.test(gsm_new_value);
        //console.log(valid_format);
        if(valid_format) {
            $(this).removeClass('not_valid');
            $(this).addClass('valid');
        }
        else {
            $(this).removeClass('valid');
            $(this).addClass('not_valid');
        }
    });

    $('.gsm .save_button').click(function() {
        var gsm_new_value = $(this).parent().find('input').val();
        //replace all spaces
        gsm_new_value = gsm_new_value.replace(/\s/g,'');
        //check whether it is a correct mobile number format
        var valid_format = /^\d{10}$/.test(gsm_new_value);   // true
        if(valid_format) {
            //send post request to server to update gsm number
            $.post( location.origin + "/api/update_profile", { type: 'mobile', user_id: user_id, new_value: gsm_new_value }, function( data ) {
                //console.log(data);
            });
        }
        
        gsm_old_value = $(this).parent().find('input').val();
        $(this).parent().find('.save_button').hide();
        //console.log('save');

        $(this).parent().find('input').removeClass('not_valid');
        $(this).parent().find('input').removeClass('valid');
    });

    $('.gsm .value').focusout(function() {
        //
        var gsm_input = $(this).find('input');
        var gsm_save_btn = $(this).parent().find('.save_button');
        setTimeout(function() {
            //console.log('focusout');
            gsm_input.val(gsm_old_value).attr('disabled', 'true').attr('readonly', 'true');
            gsm_save_btn.hide();
        }, 200);

        $(this).find('input').removeClass('not_valid');
        $(this).find('input').removeClass('valid');
        
    });

    //tel
    var tel_old_value;
    $('.tel .edit_button').click(function() {
        var tel_input = $(this).parent().find('.value input');
        tel_old_value = tel_input.val();
        tel_input.removeAttr('disabled').removeAttr('readonly').focus();
        $(this).parent().find('.save_button').show();
    });

    $('.tel input').keyup(function() {
        var tel_new_value = $(this).val();
        tel_new_value = tel_new_value.replace(/\s/g,'');
        var valid_format = /^\d{9}$/.test(tel_new_value);
        //console.log(valid_format);
        if(valid_format) {
            $(this).removeClass('not_valid');
            $(this).addClass('valid');
        }
        else {
            $(this).removeClass('valid');
            $(this).addClass('not_valid');
        }
    });


    $('.tel .save_button').click(function() {
        var tel_new_value = $(this).parent().find('input').val();
        //replace all spaces
        tel_new_value = tel_new_value.replace(/\s/g,'');
        //check whether it is a correct mobile number format
        var valid_format = /^\d{9}$/.test(tel_new_value);
        if(valid_format) {
            //send post request to server to update tel number
            $.post( location.origin + "/api/update_profile", { type: 'phone', user_id: user_id, new_value: tel_new_value }, function( data ) {
                //console.log(data);
            });
        }
        tel_old_value = $(this).parent().find('input').val();
        $(this).parent().find('.save_button').hide();
        //console.log('save');

        $(this).parent().find('input').removeClass('not_valid');
        $(this).parent().find('input').removeClass('valid');
    });

    $('.tel .value').focusout(function() {
        //
        var tel_input = $(this).find('input');
        var tel_save_btn = $(this).parent().find('.save_button');
        setTimeout(function() {
            //console.log('focusout');
            tel_input.val(tel_old_value).attr('disabled', 'true').attr('readonly', 'true');
            tel_save_btn.hide();
        }, 200);

        $(this).find('input').removeClass('not_valid');
        $(this).find('input').removeClass('valid');
        
    });

    //email
    var email_old_value;
    $('.email .edit_button').click(function() {
        var email_input = $(this).parent().find('.value input');
        email_old_value = email_input.val();
        email_input.removeAttr('disabled').removeAttr('readonly').focus();
        $(this).parent().find('.save_button').show();
    });

    $('.email input').keyup(function() {
        var email_new_value = $(this).val();
        var email_regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        var valid_format = email_regex.test(email_new_value);   // true
        //console.log(valid_format);
        if(valid_format) {
            $(this).removeClass('not_valid');
            $(this).addClass('valid');
        }
        else {
            $(this).removeClass('valid');
            $(this).addClass('not_valid');
        }
    });

    $('.email .save_button').click(function() {
        var email_new_value = $(this).parent().find('input').val();
        //console.log(email_new_value);
        //replace all spaces
        email_new_value = email_new_value.replace(/\s/g,'');
        //check whether it is a correct email format
        var email_regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        var valid_format = email_regex.test(email_new_value);   // true
        console.log(valid_format);
        if(valid_format) {
            //send post request to server to update email
            $.post( location.origin + "/api/update_profile", { type: 'email', user_id: user_id, new_value: email_new_value }, function( data ) {
                //console.log(data);
            });
            email_old_value = $(this).parent().find('input').val();
            $(this).parent().find('.save_button').hide();
            //console.log('save');
        }
        else {
            //console.log('not valid');
        }
        $(this).parent().find('input').removeClass('not_valid');
        $(this).parent().find('input').removeClass('valid');
        
    });

    $('.email .value').focusout(function() {
        //
        var email_input = $(this).find('input');
        var email_save_btn = $(this).parent().find('.save_button');
        setTimeout(function() {
            //console.log('focusout');
            email_input.val(email_old_value).attr('disabled', 'true').attr('readonly', 'true');
            email_save_btn.hide();
        }, 200);
        
        $(this).find('input').removeClass('not_valid');
        $(this).find('input').removeClass('valid');
    });


    //modal
    var lightbox = false;

    $('.lightbox_modal .modal').click(function(event) {
        event.stopPropagation();
    });

    $(window).click(function() {
        //console.log(lightbox);
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

//croppie
var $uploadCrop;

function readFile(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function (e) {
            $('.upload-container').addClass('ready');
            $uploadCrop.croppie('bind', {
                url: e.target.result
            }).then(function(){
                console.log('jQuery bind complete');
            });
            
        }
        
        reader.readAsDataURL(input.files[0]);
    }
    else {
        alert("Sorry - you're browser doesn't support the FileReader API");
    }
}

$uploadCrop = $('#upload-container').croppie({
    viewport: {
        width: 300,
        height: 300,
        type: 'circle'
    },
    boundary: {width: 350, height: 350},
    showZoomer: true
});

$('#upload').on('change', function () { readFile(this); });
$('.upload-result').on('click', function (ev) {
    $uploadCrop.croppie('result', {
        type: 'canvas',
        size: 'viewport'
    }).then(function (resp) {
        //get the result (base64 encoded image) and put it in the hidden input
        $('#imagebase64').val(resp);
        console.log($('#imagebase64').val());
        $('#upload_profile_pic_form').submit();
    });
});


var profile_pic_modal_active = false;
$('.profile_pic .edit_button').click(function() {
    //$('.lightbox_modal').show();
    $('.edit_profile_pic_modal').show();
    profile_pic_modal_active = true;
});


$(window).click(function(event) {
    console.log(event.target);
    if($(event.target).hasClass('edit_profile_pic_modal')) {
        close_profile_pic_modal();
    }
});

$('.edit_profile_pic_modal .fa-times').click(function() {
    close_profile_pic_modal();
});

$( window ).on( "keydown", function( event ) {
    //if esc key is pressed, close modal
    if(event.which == 27) {
        close_profile_pic_modal();
    }
});

function close_profile_pic_modal() {
    if(profile_pic_modal_active) {
        $('.edit_profile_pic_modal').fadeOut(350, function() {
            profile_pic_modal_active = false;
        });
    }
}


})(window, window.document, window.jQuery);