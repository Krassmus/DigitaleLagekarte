<!--[if lte IE 8]>
    <link rel="stylesheet" href="<?= $this->plugin->getPluginURL() ?>/assets/Leaflet/leaflet.ie.css" />
    <link rel="stylesheet" href="<?= $this->plugin->getPluginURL() ?>/assets/Leaflet/leaflet.draw.ie.css" />
<![endif]-->

<div id="map" style="width: 100%; height: 500px; margin-left: 5px; margin-right: 5px;"></div>

<input type="hidden" id="seminar_id" value="<?= $_SESSION['SessionSeminar'] ?>">

<script>
jQuery(function () {
    STUDIP.Lagekarte.draw_map(<?= (double) $map['latitude'] ?>, <?= (double) $map['longitude'] ?>, <?= (int) $map['zoom'] ?>);
    STUDIP.Lagekarte.edit_map();
});
</script>

<? 
$infobox = array(
    array("kategorie" => _("Informationen"),
          "eintrag"   =>
        array(
            array(
                "icon" => "icons/16/black/info",
                "text" => _("Hier sehen Sie die aktuelle Lagekarte")
            )
        )
    ),
    $GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar'])
        ? array("kategorie" => _("Aktionen"),
            "eintrag"   =>
            array(
                array(
                    "icon" => "icons/16/black/add",
                    "text" => '<label><input type="radio" name="action" value="point">'._("Punkt einzeichnen").'</label> <br>'
                             .'<label><input type="radio" name="action" value="line">'._("Pfad einzeichnen").'</label> <br>'
                             .'<label><input type="radio" name="action" value="point">'._("Fläche einzeichnen").'</label>'
                ),array(
                    "icon" => "icons/16/black/tools",
                    "text" => '<label><input type="radio" name="action" value="point">'._("Punkt verschieben").'</label> <br>'
                             .'<label><input type="radio" name="action" value="line">'._("Pfad einzeichnen").'</label> <br>'
                             .'<label><input type="radio" name="action" value="point">'._("Fläche einzeichnen").'</label>'
                ),
                array(
                    "icon" => "icons/16/black/edit",
                    "text" => '<a href="#" onClick="STUDIP.Lagekarte.save_map_viewport(); return false;">'._("Bildsausschnitt speichern.").'</a>' 
                                .'<span style="display: none;" id="save_map_viewport_spinner">'.Assets::img("ajax_indicator_small.gif", array('class' => "text-bottom")).'</span>'
                )
            )
        )
        : null
);
$infobox = array(
    'picture' => $GLOBALS['ABSOLUTE_URI_STUDIP'].$plugin->getPluginPath()."/assets/Lagekarte-4.jpg",
    'content' => $infobox
);