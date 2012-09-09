<div id="map" style="width: 600px; height: 500px;"></div>

<input type="hidden" id="seminar_id" value="<?= $_SESSION['SessionSeminar'] ?>">

<script>
jQuery(function () {
    STUDIP.Lagekarte.draw_map(<?= (double) $map['longitude'] ?>, <?= (double) $map['latitude'] ?>, <?= (int) $map['zoom'] ?>);
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
                "text" => _("Hier sehen Sie immer die aktuelle Lagekarte")
            )
        )
    ),
    $GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar'])
        ? array("kategorie" => _("Aktionen"),
            "eintrag"   =>
            array(
                array(
                    "icon" => "icons/16/black/edit",
                    "text" => '<a href="#" onClick="STUDIP.Lagekarte.save_map(); return false;">'._("Lagekarte speichern.").'</a>'
                )
            )
        )
        : null
);
$infobox = array(
    'picture' => $GLOBALS['ABSOLUTE_URI_STUDIP'].$plugin->getPluginPath()."/assets/Lagekarte-4.jpg",
    'content' => $infobox
);