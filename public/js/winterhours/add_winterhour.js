(function ( window, document, $, undefined ) {

    angular.module("dashboard_sportiva").controller("WinterhourController", function ($scope, $http) {

        $scope.scheme_exists = false;

    //add method on Date object
    //return the date that is 'days' from the date on which the method is called
    Date.prototype.addDays = function(days) {
        var dat = new Date(this.valueOf())
        dat.setDate(dat.getDate() + days);
        return dat;
    }

    //input anims
    $('#add_winterhour').find('input, textarea').on('keyup blur focus', function (e) {
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
    $add_winterhour_inputs = $('#add_winterhour').find('input[type="text"], textarea');
    $add_winterhour_inputs.each(function() {
        var label = $(this).prev('label');
        if($(this).val()) {
            label.addClass('active highlight');
        }
    });


    //autocomplete title in heading
    $( "#groupname" ).keyup(function() {
        update_title();
    });
    $( "#groupname" ).change(function() {
        update_title();
    });

    function update_title() {
        $title = $("#groupname").val();
        if($title != "") {
            $(".add_winterhour .heading").text($title);
        }
        else {
            $(".add_winterhour .heading").text('Nieuwe winteruur groep');
        }
    }


    //day select
    $('.selectpicker').selectpicker();

    $('.bootstrap-select').click(function() {
        $(this).find('.dropdown-menu.open').toggle();
        $(this).removeClass('dropup');
    });

    $('.day_select select').change(function() {

        if($('.day_select select').val()) {
            var enddate = new Date((new Date().getFullYear() + 1) + '-04-30');
            var days = {
                'maandag'   : 1,
                'dinsdag'   : 2,
                'woensdag'  : 3,
                'donderdag' : 4,
                'vrijdag'   : 5,
                'zaterdag'  : 6,
                'zondag'    : 0
            }

            var new_day = $('.day_select select').val();
            var new_day_number = days[new_day];
            
            var first_date = get_first_date_of_day(new_day_number);
            
            var active_dates = getDates(first_date, enddate);
            //reverse the array, because the calendar view jumps to the last date set as active
            //by reversing, this last date will actually be the first date, so the calendar doesn't jump to next year
            active_dates = active_dates.reverse();
            var disabled_dates = [];
            for(var day in days) {
                if(days[day] != new_day_number) {
                    disabled_dates.push(days[day]);
                }
            }
            //console.log('disabled dates are: ' + disabled_dates);
            //console.log(active_dates);
            //disable all the other days
            $('.container_date').datepicker('setDaysOfWeekDisabled', disabled_dates);
            //auto select all the dates that fall on this day of the week
            $('.container_date').datepicker('setDates', active_dates);

            //get all selected dates and pass them to the input
            $('.inputs').empty();
            var dates = $('.container_date').datepicker('getDates');
            dates = dates.reverse();
            for(var i = 0; i < dates.length; i++) {
                var formatted_date = dates[i].getFullYear() + '-' + ('0' + (dates[i].getMonth()+1)).slice(-2) + '-' + ('0' + dates[i].getDate()).slice(-2);
                $('.inputs').append('<input type="text" name="date[]" value="' + formatted_date + '" hidden>');
            }
            
        }
        
    });

    //datepicker
    $('.container_date').datepicker({
        language: 'nl-BE',
        multidateSeparator: ",",
        maxViewMode: 0,
        multidate: true,
        toggleActive: true,
        startDate: '01/09/' + new Date().getFullYear()
    }).on('changeDate', function (e) {
            var startdate = e.format();
            //console.log(startdate);
            //get all selected dates and pass them to the input
            $('.inputs').empty();
            var dates = $('.container_date').datepicker('getDates');
            //console.log(dates);
            for(var i = 0; i < dates.length; i++) {
                var formatted_date = dates[i].getFullYear() + '-' + ('0' + (dates[i].getMonth()+1)).slice(-2) + '-' + ('0' + dates[i].getDate()).slice(-2);
                $('.inputs').append('<input type="text" name="date[]" value="' + formatted_date + '" hidden>');
            }
        }
    );

    if($('.day_select select').val()) {
        //this underneath won't do, since it'll activate all the dates, also the ones who were checked off
        //$( ".day_select select" ).trigger( "change" );
        var dates = $('.inputs input');
        var new_dates = [];
        for(var i = 0; i < dates.length; i++) {
            var splitted_date = $(dates[i]).val().split('-');
            var new_date = splitted_date[2] + '/' + splitted_date[1] + '/' + splitted_date[0];
            new_dates.push(new_date);
        }
        //get weekday of first day
        var active_weekday = new Date($($('.inputs input')[0]).val()).getDay();
        var days = [0, 1, 2, 3, 4, 5, 6];
        console.log(active_weekday);
        //set the weekdays disabled
        var disabled_dates = [];
        for(var day in days) {
            if(day != active_weekday) {
                disabled_dates.push(day);
            }
        }
        //check if the dates are in the right order, otherwise reverse the array
        if(parseInt(new_dates[0].substr(new_dates[0].length - 4)) < parseInt(new_dates[new_dates.length-1].substr(new_dates[new_dates.length-1].length - 4))) {
            new_dates.reverse();
        }
        //disable all the other days
        $('.container_date').datepicker('setDaysOfWeekDisabled', disabled_dates);
        $('.container_date').datepicker('setDates', new_dates);
    }

    //get the first eg. Friday of the month September
    function get_first_date_of_day(day) {
        //month is September, but because it is zero based, the number will be 8 instead of 9
        var month = 8;
        var startdate = new Date(new Date().getFullYear(), month, 1);
        console.log('startdate: ' + startdate);

        //while the weekday of the date is not the requested weekday, get the next date
        while (startdate.getDay() !== day) {
            startdate.setDate(startdate.getDate() + 1);
            //console.log('new date: ' + startdate);
        }
        //console.log('real startdate: ' + startdate);
        //console.log(startdate);
        //for a weird reason if you only log the date, you get one day before the real date, but not if it's parsed to a string
        //has something to do with GMT with a difference of 2 hours
        return startdate;
    }

    function getDates(startDate, stopDate) {
        var dateArray = new Array();
        var currentDate = startDate;
        while (currentDate <= stopDate) {
            var string_date = currentDate.getDate() + '/' + (currentDate.getMonth()+1) + '/' + currentDate.getFullYear();
            dateArray.push(string_date);
            //dateArray.push( new Date (currentDate) )
            currentDate = currentDate.addDays(7);
        }
        return dateArray;
    }



    //participants
    var not_ids = [0];
    $('.add_participants').on('keyup', '.search_select .bs-searchbox input', function(e) {
        console.log("blieblabloe !!!!!!!");
        if(e.which != 38 && e.which != 40 && e.which != 13) {
            $.each($('.add_participants .add_participant input.id'), function(key, person) {
                var value = $(person).val();
                if(value) {
                    not_ids.push(parseInt(value));
                }
            });

            //$('.search_select select').empty();
            //$('search_select select option').not('option:first').remove();
            var searchstring    = $(this).val();
            if(searchstring.length > 0) {
                //get 5 first search results //add , not_ids: not_ids beneath
                $.get( location.origin + "/get_matching_users", {searchstring: searchstring, not_ids: not_ids}, function( data ) {
                    $('.search_select select').empty();
                    $.each(data, function( key, result ) {
                        var id = result["id"];
                        var first_name = result["first_name"];
                        var last_name = result["last_name"];
                        var new_list_item = '<option value="' + id + '">' + first_name + ' ' + last_name + '</option>';
                        $('.search_select select').append(new_list_item);
                    });
                    $('.selectpicker').selectpicker('refresh');
                    if(data.length < 1) {
                        //$new_list_item = '<li>Geen leden gevonden</li>';
                        //$('.search_results ul').append($new_list_item);
                    }
                }, "json" );
            }
            else {
                $('.search_results ul').empty();
            }
            //console.log($('.search_select select option:selected').text());
        }
        

    });
    
    $('.add_participants').on('DOMSubtreeModified', '.search_select .bootstrap-select .dropdown-toggle span.filter-option', function() {
        console.log('do you reach this?');
        //console.log($(this));
        console.log($('.search_select .dropdown-toggle span.filter-option').text());
        
        var add_participant_block = $(this).parent().parent().parent().parent().parent();
        var value = add_participant_block.find('.search_select .dropdown-toggle span.filter-option').text();
        //$('.bootstrap-select .bs-searchbox input').val($('.bootstrap-select .dropdown-toggle span.filter-option').text());
        if(value != 'default') {
            console.log('now you are allowed to enter')
            //get the select value
            var participant_name = value;
            var participant_id = add_participant_block.find('.search_select select').val();
            console.log('participant name is ' +participant_name);
            console.log('partivipant id is ' + participant_id);
            console.log(participant_name + ' ' + participant_id);
            //console.log($(this).parent().parent().parent().parent().parent());

            
            var input = add_participant_block.find('input.name');
            var id_input = add_participant_block.find('input.id');
            var name_input = add_participant_block.find('input.participant_name');
            var delete_btn = add_participant_block.find('.delete');
            //console.log(delete_btn);
            input.val($(this).text());
            input.attr('readonly', 'true');
            input.attr('disabled', 'true');
            input.show();
            id_input.val($(this).attr('user_id'));
            name_input.val($(this).text());
            delete_btn.removeClass('not_working');

            var new_input = $('.add_participants .template').clone();
            new_input.removeClass('template');
            new_input.find('input.id').removeAttr('disabled');
            new_input.find('input.participant_name').removeAttr('disabled');
            $('.add_participants').append(new_input);
            $('.selectpicker').selectpicker();
            
        }
        
    });


    var not_ids = [0];
    //search participants for winterhour
    $('.add_participants').on('keyup', '.search_participants', function() {
        var search_results = $(this).parent().find('.search_results ul');
        search_results.show();
        var searchstring    = $(this).val();
        not_ids = [0];
        
        $.each($('.add_participants .add_participant input.id'), function(key, person) {
            var value = $(person).val();
            if(value) {
                not_ids.push(parseInt(value));
            }
        });
        
        console.log(not_ids);
        if(searchstring.length > 1) {
            //get 5 first search results //add , not_ids: not_ids beneath
            $.get( location.origin + "/get_matching_users", {searchstring: searchstring, not_ids: not_ids}, function( data ) {
                //console.log(data[0]['first_name']);
                $('.search_results ul').empty();
                $.each(data, function( key, result ) {
                    var id = result["id"];
                    var first_name = result["first_name"];
                    var last_name = result["last_name"];
                    $new_list_item = '<li user_id=' + id + '>' + first_name + ' ' + last_name + '</li>';
                    $('.search_results ul').append($new_list_item);
                });
                if(data.length < 1) {
                    $new_list_item = '<li>Geen leden gevonden</li>';
                    $('.search_results ul').append($new_list_item);
                }
            }, "json" );
        }
        else {
            $('.search_results ul').empty();
        }
    });

    $('.add_participants').on('click', '.search_results ul li', function() {
        //console.log($(this).parent().parent().parent().find('input'));
        var input = $(this).parent().parent().parent().find('input.name');
        var id_input = $(this).parent().parent().parent().find('input.id');
        var name_input = $(this).parent().parent().parent().find('input.participant_name');
        var delete_btn = $(this).parent().parent().parent().parent().find('.delete');
        //console.log(delete_btn);
        input.val($(this).text());
        input.attr('readonly', 'true');
        input.attr('disabled', 'true');
        id_input.attr('name', 'participant_id[]');
        id_input.val($(this).attr('user_id'));
        name_input.val($(this).text());
        delete_btn.removeClass('not_working');
        $('.search_results ul').hide();

        $new_input = $('.add_participants .template').clone();
        $new_input.removeClass('template');
        $new_input.find('input.id').removeAttr('disabled');
        $new_input.find('input.participant_name').removeAttr('disabled');
        $('.add_participants').append($new_input);
        console.log(name_input);
        console.log($('.add_participants input.participant_name'));
    });

    //remove already added participant
    $('.add_participants').on('click', '.delete', function() {
        var participant = $(this).parent();
        //remove this id from the not_ids
        var participant_id = parseInt(participant.find('input.id').val());
        var index = not_ids.indexOf(participant_id);
        if (index > -1) {
            not_ids.splice(index, 1);
        }
        //remove particpant block
        participant.remove();
    });
    
    if($('.block').parent().hasClass('add_winterhour')) {
        $(".timeline div[class^='step']").click(function () {
            if($(this).hasClass('step3') || $(this).hasClass('step4')) {
                //nothing must happen
                console.log('not allowed');
                var left = $(".total").css("left");
                //console.log(left);
                $(".total").css("left", left);
                var filled_line = $('.filled_line').css('width');
                $('.filled_line').css('width', filled_line);

                $('.timeline .step3').removeClass('reached');
                $('.timeline .step4').removeClass('reached');
                //console.log(previous_clicked_step);
                if(previous_clicked_step == 1) {
                    $('.timeline .step2').removeClass('reached');
                }
                //show message that this step can only be reached when the group has been created
                $('.step_not_reachable').slideDown( "slow", function() {
                    // Animation complete.
                    setTimeout(function() {
                        $('.step_not_reachable').slideUp();
                    }, 5000);
                });
            }
        });
    }
    

    //edit winterhour extra's //only if winterhour id is defined // alles hieronder zou eigenlijk in apart script moeten staan


    //generate scheme with api request instead of direct link, to show anim
    $('.generate_scheme').click(function() {
        //show loading icon
        $('.loader').show();
        //hide the previous scheme
        $('.scheme').hide();
        $('.play_times').hide();
        //console.log('what happens??');
        //api generate scheme
        
        $.get(location.origin +  "/generate_scheme/" + winterhour_id, function( data ) {
            //console.log(data);
            $.get(location.origin +  "/get_scheme/" + winterhour_id, function( data ) {
                $('.loader').hide();
                $scope.scheme = data.scheme;
                $scope.play_times = data.play_times;
                $scope.scheme_exists = true;
                $scope.$apply();
                make_drag_and_droppable();
                $('.scheme').show();
                $('.play_times').show();
            });
        });
        
    });


    if(typeof winterhour_id !== 'undefined') {
        //get winterhour status
        $.get(location.origin +  "/get_winterhour_status", { winterhour_id: winterhour_id }, function( data ) {
            console.log(data);
            var status = data;
            $scope.winterhour_status = status;
            //if the status is 4, show message + disable all inputs
            if(status == 4) {
                $('.not_editable_message').show();
                //disable all inputs
                $('.groupname input').attr('disabled', 'true');
                $('select').attr('disabled', 'true');
                $('.btn.dropdown-toggle').addClass('disabled');
                $(".container_date .disable_datepicker").show();

                $('.add_participant .delete').hide();
                $('.add_participant:last-child()').hide();

                $('.scheme .participant').removeClass('dragdrop');
            }
            if(status > 2) {
                 $.get(location.origin +  "/get_scheme/" + winterhour_id, function( data ) {
                    $scope.scheme = data.scheme;
                    $scope.play_times = data.play_times;
                    $scope.scheme_exists = true;
                    $scope.$apply();
                    make_drag_and_droppable();
                });
            }
            if(status != 4) {
                //only if the scheme is not accepted yet, drag and drop is allowed
                make_drag_and_droppable();
            }
        });
        /*
        //set days of weeks disabled according to selected weekday by triggering a change in the weekday (since all dates will be set to active, get real active dates afterwards)
        $('.day_select select').trigger( "change" );
        //get the dates from the current winterhour to set them as active for the datepicker
        $.get(location.origin +  "/get_winterhour_dates", { winterhour_id: winterhour_id }, function( data ) {
            //console.log(data);
            console.log('is this what is causing the trouble??');
            var dates = [];
            $.each( data, function( key, value ) {
                var date = value.date;
                date = date.split('-');
                date.reverse();
                date = date.join('/');
                //console.log( date );
                dates.push(date);
            });
            dates.reverse();
            //console.log(dates);
            $('.container_date').datepicker('setDates', dates);
        });
        */

        function make_drag_and_droppable() {
            jQuery.fn.swap = function(b){ 
                // method from: http://blog.pengoworks.com/index.cfm/2008/9/24/A-quick-and-dirty-swap-method-for-jQuery
                b = jQuery(b)[0]; 
                var a = this[0]; 
                var t = a.parentNode.insertBefore(document.createTextNode(''), a); 
                b.parentNode.insertBefore(a, b); 
                t.parentNode.insertBefore(b, t); 
                t.parentNode.removeChild(t); 
                return this; 
            };

            $( ".dragdrop" ).draggable({ revert: true, helper: "clone", cursor: "move" });

            $( ".dragdrop" ).droppable({
                accept: ".dragdrop",
                activeClass: "ui-state-hover",
                hoverClass: "ui-state-active",
                drop: function( event, ui ) {
                    console.log('test');
                    var draggable = ui.draggable;
                    var droppable = $(this);
                    var dragPos = draggable.position(), dropPos = droppable.position();
                    console.log(draggable[0]);
                    console.log(droppable[0]);
                    var user_id1 = $(draggable[0]).attr('user_id');
                    var date_id1 = $(draggable[0]).attr('date_id');
                    var user_id2 = $(droppable[0]).attr('user_id');
                    var date_id2 = $(droppable[0]).attr('date_id');
                    var swapdata = { swap1 : {user_id : user_id1, date_id : date_id1}, swap2 : {user_id : user_id2, date_id : date_id2} };
                    console.log(swapdata);

                    //check if participants can be swapped
                    
                    $.post(location.origin +  "/swap_places", swapdata, function( data ) {
                        console.log( data );
                        $('.swap_message').removeClass('success failed');
                        $('.swap_message').addClass(data.status);
                        $('.swap_message').text(data.message);
                        $('.swap_message').slideDown( "slow", function() {
                            // Animation complete.
                            setTimeout(function() {
                                $('.swap_message').slideUp();
                            }, 5000);
                        });
                        //only if the swap ws succesful, the swap may take place on the front-end as well
                        if(data.status == 'success') {
                            draggable.swap(droppable);
                            $(draggable[0]).attr('date_id', date_id2);
                            $(droppable[0]).attr('date_id', date_id1);
                        }
                    }, "json");
                    
                    
                }
            });
        }
        
        
    }

    //check if a certain step is given
    var step = findGetParameter('step');
    if(typeof step !== 'undefined') {
        //console.log('step is: ' + step);
        //trigger click on this step
        $('.timeline .step' + step).trigger('click');
    }

    function findGetParameter(parameterName) {
        var result = null,
            tmp = [];
        location.search
        .substr(1)
            .split("&")
            .forEach(function (item) {
            tmp = item.split("=");
            if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        });
        return result;
    }

    });

})(window, window.document, window.jQuery);