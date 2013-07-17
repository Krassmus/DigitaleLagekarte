STUDIP.Lagekarte = {
    map: null,
    map_projection: "EPSG:900913",
    draw_map: function (longitude, latitude, zoom) {
        STUDIP.Lagekarte.map = new OpenLayers.Map("map", {
            controls: [
                new OpenLayers.Control.Navigation(),
                new OpenLayers.Control.PanZoom()
            ]
        });
        var mapnik = new OpenLayers.Layer.OSM();
        STUDIP.Lagekarte.map.addLayer(mapnik);
        STUDIP.Lagekarte.map.setCenter(new OpenLayers.LonLat(longitude,latitude) // Center of the map
            .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            new OpenLayers.Projection("EPSG:900913") // to Spherical Mercator Projection
            ), zoom // Zoom level
        );
    },
    edit_map: function () {
        var vlayer = new OpenLayers.Layer.Vector( "Editable" );
        STUDIP.Lagekarte.map.addControl(new OpenLayers.Control.EditingToolbar(vlayer));
        STUDIP.Lagekarte.map.addLayer(vlayer);
    },
    save_map: function () {
        var zoom = STUDIP.Lagekarte.map.getZoom();
        var center = STUDIP.Lagekarte.map.getCenter();
        var lonlat = center.transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));
        var longitude = lonlat.lon;
        var latitude = lonlat.lat;
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
    },
    
    draw_map_leaflet: function (longitude, latitude, zoom) {
        STUDIP.Lagekarte.map = L.map('map').setView([latitude, longitude], zoom);
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            //attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(STUDIP.Lagekarte.map);
    },
    edit_map_leaflet: function () {
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
    save_map_leaflet: function () {
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