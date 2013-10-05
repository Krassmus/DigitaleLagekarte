<!--[if lte IE 8]>
    <link rel="stylesheet" href="<?= $this->assets_url ?>Leaflet/leaflet.ie.css" />
    <link rel="stylesheet" href="<?= $this->assets_url ?>Leaflet/leaflet.draw.ie.css" />
<![endif]-->

<style>
    .leaflet-popup-content-wrapper {
        border-radius: 3px;
    }
</style>

<div id="map" style="width: 100%; height: 500px; margin-left: 5px; margin-right: 5px;"></div>

<input type="hidden" id="current_map" value="true">
<input type="hidden" id="map_id" value="<?= $map->getId() ?>">
<input type="hidden" id="last_update" value="<?= time() ?>">
<input type="hidden" id="seminar_id" value="<?= $_SESSION['SessionSeminar'] ?>">
<input type="hidden" id="original_lat" value="<?= $map['latitude'] ?>">
<input type="hidden" id="original_lon" value="<?= $map['longitude'] ?>">
<input type="hidden" id="original_zoom" value="<?= $map['zoom'] ?>">

<script>
jQuery(function () {
    STUDIP.Lagekarte.draw_map(<?= (double) $map['latitude'] ?>, <?= (double) $map['longitude'] ?>, <?= (int) $map['zoom'] ?>);
    <? foreach ($schadenskonten as $schadenskonto) : ?>
        <? foreach ($schadenskonto->getPOIs() as $poi) : ?>
            <? if ($poi['visible']) : ?>
            STUDIP.Lagekarte.draw_poi(
                '<?= $poi->getId() ?>',
                '<?= htmlReady($poi['shape']) ?>',
                <?= json_encode($poi['coordinates']) ?>,
                <?= (int) $poi['radius'] ?>,
                '<?= htmlReady($poi['color'] ? $poi['color'] : "blue") ?>',
                '<?= htmlReady($poi['image']) ?>',
                "<?= addslashes(str_replace("\n", "", $this->render_partial("map/_poi_popup.php", array('poi' => $poi, 'images' => $images)))) ?>"
            );
            <? endif ?>
        <? endforeach ?>
    <? endforeach ?>
    <? if ($GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar'])) : ?>
    STUDIP.Lagekarte.edit_map();
    <? endif ?>
});
window.setInterval(function () {
    jQuery("body").trigger("mousemove");
}, 3000);
</script>

<div style="display: none;" id="create_poi_window_title"><?= _("Objekt zuordnen") ?></div>
<div style="display: none;" id="create_poi_window">
    <input type="hidden" name="type" value="">
    <input type="hidden" name="coordinates" value="">
    <input type="hidden" name="radius" value="">
    <table>
        <tbody>
            <tr>
                <td><label for="title"><?= _("Name") ?></label></td>
                <td><input type="text" value="" name="title" id="title"></td>
            </tr>
            <tr>
                <td><label for="schadenskonto_id"><?= _("Schadenskonto") ?></label></td>
                <td>
                    <select name="schadenskonto_id" id="schadenskonto_id" required onChange="this.value==='neu' ? jQuery('#schadenskonto_title').fadeIn() : jQuery('#schadenskonto_title').fadeOut(); ">
                        <option value=""><?= _("---") ?></option>
                        <? foreach ($schadenskonten as $schadenskonto) : ?>
                        <option value="<?= htmlReady($schadenskonto->getId()) ?>"><?= htmlReady($schadenskonto['title']) ?></option>
                        <? endforeach ?>
                        <option value="neu"><?= _("neues Konto") ?></option>
                    </select>
                    <br>
                    <input type="text" name="schadenskonto_title" id="schadenskonto_title" aria-label="<?= _("Name des neuen Kontos") ?>" style="display: none;" placeholder='<?= _("Name des neuen Kontos") ?>'>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><?= \Studip\Button::create(_("speichern"), 'send', array('onClick' => "STUDIP.Lagekarte.save_new_layer();")) ?></td>
            </tr>
        </tbody>
    </table>
</div>

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
                    "icon" => "icons/16/black/edit",
                    "text" => '<a href="#" onClick="STUDIP.Lagekarte.save_map_viewport(); return false;">'._("Bildsausschnitt speichern.").'</a>' 
                                .'<span style="display: none;" id="save_map_viewport_spinner">'.Assets::img("ajax_indicator_small.gif", array('class' => "text-bottom")).'</span>'
                ),
                array(
                    "icon" => "icons/16/black/date",
                    "text" => '<a href="#" onClick="STUDIP.Lagekarte.create_snapshot(); return false;">'._("Snapshot der Karte anlegen.").'</a>' 
                                .'<span style="display: none;" id="create_snapshot_spinner">'.Assets::img("ajax_indicator_small.gif", array('class' => "text-bottom")).'</span>'
                )
            )
        )
        : null
);
$infobox = array(
    'picture' => $assets_url."Lagekarte-4.jpg",
    'content' => $infobox
);