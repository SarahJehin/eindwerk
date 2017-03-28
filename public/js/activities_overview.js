(function ( window, document, $, undefined ) {

	$('#calendar').fullCalendar({
     locale: 'nl-be',
     weekends: true,
     defaultView: 'month',
     header: {
     left: 'prev,next today',
     center: 'title',
     right: 'month,agendaWeek,agendaDay'
     },
     editable: false,
     eventLimit: true, // allow "more" link when too many events
     timeFormat: 'H:mm',
     events: {
         url: base_url + '/api/get_activities',
             error: function() {
                 //will be executed because the api route doesn't exist yet
                 //alert("home.blade.php:52 cannot load json, are you sure " + base_url + '/api exists?');
                 console.log("home.blade.php:52 cannot load json, are you sure " + base_url + '/api exists?');
             }
         }
         
     /*
     events: [
        {
            title  : 'event1',
            start  : '2017-03-01'
        },
        {
            title  : 'event2',
            start  : '2017-03-05',
            end    : '2017-03-07'
        },
        {
            title  : 'event3',
            start  : '2017-03-09T12:30:00',
            allDay : false // will make the time show
        },
        {
            title  : 'event4',
            start  : '2017-03-25 12:30:00',
            end    : '2017-03-25 15:45:00'
        }
    ]
    */
     });
})(window, window.document, window.jQuery);