STUDIP.Lagekarte = {
    map: null,
    pois: {},
    temporary_layer: null,
    featureGroup: null,
    draw_map: function (latitude, longitude, zoom) {
        STUDIP.Lagekarte.map = L.map('map', { 'attributionControl': false }).setView([latitude, longitude], zoom);
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            //attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(STUDIP.Lagekarte.map);
        L.control.scale().addTo(STUDIP.Lagekarte.map);
        STUDIP.Lagekarte.map.addControl(new L.Control.FullScreen());
        STUDIP.Lagekarte.featureGROUP = new L.FeatureGroup();
        STUDIP.Lagekarte.map.addLayer(STUDIP.Lagekarte.featureGROUP);
    },
    draw_poi: function (id, type, coordinates, radius, image, popup) {
        if (typeof STUDIP.Lagekarte.pois[id] === "undefined") {
            var new_object = null;
            
            if (type === "marker") {
                coordinates = new L.LatLng(coordinates[1], coordinates[0]);
                new_object = new L.Marker(coordinates, {});
            }
            if (type === "circle") {
                coordinates = new L.LatLng(coordinates[1], coordinates[0]);
                new_object = new L.Circle(coordinates, radius);
            }
            if (type === "polyline") {
                coordinates = _.map(coordinates, function (value) {
                    return new L.LatLng(value[1], value[0]);
                });
                new_object = new L.Polyline(coordinates, {});
            }
            if (type === "polygon") {
                coordinates = _.map(coordinates, function (value1) {
                    return _.map(value1, function (value2) {
                        return new L.LatLng(value2[1], value2[0]);
                    });
                });
                new_object = new L.MultiPolygon(coordinates, {});
            }
            if (new_object !== null) {
                new_object.feature_id = id;
                if (popup) {
                    new_object.bindPopup(popup);
                }
                STUDIP.Lagekarte.pois[id] = new_object;
                new_object.addTo(STUDIP.Lagekarte.featureGROUP);
            }
        }
    },
    edit_map: function () {
        var drawControl = new L.Control.Draw({
            draw: {
                position: 'topleft',
                polyline: {
                    shapeOptions: { color: '#222277'}
                },
                polygon: {
                    allowIntersection: false,
                    drawError: {
                        color: '#b00b00',
                        timeout: 1000
                    },
                    shapeOptions: {
                        color: '#222277'
                    },
                    showArea: true
                },
                circle: {
                    drawError: {
                        color: '#b00b00',
                        timeout: 1000
                    },
                    shapeOptions: {
                        color: '#222277'
                    }
                },
                rectangle: null
            },
            edit: {
                featureGroup: STUDIP.Lagekarte.featureGROUP
            }
        });
        STUDIP.Lagekarte.map.addControl(drawControl);

        STUDIP.Lagekarte.map.on('draw:created', function (e) {
            var type = e.layerType,
                layer = e.layer
                geometry = layer.toGeoJSON().geometry;

            //console.log(type);
            
            jQuery("#create_poi_window input[name=type]").val(type);
            jQuery("#create_poi_window input[name=coordinates]").val(JSON.stringify(geometry.coordinates));
            jQuery("#create_poi_window input[name=radius]").val(layer._mRadius);
            
            jQuery("#create_poi_window input[name=schadenskonto_title]").val("");
            jQuery("#create_poi_window input[name=title]").val("");
            jQuery("#create_poi_window select[name=schadenskonto_id]").val("");
            jQuery("#create_poi_window input[name=schadenskonto_title]").hide();
            
            jQuery("#create_poi_window").dialog({
                'title': jQuery("#create_poi_window_title").text(),
                'modal': true,
                'show': "fade",
                'hide': "fade"
            });

            STUDIP.Lagekarte.featureGROUP.addLayer(layer);
            STUDIP.Lagekarte.temporary_layer = layer;
        });
        
        
        STUDIP.Lagekarte.map.on('draw:edited', function (e) {
            var layers = e.layers;
            var geometries = {};
            layers.eachLayer(function (layer) {
                //do whatever you want, most likely save back to db
                console.log(layer.feature_id);
                geometries[layer.feature_id] = {
                    'coordinates': layer.toGeoJSON().geometry.coordinates,
                    'radius': layer._mRadius
                }
            });
            console.log(geometries);
            jQuery.ajax({
                'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/map/edit_poi",
                'data': {
                    'poi': geometries,
                    'cid': jQuery("#seminar_id").val()
                }
            });
            
        });
        
        STUDIP.Lagekarte.map.on('draw:deleted', function (e) {
            var layers = e.layers;
            var ids = [];
            layers.eachLayer(function (layer) {
                //these layers should be deleted
                ids.push(layer.feature_id);
            });
            STUDIP.Lagekarte.delete_poi(ids);
        });
        
    },
    delete_poi: function (poi_ids) {
        jQuery.ajax({
            'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/map/delete_poi",
            'data': {
                'poi_ids': poi_ids,
                'cid': jQuery("#seminar_id").val()
            }
        });
        jQuery.each(poi_ids, function (index, id) {
            delete STUDIP.Lagekarte.pois[id];
        });
    },
    save_new_layer: function () {
        jQuery.ajax({
            'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/map/save_new_layer",
            'data': {
                'shape': jQuery("#create_poi_window input[name=type]").val(),
                'coordinates': JSON.parse(jQuery("#create_poi_window input[name=coordinates]").val()),
                'radius': jQuery("#create_poi_window input[name=radius]").val(),
                'title': jQuery("#create_poi_window input[name=title]").val(),
                'schadenskonto_id': jQuery("#create_poi_window select[name=schadenskonto_id]").val(),
                'schadenskonto_title': jQuery("#create_poi_window input[name=schadenskonto_title]").val(),
                'image': jQuery("#create_poi_window input[name=image]:selected").val(),
                'cid': jQuery("#seminar_id").val()
            },
            dataType: "json",
            success: function (output) {
                if (output.new_schadenskonto) {
                    jQuery('<option/>')
                        .attr('value', output.new_schadenskonto.id)
                        .text(output.new_schadenskonto.name)
                        .prependTo("#create_poi_window select[name=schadenskonto_id]");
                }
                if (output.new_poi) {
                    STUDIP.Lagekarte.pois[output.new_poi.id] = STUDIP.Lagekarte.temporary_layer;
                    STUDIP.Lagekarte.temporary_layer = null;
                }
                jQuery("#create_poi_window").dialog("close");
            }
        });
    },
    save_map_viewport: function () {
        var zoom = STUDIP.Lagekarte.map.getZoom();
        var center = STUDIP.Lagekarte.map.getCenter();
        var longitude = center.lng;
        var latitude = center.lat;
        jQuery("#save_map_viewport_spinner").show('swing');
        jQuery.ajax({
            'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/map/save_viewport",
            'data': {
                'zoom': zoom,
                'longitude': longitude,
                'latitude': latitude,
                'cid': $("#seminar_id").val()
            },
            'success': function () {
                jQuery("#save_map_viewport_spinner").hide('swing');
            }
        });
    }
};