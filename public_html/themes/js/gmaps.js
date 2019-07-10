if ($('#map').length) {
    function initialize() {
        var myLatLng = {lat: $("#map").data('lat'), lng: $("#map").data('lng')};
        var map = new google.maps.Map(document.getElementById('map'), {
            center: myLatLng,
            draggable: true,
            scrollwheel: false,
            zoom: 16
        });

        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            title: 'webcomum!',
            icon: '/themes/images/contactos/maps_picker.png'
        });
    }
    google.maps.event.addDomListener(window, 'load', initialize);
}
