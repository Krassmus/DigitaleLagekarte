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
        /*var drawnItems = new L.FeatureGroup();
        STUDIP.Lagekarte.map.addLayer(drawnItems);

        // Initialize the draw control and pass it the FeatureGroup of editable layers
        var drawControl = new L.Control.Draw({
            edit: {
                featureGroup: drawnItems
            }
        });
        STUDIP.Lagekarte.map.addControl(drawControl);

        STUDIP.Lagekarte.map.on('draw:created', function (e) {
            var type = e.layerType,
                layer = e.layer;

            if (type === 'marker') {
                // Do marker specific actions
            }

            // Do whatever else you need to. (save to db, add to map etc)
            STUDIP.Lagekarte.map.addLayer(layer);
        })

        STUDIP.Lagekarte.map.on('draw:edited', function (e) {
            var layers = e.layers;
            layers.eachLayer(function (layer) {
                //do whatever you want, most likely save back to db
            });
        });*/

        var drawnItems = new L.FeatureGroup();
        STUDIP.Lagekarte.map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            draw: {
                position: 'topleft',
                polygon: {
                    title: 'Draw a sexy polygon!',
                    allowIntersection: false,
                    drawError: {
                        color: '#b00b00',
                        timeout: 1000
                    },
                    shapeOptions: {
                        color: '#bada55'
                    },
                    showArea: true
                },
                circle: {
                    shapeOptions: {
                        color: '#662d91'
                    }
                }
            },
            edit: {
                featureGroup: drawnItems
            }
        });
        STUDIP.Lagekarte.map.addControl(drawControl);

        STUDIP.Lagekarte.map.on('draw:created', function (e) {
            var type = e.layerType,
                layer = e.layer;

            if (type === 'marker') {
                layer.bindPopup('A popup!');
            }

            drawnItems.addLayer(layer);
        });
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