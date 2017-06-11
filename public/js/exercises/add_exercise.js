(function ( window, document, $, undefined ) {
    //initiate editor with custom toolbar
    $('#summernote').summernote({
        placeholder: 'Type hier je beschrijving...',
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline'/*, 'clear'*/]],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['insert', ['link']],
            ['para', ['ul', 'ol']]
      ],
      callbacks: {
        onPaste: function (e) {
            var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
            e.preventDefault();
            document.execCommand('insertText', false, bufferText);
        }
      }
    });
    //for the dropdown menu's to work:
    $('.dropdown-toggle').dropdown()
    //make this editor use bootstrap
    $('.apply_bootstrap + div').addClass('apply_bootstrap');
    //Focus when description label is clicked
    $('h3 label[for="summernote"]').click(function() {
        $('.note-editable').focus();
    });
    $('.note-editable').focus(function() {
        $(this).parent().parent().addClass('focus');
    });
    $('.note-editable').focusout(function() {
        $(this).parent().parent().removeClass('focus');
    });

    //check whether old input exists, if yes, fill in
    if($('#description').val()) {
        $('.note-editable').html($('#description').val());
        $('.note-editing-area .note-placeholder').hide();
    }

    document.getElementsByClassName("note-editable")[0].addEventListener("input", function() {
        $description_html = $('.note-editable').html();
        $description_html = $description_html.replace('<a', '<a target="_blank"');
        $('#description').html($description_html);
    }, false);

    //images
    //make image box height equal to 4:3 ratio according to width
    var image_label_width = parseInt($('.images_block .images label').css('width'));
    console.log(image_label_width);
    var image_label_height = image_label_width/4*3;
    console.log(image_label_height);
    $('.images_block .images label').css('height', image_label_height + 'px');

    var ids_amount = 0;
    var uploaded_images = 0;
    var handle_images = function(e){
        uploaded_images = $('.images .image').length-1 + this.files.length;
        var file_input = this;

        if(uploaded_images > 6) {
            console.log(this);
            //if in total more than 6 images were uploaded, throw error
            var error_msg = 'Afbeeldingen werden niet toegevoegd omdat er maar maximum 6 afbeeldingen mogen worden toegevoegd.';
            $(this).val('');
            $('.images_block .error_msg').text(error_msg);
            $('.images_block .error_msg').slideDown(200, function() {
                setTimeout(function() {
                    $('.images_block .error_msg').slideUp();
                }, 3500);
            });
        }
        else {
            if (this.files) {
                var filesAmount = this.files.length;
                var files = this.files;

                for (i = 0; i < filesAmount; i++) {
                    (function(file) {
                        //if file size greater than 500kB, don't fullfill the upload
                        if(file.size > 500000) {
                            var error_msg = 'Afbeeldingen werden niet toegevoegd omdat er afbeeeldingen zijn die groter zijn dan 500kB.';
                            $(file_input).val('');
                            $('.images_block .error_msg').text(error_msg);
                            $('.images_block .error_msg').slideDown(200, function() {
                                setTimeout(function() {
                                    $('.images_block .error_msg').slideUp();
                                }, 3500);
                            });
                            return;
                        }
                        var reader = new FileReader();

                        reader.onload = function(event) {
                            var new_image_block = $('.images .template').clone();
                            new_image_block.removeClass('template');
                            new_image_block.attr('identifier', file.name + file.size);
                            new_image_block.find('img').attr('src', event.target.result).attr('alt', file.name);
                            new_image_block.find('input').val(file.name + file.size);
                            $(new_image_block).insertBefore($('.images .template'));
                            var image_width = parseInt($('.images .image').css('width'));
                            var image_height = image_width/4*3;
                            $('.images .image').css('height', image_height + 'px');
                        }
                        reader.readAsDataURL(file);
                    })(files[i])
                }
            }

            //hide previous input so, files won't be overwritten
            //clone input to add new files with different id
            ids_amount++;
            console.log(uploaded_images);
            var new_label = $('.images .labelholder.first').clone();
            new_label.removeClass('first');
            new_label.find('input').attr('id', 'images' + ids_amount).val('');
            new_label.find('label').attr('for', 'images' + ids_amount);
            $('.images .labelholder').hide();
            if(uploaded_images < 6) {
                console.log('test');
                new_label.appendTo($('.images'));
                console.log($('.images .labelholder:last-child'));
                $('.images .labelholder:last-child').show();
            }
        }
    }

    $('#images').on('change', handle_images);
    for(var i = 1; i < 6; i++) {
        $('.images').on('change', '#images' + i, handle_images);
    }

    //handle images delete
    var deleted_images = [];
    $('.images').on('click', '.image .delete', function() {
        console.log('delete this image');
        var image_block = $(this).parent();
        image_block.remove();
        uploaded_images = $('.images .image').length-1;
        if(uploaded_images < 6) {
            //check if an empty label already exists
            if($('.images .labelholder:last-child input').val() != '') {
                ids_amount++;
                var new_label = $('.images .labelholder.first').clone();
                new_label.removeClass('first');
                new_label.find('input').attr('id', 'images' + ids_amount).val('');
                new_label.find('label').attr('for', 'images' + ids_amount);
                new_label.appendTo($('.images'));
                $('.images .labelholder:last-child').show();
            }
        }
    });

})(window, window.document, window.jQuery);