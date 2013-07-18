STUDIP.Lagekarte = {
    map: null,
    draw_map: function (longitude, latitude, zoom) {
        STUDIP.Lagekarte.map = L.map('map').setView([latitude, longitude], zoom);
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            //attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(STUDIP.Lagekarte.map);
    },
    edit_map: function () {
        // Initialize the FeatureGroup to store editable layers
        var drawnItems = new L.FeatureGroup();
        STUDIP.Lagekarte.map.addLayer(drawnItems);

        // Initialize the draw control and pass it the FeatureGroup of editable layers
        var drawControl = new L.Control.Draw({
            edit: {
                featureGroup: drawnItems
            }
        });
        STUDIP.Lagekarte.map.addControl(drawControl);
    },
    save_map: function () {
        var zoom = STUDIP.Lagekarte.map.getZoom();
        var center = STUDIP.Lagekarte.map.getCenter();
        var longitude = center.lng;
        var latitude = center.lat;
        $.ajax({
            'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/save_map",
            'data': {
                'zoom': zoom,
                'longitude': longitude,
                'latitude': latitude,
                'cid': $("#seminar_id").val()
            },
            'success': function () {
                location.href = STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/show?cid=" + $("#seminar_id").val()
            }
        });
    }
};