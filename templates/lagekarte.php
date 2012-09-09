<div id="map" style="width: 600px; height: 500px;"></div>


<script>
jQuery(function () {
    STUDIP.Lagekarte.draw_map(<?= (double) $map['longitude'] ?>, <?= (double) $map['latitude'] ?>, <?= (int) $map['zoom'] ?>);
});
</script>

<? 
$infobox = array(
    array("kategorie" => _("Aktionen"),
          "eintrag"   =>
        array(
            array(
                "icon" => "icons/16/black/edit",
                "text" => '<a href="'.URLHelper::getLink("plugins.php/digitalelagekarte/edit_map").'">'._("Lagekarte bearbeiten.").'</a>'
            )
        )
    )
);
$infobox = array(
    'picture' => $GLOBALS['ABSOLUTE_URI_STUDIP'].$plugin->getPluginPath()."/assets/Lagekarte-4.jpg",
    'content' => $infobox
);