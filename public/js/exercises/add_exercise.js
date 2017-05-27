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
      ]
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

    //if images were uploaded show them before the label
    /*
    $('#images').on('change', function(e){
        console.log('images changed');
        if (this.files && this.files[0]) {
            console.log(this.files);
            var reader = new FileReader();

            reader.onload = function (e) {
                console.log("testtststst");
                //create element.
                $('.uploaded_poster').attr('src', e.target.result);
                $('.uploaded_poster').show();
                $(".poster label").addClass("with_poster");
            }

            reader.readAsDataURL(this.files[0]);
            $.each(this.files, function(key, value) {
                console.log('helibeli');
                console.log(value);
                reader.readAsDataURL(value);
            })
        }
    });
    */
    var previously_added_files = []
    $('#images').on('change', function(e){
        $.each(this.files, function(key, val) {
            previously_added_files.push(val);
        });
        
        if (this.files) {
            var filesAmount = this.files.length;

            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();

                reader.onload = function(event) {
                    var new_image_block = '<div class="image float"><img src=' + event.target.result + '></div>';
                    console.log($(new_image_block));
                    /*
                    var image = $(new_image_block).find('img');
                    console.log(image);
                    image.attr('src', event.target.result);
                    console.log(image);
                    */
                    console.log($(new_image_block))
                    $(new_image_block).prependTo($('.images'));
                    console.log($($.parseHTML('<img>')).attr('src', event.target.result));
                    //$($.parseHTML('<img>')).attr('src', event.target.result).appendTo($('.images'));
                    var image_width = parseInt($('.images .image').css('width'));
                    console.log(image_width)
                    var image_height = image_width/4*3;
                    $('.images .image').css('height', image_height + 'px');
                }

                reader.readAsDataURL(this.files[i]);
            }
        }
        var formData = new FormData();
        console.log(previously_added_files);
        formData.append('images', previously_added_files); 
        formData.append('test', "blieblabloe"); 
    });


})(window, window.document, window.jQuery);