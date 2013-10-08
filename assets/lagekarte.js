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
        if (mapdata['longitude'] !== jQuery("#original_lon").val() 
                || mapdata['latitude'] !== jQuery("#original_lat").val()
                || mapdata['zoom'] !== jQuery("#original_zoom").val()) {
            STUDIP.Lagekarte.map.setZoom(mapdata['zoom'], {
                'animate': false
            });
            STUDIP.Lagekarte.map.panTo({lon: mapdata['longitude'], lat: mapdata['latitude']}, {
                'animate': true,
                'duration': 1
            });
            jQuery("#original_zoom").val(mapdata['zoom']);
            jQuery("#original_lon").val(mapdata['longitude']);
            jQuery("#original_lat").val(mapdata['latitude']);
        }
        var existing_ids = [];
        var layer;
        if (mapdata.poi) {
            jQuery.each(mapdata.poi, function (index, poi) {
                layer = STUDIP.Lagekarte.draw_poi(poi.poi_id, poi.type, poi.coordinates, poi.radius, poi.color, poi.image, poi.popup);
                if (typeof STUDIP.Lagekarte.pois[poi.poi_id] === "undefined") {
                    STUDIP.Lagekarte.fadeInLayer(layer);
                }
                existing_ids.push(poi.poi_id);
            });
        }
        //noch die gelöschten POIs löschen:
        jQuery.each(STUDIP.Lagekarte.pois, function (poi_id, layer) {
            if (_.indexOf(mapdata.poi_ids, poi_id) === -1) {
                STUDIP.Lagekarte.fadeOutLayer(layer);
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
    draw_poi: function (id, type, coordinates, radius, color, image, popup) {
        if (typeof STUDIP.Lagekarte.pois[id] === "undefined") {
            var new_object = null;
            
            if (type === "marker") {
                coordinates = new L.LatLng(coordinates[1], coordinates[0]);
                new_object = new L.Marker(coordinates, {});
                new_object.setIcon(STUDIP.Lagekarte.get_icon(image));
            }
            if (type === "circle") {
                coordinates = new L.LatLng(coordinates[1], coordinates[0]);
                new_object = new L.Circle(coordinates, radius, {'color': color, 'weight': "2"});
            }
            if (type === "polyline") {
                coordinates = _.map(coordinates, function (value) {
                    return new L.LatLng(value[1], value[0]);
                });
                new_object = new L.Polyline(coordinates, {'color': color});
            }
            if (type === "polygon") {
                coordinates = _.map(coordinates, function (value1) {
                    return _.map(value1, function (value2) {
                        return new L.LatLng(value2[1], value2[0]);
                    });
                });
                new_object = new L.Polygon(coordinates, {'color': color, 'weight': "2"});
            }
            if (new_object !== null) {
                new_object.feature_id = id;
                if (popup) {
                    new_object.bindPopup(popup);
                }
                STUDIP.Lagekarte.pois[id] = new_object;
                new_object.addTo(STUDIP.Lagekarte.featureGROUP);
                return new_object;
            }
        } else {
            if (type === "marker") {
                coordinates = new L.LatLng(coordinates[1], coordinates[0]);
                STUDIP.Lagekarte.moveMarker(STUDIP.Lagekarte.pois[id], coordinates);
                STUDIP.Lagekarte.pois[id].setIcon(STUDIP.Lagekarte.get_icon(image));
            }
            if (type === "circle") {
                coordinates = new L.LatLng(coordinates[1], coordinates[0]);
                STUDIP.Lagekarte.moveCircle(STUDIP.Lagekarte.pois[id], coordinates, radius);
                STUDIP.Lagekarte.pois[id].setStyle({'color': color, 'fillColor': color});
            }
            if (type === "polyline") {
                coordinates = _.map(coordinates, function (value) {
                    return new L.LatLng(value[1], value[0]);
                });
                STUDIP.Lagekarte.pois[id].setLatLngs(coordinates);
                STUDIP.Lagekarte.pois[id].setStyle({'color': color, 'fillColor': color});
            }
            if (type === "polygon") {
                coordinates = _.map(coordinates, function (value1) {
                    return _.map(value1, function (value2) {
                        return new L.LatLng(value2[1], value2[0]);
                    });
                });
                STUDIP.Lagekarte.pois[id].setLatLngs(coordinates);
                STUDIP.Lagekarte.pois[id].setStyle({'color': color, 'fillColor': color});
            }
            STUDIP.Lagekarte.pois[id].bindPopup(popup);
        }
    },
    get_icon: function (image) {
        var image_url = STUDIP.ABSOLUTE_URI_STUDIP + "plugins_packages/THW/DigitaleLagekarte/assets/markers" + image;
        var shadow_url = STUDIP.ABSOLUTE_URI_STUDIP + "plugins_packages/THW/DigitaleLagekarte/assets/symbol_shadow.svg";
        if (image) {
            return new L.Icon({
                iconUrl: image_url,
                iconSize: [60, 40],
                iconAnchor: [30, 54],
                popupAnchor: [0, -36],
                shadowUrl: shadow_url,
                shadowSize: [80, 30],
                shadowAnchor: [40, 30]
            });
        } else {
            return new L.Icon.Default();
        }
    },
    edit_map: function () {
        var drawControl = new L.Control.Draw({
            draw: {
                position: 'topleft',
                polyline: {
                    shapeOptions: { color: 'blue'}
                },
                polygon: {
                    allowIntersection: false,
                    shapeOptions: {
                        color: 'blue',
                        weight: "2"
                    }
                },
                circle: {
                    shapeOptions: {
                        color: 'blue',
                        weight: "2"
                    }
                },
                rectangle: null //disable rectangles
            },
            edit: {
                featureGroup: STUDIP.Lagekarte.featureGROUP,
                edit: {
                    selectedPathOptions: null //so colors won't be overwritten
                }
                
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
                        STUDIP.Lagekarte.fadeOutLayer(STUDIP.Lagekarte.temporary_layer);
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
                    STUDIP.Lagekarte.pois[output.new_poi.id].feature_id = output.new_poi.id;
                    STUDIP.Lagekarte.pois[output.new_poi.id].bindPopup(output.new_poi.popup);
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
    },
    change_marker_image: function () {
        var poi_id = jQuery(this).closest("form").find("input[name=poi_id]").val();
        var value = this.value;
        STUDIP.Lagekarte.pois[poi_id].setIcon(STUDIP.Lagekarte.get_icon(value));
        
        jQuery.ajax({
            'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/map/edit_poi_attribute",
            'type': "POST",
            'data': {
                'cid': jQuery("#Seminar_id").val(),
                'poi_id': poi_id,
                'image': value
            }
        });
    },
    
    
    /* special effects section */
    fadeOutLayer: function (layer) {
        var starttime = new Date();
        var isMarker = layer instanceof L.Marker;
        var fadeOut = function (layer, duration) {
            var time = new Date();
            var opacity = 1 - (time - starttime) / duration;
            if (isMarker) {
                layer.setOpacity(opacity > 0 ? opacity : 0);
            } else {
                layer.setStyle({
                    opacity: opacity * 0.5,
                    fillOpacity: opacity * 0.2
                });
            }
            if (opacity > 0) {
                window.setTimeout(function () { fadeOut(layer, duration); }, 10);
            } else {
                STUDIP.Lagekarte.map.removeLayer(layer);
            }
        }
        fadeOut(layer, 300);
    },
    fadeInLayer: function (layer) {
        var starttime = new Date();
        var isMarker = layer instanceof L.Marker;
        var fadeIn = function (layer, duration) {
            var time = new Date();
            var opacity = (time - starttime) / duration;
            if (isMarker) {
                layer.setOpacity(opacity < 1 ? opacity : 1);
            } else {
                layer.setStyle({
                    opacity: opacity < 1 ? opacity * 0.5 : 0.5,
                    fillOpacity: opacity < 1 ? opacity * 0.2 : 0.2
                });
            }
            if (opacity < 1) {
                window.setTimeout(function () { fadeIn(layer, duration); }, 10);
            }
        }
        fadeIn(layer, 300);
    },
    moveMarker: function (layer, position) {
        var starttime = new Date();
        var originalPosition = layer.getLatLng();
        var moveMarker = function (layer, position, duration) {
            var time = new Date();
            var way = (time - starttime) / duration;
            var newPosition = new L.LatLng(
                (position.lat * way + originalPosition.lat * (1 - way)), 
                (position.lng * way + originalPosition.lng * (1 - way))
            );
            layer.setLatLng(way < 1 ? newPosition : position);
            if (way < 1) {
                window.setTimeout(function () { moveMarker(layer, position, duration); }, 10);
            }
        }
        moveMarker(layer, position, 1000);
    },
    moveCircle: function (layer, position, radius) {
        var starttime = new Date();
        var originalPosition = layer.getLatLng();
        var originalradius = layer.getRadius();
        var moveCircle = function (layer, position, radius, duration) {
            var time = new Date();
            var way = (time - starttime) / duration;
            var newPosition = new L.LatLng(
                (position.lat * way + originalPosition.lat * (1 - way)), 
                (position.lng * way + originalPosition.lng * (1 - way))
            );
            layer.setLatLng(way < 1 ? newPosition : position);
            layer.setRadius((radius * way + originalradius * (1 - way)));
            if (way < 1) {
                window.setTimeout(function () { moveCircle(layer, position, radius, duration); }, 10);
            }
        }
        moveCircle(layer, position, radius, 1000);
    },
    // external data urls:
    new_external_data_url: function () {
        jQuery("#new_external_data_url_window").dialog({
            'title': jQuery("#new_external_data_url_window_title").text(),
            'modal': true,
            'show': "fade",
            'hide': "fade"
        });
    },
    create_external_data_url: function () {
        jQuery.ajax({
            'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/externedaten/create_external_data_url",
            'data': {
                'cid': jQuery("#Seminar_id").val(),
                'name': jQuery("#new_external_data_url_window input[name=name]").val(),
                'url': jQuery("#new_external_data_url_window input[name=url]").val()
            },
            'type': "POST",
            'dataType': "json",
            'success': function (result) {
                location.href = result.link
            }
        });
    },
    toggle_external_data_url_activation: function () {
        var url = jQuery(this).closest("tr").attr("data-url");
        var icon = jQuery(this).find("img");
        var activated = (icon.attr('src').indexOf("checkbox-checked") !== -1);
        if (!activated) {
            icon.attr('src', icon.attr('src').replace("checkbox-unchecked", "checkbox-checked"));
        } else {
            icon.attr('src', icon.attr('src').replace("checkbox-checked", "checkbox-unchecked"));
        }
        jQuery.ajax({
            'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/externedaten/toggle_external_data_url_activation",
            'data': {
                'url': url,
                'active': activated ? 0 : 1,
                'cid': jQuery("#Seminar_id").val()
            },
            'type': "POST",
            'dataType': "json",
            'success': function (json) {
                //nothing
            }
        });
        return false;
    },
    show_matching_dialog: function () {
        var index = [];
        var parent = jQuery(this).closest("li, tr");
        var i = 0;
        while (parent.length > 0 && i < 10000) {
            if (jQuery(parent).is("li")) {
                index.push(parent.index());
            } else {
                index.push(parent.attr('data-key'));
            }
            parent = parent.parents("li, tr").first();
            i++;
        }
        jQuery.ajax({
            'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/externedaten/mapping_window",
            'data': {
                'url': jQuery("#url").val(),
                'path': index.reverse(),
                'cid': jQuery("#Seminar_id").val()
            },
            'dataType': "json",
            'success': function (json) {
                jQuery(json.html).dialog({
                    'title': jQuery("#mapping_window_title").text(),
                    'show': "fade",
                    'hide': "fade",
                    'modal': true,
                    'close': function() {
                        jQuery(this).remove();
                    }
                });
            }
        });
        
        return false;
    },
    map_external_data: function () {
        jQuery.ajax({
            'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/externedaten/edit_mapping",
            'data': {
                'url': jQuery("#url").val(),
                'path': jQuery("#mapping_path").val(),
                'cid': jQuery("#Seminar_id").val(),
                'poi_id': jQuery("#poi_id").val(),
                'poi_attribute': jQuery("#poi_attribute").val()
            },
            'type': "post",
            'dataType': "json",
            'success': function (json) {
                jQuery("#mapping_window").dialog('close');
            }
        });
    }
    
};

jQuery(function () {
    jQuery("#overview_schadenskonten > tbody > tr").bind("click", function () {
        location.href = jQuery(this).find("a").attr("href");
    });
    jQuery(".troopstrength").bind("keydown", function (event) {
        if (event.keyCode === 32) {
            jQuery(this).next().focus();
            event.stopPropagation();
            event.preventDefault();
            return false;
        }
        
    });
    jQuery(".troopstrength").bind("keyup", function () {
        jQuery("#gesamt").val(
            parseInt(jQuery("#fuehrer").val() || 0, 10)
            + parseInt(jQuery("#unterfuehrer").val() || 0, 10)
            + parseInt(jQuery("#helfer").val() || 0, 10)
        );
    });
    jQuery(".troopstrength").bind("blur", function () {
        if (!jQuery(this).val()) {
            jQuery(this).val(0);
        }
    });
    jQuery("#select_schadenskonto").bind("change", function () {
        location.href = STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/schadenskonten/konto/" + this.value + "?cid=" + jQuery("#seminar_id").val()
    });
    jQuery("select[name=poi_color]").live("change", function () {
        var poi_id = jQuery(this).closest("form").find("input[name=poi_id]").val();
        var color = this.value;
        STUDIP.Lagekarte.pois[poi_id].setStyle({'color': color, 'fillColor': color});
        jQuery.ajax({
            'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/map/edit_poi_color",
            'type': "post",
            'data': {
                'poi_id': poi_id,
                'color': color,
                'cid': jQuery("#seminar_id").val()
            }
        });
    });
    //Edit schadenskonto:
    jQuery(".editable .edit-icon").live('click', function () {
        jQuery(this).closest(".editable").addClass("edit").find(".input input").focus();
    });
    jQuery(".editable .input").live("blur", function () {
        var that = this;
        jQuery.ajax({
            'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/schadenskonten/edit",
            'data': {
                'cid': jQuery("#seminar_id").val(),
                'schadenskonto_id': jQuery("#schadenskonto_id").val(),
                'attribute': jQuery(this).find("input").attr("name"),
                'value': jQuery(this).find("input").val()
            },
            success: function (html) {
                jQuery(that).closest(".editable").find(".value").html(html);
                jQuery(that).closest(".editable").removeClass("edit");
                
            }
        });
    });
    jQuery(".pois").sortable({
        'placeholder': "empty_symbol",
        'revert': 200,
        'connectWith': ".pois"
    });
    jQuery(".pois > li").droppable({
        'accept': ".poi_batch",
        'hoverClass': "drophere"
    });
    jQuery("#select_schadenskonto > option").droppable({
        'accept': ".poi_batch",
        'hoverClass': "drophere",
        'drop': function (event, ui) {
            var poi_id = ui.draggable.attr('id').substr(ui.draggable.attr('id').lastIndexOf("_") + 1);
            var schadenskonto_id = this.value;
            jQuery.ajax({
                'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/digitalelagekarte/schadenskonten/move_poi_to_schadenskonto",
                'data': {
                    'poi_id': poi_id,
                    'schadenskonto_id': schadenskonto_id,
                    'cid': jQuery("#seminar_id").val()
                },
                'type': "post"
            });
            ui.draggable.hide('swing', function () { 
                jQuery(this).remove();
                jQuery(".pois li.empty_symbol").remove();
                jQuery(".pois").sortable("destroy");
                jQuery(".pois").sortable({
                    'placeholder': "empty_symbol",
                    'revert': 200
                });
            });
        }
    });
    
    jQuery(".json_object_list tr > td.match a").bind('click', STUDIP.Lagekarte.show_matching_dialog);
    jQuery("#url_overview .checkbox").live("click", STUDIP.Lagekarte.toggle_external_data_url_activation);
    jQuery(".poi_popup select[name=poi_image]").live("change", STUDIP.Lagekarte.change_marker_image);
});
