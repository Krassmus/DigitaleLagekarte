<!--[if lte IE 8]>
    <link rel="stylesheet" href="<?= $plugin->getPluginURL() ?>/assets/Leaflet/leaflet.ie.css" />
    <link rel="stylesheet" href="<?= $plugin->getPluginURL() ?>/assets/Leaflet/leaflet.draw.ie.css" />
<![endif]-->

<div id="map" style="width: 100%; height: 500px; margin-left: 5px; margin-right: 5px;"></div>


<script>
jQuery(function () {
    STUDIP.Lagekarte.draw_map(<?= (double) $map['latitude'] ?>, <?= (double) $map['longitude'] ?>, <?= (int) $map['zoom'] ?>);
});
</script>

<? 
$infobox = array(
    array("kategorie" => _("Aktionen"),
          "eintrag"   =>
        array(
            array(
                "icon" => "icons/16/black/edit",
                "text" => '<a href="'.URLHelper::getLink("plugins.php/digitalelagekarte/map/edit").'">'._("Lagekarte bearbeiten.").'</a>'
            )
        )
    )
);
$infobox = array(
    'picture' => $assets_url."Lagekarte-4.jpg",
    'content' => $infobox
);