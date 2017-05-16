function initAutocomplete() {
    var basicLatLng = {lat: 51.083253, lng: 4.805906};
    if($('#latitude').val() && $('#longitude').val()) {
        var latitude = parseFloat($('#latitude').val());
        var longitude = parseFloat($('#longitude').val());
        basicLatLng = {lat: latitude, lng: longitude};
    }
    var map = new google.maps.Map(document.getElementById('map'), {
        center: basicLatLng,
        zoom: 12,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    // Create the search box and link it to the UI element.
    var input = document.getElementById('place-input');
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    // Bias the SearchBox results towards current map's viewport.
    map.addListener('bounds_changed', function() {
        searchBox.setBounds(map.getBounds());
    });

    var markers = [];

    //if latitude and longitude contain old values or values from existing activity, render the marker on the map
    if($('#latitude').val() && $('#longitude').val()) {
        //basic marker
        var basicMarker = new google.maps.Marker({
            position: basicLatLng,
            map: map,
            title: 'Locatie',
            draggable: true
          });
        
        basicMarker.setMap(map);
        
        markers.push(basicMarker);
    }
    //if location = sportiva, render a non draggable marker on the map
    if($('input[name="location_type"]').val() == 'sportiva') {
        var basicMarker = new google.maps.Marker({
            position: basicLatLng,
            map: map,
            title: 'Locatie'
          });
        
        basicMarker.setMap(map);
        
        markers.push(basicMarker);
    }
    //if other location is selected, clear all markers from map
    $(".loc_else").click(function(){
        // Clear out the old markers.
        markers.forEach(function(marker) {
            marker.setMap(null);
        });
        markers = [];
    });
    //when sportiva location is selected, put marker back on map
    $(".loc_sportiva").click(function(){
        // Clear out the old markers.
        markers.forEach(function(marker) {
            marker.setMap(null);
        });
        markers = [];

        var basicMarker = new google.maps.Marker({
            position: basicLatLng,
            map: map,
            title: 'Locatie'
          });
        
        basicMarker.setMap(map);
        
        markers.push(basicMarker);
        map.setCenter(basicLatLng);
    });

    // Listen for the event fired when the user selects a prediction and retrieve
    // more details for that place.
    searchBox.addListener('places_changed', function() {
        var places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        // Clear out the old markers.
        markers.forEach(function(marker) {
            marker.setMap(null);
        });
        markers = [];

        // For each place, get the icon, name and location.
        var bounds = new google.maps.LatLngBounds();
        places.forEach(function(place) {
            var icon = {
                url: place.icon,
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(25, 25)
            };

            // Create a marker for each place.
            markers.push(new google.maps.Marker({
                map: map,
                title: place.name,
                position: place.geometry.location,
                draggable: true
            }));

            //console.log(place.geometry.location.lat());
            //ook hier gaan we de latitude en longitude toevoegen aan de hidden inputs
            $("#latitude").val(place.geometry.location.lat().toFixed(5));
            $("#longitude").val(place.geometry.location.lng().toFixed(5));

            if (place.geometry.viewport) {
                // Only geocodes have viewport.
                bounds.union(place.geometry.viewport);
            } else {
                bounds.extend(place.geometry.location);
            }
        });
        //dit hieronder ga je doen, zodat er telkens maar 1 marker zichtbaar is, de rest wordt dus niet op de map gezet
        for(var i = 1; i < markers.length; i++) {
            markers[i].setMap(null);
        }

        map.fitBounds(bounds);

        google.maps.event.addListener(markers[0], 'dragend', function (evt) {
            //de coördinaten moeten in hidden inputs gestoken worden
            $("#latitude").val(evt.latLng.lat().toFixed(5));
            $("#longitude").val(evt.latLng.lng().toFixed(5));
            //document.getElementById('current').innerHTML = '<p>Marker dropped: Current Lat: ' + evt.latLng.lat().toFixed(3) + ' Current Lng: ' + evt.latLng.lng().toFixed(3) + '</p>';

            //het nieuwe adres moet in de search input worden weergegeven
            $.getJSON( "https://maps.googleapis.com/maps/api/geocode/json?latlng="+evt.latLng.lat().toFixed(3) + ","+evt.latLng.lng().toFixed(3), function( data ) {
                //console.log(data.results[0].formatted_address);
                $("#place-input").val(data.results[0].formatted_address);
            });
        });

        google.maps.event.addListener(markers[0], 'dragstart', function (evt) {
            //document.getElementById('current').innerHTML = '<p>Currently dragging marker...</p>';
        });


    });
}

//to prevent submitting the form when hitting enter on the google maps search
$('#add_activity').on('submit', function(){
    if ($('input:focus').length){return false;}
});