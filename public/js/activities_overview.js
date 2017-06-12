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
         url: location.origin + '/get_activities',
             error: function() {;
             }
         }
     });

    //if the screen is smartphone size, switch to month list view
    if (window.matchMedia("(max-width: 768px)").matches) {
        //when on a mobile change the view to listMonth-view, for one reason or another the events should be rerendered first
        $('#calendar').fullCalendar('rerenderEvents');
        $('#calendar').fullCalendar('changeView', 'listMonth');
        $('#calendar').fullCalendar('option', 'height', 400);
    }

})(window, window.document, window.jQuery);