
<style>
    .leaflet-popup-content-wrapper {
        border-radius: 3px;
    }
</style>

<div id="map_container" style="width: 100%; min-height: 500px; margin-left: 5px; margin-right: 5px;">
    <div style="position:absolute; z-index: 1001; margin-left: 50px; background-color: lightyellow; box-shadow: 0px 0px 15px rgba(0,0,0,0.3); padding: 3px;">
        <?= formatReady($map['alert_window_text']) ?>
    </div>
    <div id="map" style="width: 100%; min-height: 500px;">

    </div>
</div>

<input type="hidden" id="current_map" value="true">
<input type="hidden" id="map_id" value="<?= $map->getId() ?>">
<input type="hidden" id="last_update" value="<?= time() ?>">
<input type="hidden" id="seminar_id" value="<?= Context::get()->id ?>">
<input type="hidden" id="original_lat" value="<?= $map['latitude'] ?>">
<input type="hidden" id="original_lon" value="<?= $map['longitude'] ?>">
<input type="hidden" id="original_zoom" value="<?= $map['zoom'] ?>">
<input type="hidden" id="tile_server" value="<?= htmlReady(get_config("KATIP_TILE_SERVER")) ?>">

<script>
STUDIP.WYSIWYG = true;
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
                '<?= $poi['size'] ? (int) $poi['size'] : 40 ?>',
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

    <form class="default">
        <label>
            <?= _("Name") ?>
            <input type="text" value="" name="title" id="title">
        </label>
        <label>
            <?= _("Schadenskonto") ?>
            <select name="schadenskonto_id" id="schadenskonto_id" required onChange="this.value==='neu' ? jQuery('#schadenskonto_title').fadeIn() : jQuery('#schadenskonto_title').fadeOut(); ">
                <option value=""><?= _("---") ?></option>
                <? foreach ($schadenskonten as $schadenskonto) : ?>
                    <option value="<?= htmlReady($schadenskonto->getId()) ?>"><?= htmlReady($schadenskonto['title']) ?></option>
                <? endforeach ?>
                <option value="neu"><?= _("neues Konto") ?></option>
            </select>
        </label>
        <input type="text" name="schadenskonto_title" id="schadenskonto_title" aria-label="<?= _("Name des neuen Kontos") ?>" style="display: none;" placeholder='<?= _("Name des neuen Kontos") ?>'>
        <?= \Studip\LinkButton::create(_("speichern"), '#', array('onClick' => "STUDIP.Lagekarte.save_new_layer();")) ?>
    </form>
</div>

<? 
<<<<<<< HEAD

$sidebar = Sidebar::get();
$sidebar->setImage($plugin->getPluginURL()."/assets/sidebar.png");
=======
Sidebar::Get()->setImage($plugin->getPluginURL()."/assets/sidebar.png");
>>>>>>> ddb0468afde9d47a0983fcbe97d3e6d2473ef812

if ($GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id)) {
    $actions = new ActionsWidget();
<<<<<<< HEAD
    $actions->addLink(_("Bildsausschnitt speichern"), "#", null, array('onclick' => "STUDIP.Lagekarte.save_map_viewport(); return false;"));
    $actions->addLink(_("Snapshot der Karte anlegen"), "#", null, array('onclick' => "STUDIP.Lagekarte.create_snapshot(); return false;"));
    $actions->addLink(_("Vollbild aktivieren"), '#', null, array('onClick' => "STUDIP.Lagekarte.activateFullscreen(); return false;"));
    $actions->addLink(_("Meldung bearbeiten"), PluginEngine::getURL($plugin, array(), "map/edit_alert_window"), null, array('data-dialog' => "button"));
    $sidebar->addWidget($actions);
=======
    $actions->addLink(
        _("Bildsausschnitt speichern."),
        "#1",
        Icon::create("visibility-checked", "clickable"),
        array('onClick' => "STUDIP.Lagekarte.save_map_viewport(); return false;")
    );
    $actions->addLink(
        _("Snapshot der Karte anlegen."),
        "#2",
        Icon::create("archive2", "clickable"),
        array('onClick' => "STUDIP.Lagekarte.create_snapshot(); return false;")
    );
    $actions->addLink(_("Vollbild aktivieren"),
        '#3',
        Icon::create("tan3", "clickable"),
        array('onClick' => "STUDIP.Lagekarte.activateFullscreen(); return false;")
    );
    $actions->addLink(_("Meldung bearbeiten"),
        PluginEngine::getURL($plugin, array(), "map/edit_alert_window"),
        Icon::create("edit", "clickable"),
        array('data-dialog' => "button")
    );
    Sidebar::Get()->addWidget($actions);
>>>>>>> ddb0468afde9d47a0983fcbe97d3e6d2473ef812
}