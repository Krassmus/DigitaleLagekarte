STUDIP.Lagekarte = {
    map: null,
    pois: {},
    temporary_layer: null,
    featureGroup: null,
    periodicalPushData: function () {
        return {
            'current_map': jQuery("#current_map").val() === "true" ? 1 : 0,
            'map_id': jQuery("#map_id").val(),
            'last_update': jQuery("#last_update").val()
        };
    },
    updateMap: function (mapdata) {
        if (mapdata['map_id'] !== jQuery("#map_id").val()) {
            jQuery("#map_id").val(mapdata['map_id']);
        }
        var existing_ids = [];
        if (mapdata.poi) {
            jQuery.each(mapdata.poi, function (index, poi) {
                STUDIP.Lagekarte.draw_poi(poi.poi_id, poi.type, poi.coordinates, poi.radius, poi.image, "ha!");
                existing_ids.push(poi.poi_id);
            });
        }
        //noch die gel�schten POIs l�schen:
        jQuery.each(STUDIP.Lagekarte.pois, function (poi_id, layer) {
            if (_.indexOf(mapdata.poi_ids, poi_id) === -1) {
                var fadeOut = function (map, layer, opacity) {
                    opacity -= 0.05;
                    layer.setOpacity(opacity > 0 ? opacity : 0);
                    if (opacity > 0) {
                        window.setTimeout(function () { fadeOut(STUDIP.Lagekarte.map, layer, opacity); }, 50);
                    } else {
                        map.removeLayer(layer);
                    }
                }
                window.setTimeout(function () { fadeOut(STUDIP.Lagekarte.map, layer, 1); }, 50);
                delete STUDIP.Lagekarte.pois[poi_id];
            }
        });
        
        jQuery('#last_update').val(Math.floor(new Date().getTime() / 1000));
    },
    draw_map: function (latitude, longitude, zoom) {
        STUDIP.Lagekarte.map = L.map('map', { 'attributionControl': false }).setView([latitude, longitude], zoom);
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {}).addTo(STUDIP.Lagekarte.map);
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
        } else {
            if (type === "marker") {
                coordinates = new L.LatLng(coordinates[1], coordinates[0]);
                STUDIP.Lagekarte.pois[id].setLatLng(coordinates);
            }
            if (type === "circle") {
                coordinates = new L.LatLng(coordinates[1], coordinates[0]);
                STUDIP.Lagekarte.pois[id].setLatLng(coordinates);
                STUDIP.Lagekarte.pois[id].setRadius(radius);
            }
            if (type === "polyline") {
                coordinates = _.map(coordinates, function (value) {
                    return new L.LatLng(value[1], value[0]);
                });
                STUDIP.Lagekarte.pois[id].setLatLngs(coordinates);
            }
            if (type === "polygon") {
                coordinates = _.map(coordinates, function (value1) {
                    return _.map(value1, function (value2) {
                        return new L.LatLng(value2[1], value2[0]);
                    });
                });
                STUDIP.Lagekarte.pois[id].setLatLngs(coordinates);
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
                featureGroup: STUDIP.Lagekarte.featureGROUP,
                
            }
        });
        STUDIP.Lagekarte.map.addControl(drawControl);

        STUDIP.Lagekarte.map.on('draw:created', function (e) {
            var type = e.layerType,
                layer = e.layer
                geometry = layer.toGeoJSON().geometry;

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
                'hide': "fade",
                close: function () {
                    if (STUDIP.Lagekarte.temporary_layer) {
                        STUDIP.Lagekarte.map.removeLayer(STUDIP.Lagekarte.temporary_layer);
                        STUDIP.Lagekarte.temporary_layer = null;
                    }
                }
            });

            STUDIP.Lagekarte.featureGROUP.addLayer(layer);
            STUDIP.Lagekarte.temporary_layer = layer;
        });
        
        
        STUDIP.Lagekarte.map.on('draw:edited', function (e) {
            var layers = e.layers;
            var geometries = {};
            layers.eachLayer(function (layer) {
                //do whatever you want, most likely save back to db
                geometries[layer.feature_id] = {
                    'coordinates': layer.toGeoJSON().geometry.coordinates,
                    'radius': layer._mRadius
                }
            });
            jQuery.ajax({
                'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/map/edit_poi",
                'type': "post",
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
            'type': "post",
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
            'type': "post",
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
            'type': "post",
            'success': function () {
                jQuery("#save_map_viewport_spinner").hide('swing');
            }
        });
    },
    create_snapshot: function () {
        jQuery("#create_snapshot_spinner").show('swing');
        jQuery.ajax({
            'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/map/create_snapshot",
            'data': {
                'cid': $("#seminar_id").val()
            },
            'type': "post",
            'success': function () {
                jQuery("#create_snapshot_spinner").hide('swing');
            }
        });
    }
};