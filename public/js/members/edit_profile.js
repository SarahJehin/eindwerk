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
        console.log(valid_format);
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
        }
        
        gsm_old_value = $(this).parent().find('input').val();
        $(this).parent().find('.save_button').hide();
        console.log('save');

        $(this).parent().find('input').removeClass('not_valid');
        $(this).parent().find('input').removeClass('valid');
    });

    $('.gsm .value').focusout(function() {
        //
        var gsm_input = $(this).find('input');
        var gsm_save_btn = $(this).parent().find('.save_button');
        setTimeout(function() {
            console.log('focusout');
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
        console.log(valid_format);
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
            //send post request to server to update gsm number
        }
        tel_old_value = $(this).parent().find('input').val();
        $(this).parent().find('.save_button').hide();
        console.log('save');

        $(this).parent().find('input').removeClass('not_valid');
        $(this).parent().find('input').removeClass('valid');
    });

    $('.tel .value').focusout(function() {
        //
        var tel_input = $(this).find('input');
        var tel_save_btn = $(this).parent().find('.save_button');
        setTimeout(function() {
            console.log('focusout');
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
        console.log(valid_format);
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
        console.log(email_new_value);
        //replace all spaces
        email_new_value = email_new_value.replace(/\s/g,'');
        //check whether it is a correct email format
        var email_regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        var valid_format = email_regex.test(email_new_value);   // true
        console.log(valid_format);
        if(valid_format) {
            //send post request to server to update email
            console.log('valid email');
            email_old_value = $(this).parent().find('input').val();
            $(this).parent().find('.save_button').hide();
            console.log('save');
        }
        else {
            console.log('not valid');
        }
        $(this).parent().find('input').removeClass('not_valid');
        $(this).parent().find('input').removeClass('valid');
        
    });

    $('.email .value').focusout(function() {
        //
        var email_input = $(this).find('input');
        var email_save_btn = $(this).parent().find('.save_button');
        setTimeout(function() {
            console.log('focusout');
            email_input.val(email_old_value).attr('disabled', 'true').attr('readonly', 'true');
            email_save_btn.hide();
        }, 200);
        
        $(this).find('input').removeClass('not_valid');
        $(this).find('input').removeClass('valid');
    });


})(window, window.document, window.jQuery);