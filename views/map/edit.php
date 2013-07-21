<!--[if lte IE 8]>
    <link rel="stylesheet" href="<?= $this->assets_url ?>Leaflet/leaflet.ie.css" />
    <link rel="stylesheet" href="<?= $this->assets_url ?>Leaflet/leaflet.draw.ie.css" />
<![endif]-->

<div id="map" style="width: 100%; height: 500px; margin-left: 5px; margin-right: 5px;"></div>

<input type="hidden" id="seminar_id" value="<?= $_SESSION['SessionSeminar'] ?>">

<script>
jQuery(function () {
    STUDIP.Lagekarte.draw_map(<?= (double) $map['latitude'] ?>, <?= (double) $map['longitude'] ?>, <?= (int) $map['zoom'] ?>);
    STUDIP.Lagekarte.edit_map();
    <? foreach ($schadenskonten as $schadenskonto) : ?>
        <? foreach ($schadenskonto->getPOIs() as $poi) : ?>
            STUDIP.Lagekarte.draw_poi('<?= $poi->getId() ?>', '<?= htmlReady($poi['shape']) ?>', <?= json_encode($poi['coordinates']) ?>, <?= (int) $poi['radius'] ?>);
        <? endforeach ?>
    <? endforeach ?>
});
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
    'picture' => $assets_url."Lagekarte-4.jpg",
    'content' => $infobox
);