<form>
<input type="hidden" name="poi_id" value="<?= $poi->getId() ?>">
<h3 style="margin: 0px;"><?= htmlReady($poi['title']) ?></h3>
<hr>
<table>
    <tr>
        <td><strong><?= _("Schadenskonto") ?></strong></td>
        <td>
            <a href="<?= PluginEngine::getLink($plugin, array(), "schadenskonten/konto/".$poi['schadenskonto_id']) ?>">
                <?= Assets::img("icons/16/blue/link-intern", array('class' => "text-bottom")) ?>
                <?= htmlReady(Schadenskonto::find($poi['schadenskonto_id'])->title) ?>
            </a>
        </td>
    </tr>
    <? if ($poi['shape'] === "polyline") : ?>
    <tr>
        <td><strong><?= _("Länge") ?></strong></td>
        <td><?= $poi->getLength() ?> km</td>
    </tr>
    <? endif ?>
    <? if ($poi['shape'] === "marker") : ?>
    <tr>
        <td><strong><?= _("Koordinaten (Latitude, Longitude)") ?></strong></td>
        <td><?= $poi['coordinates'][1].", ".$poi['coordinates'][0] ?></td>
    </tr>
    <? endif ?>
    <? if ($poi['shape'] !== "marker") : ?>
    <tr>
        <td><strong><label for="poi_<?= $poi->getId() ?>_color"><?= _("Farbe") ?></label></strong></td>
        <td>
            <select name="poi_color" id="poi_<?= $poi->getId() ?>_color">
                <option value="blue"<?= $poi['color'] === "blue" ? " selected" : "" ?>><?= _("blau") ?></option>
                <option value="darkgreen"<?= $poi['color'] === "darkgreen" ? " selected" : "" ?>><?= _("grün") ?></option>
                <option value="red"<?= $poi['color'] === "red" ? " selected" : "" ?>><?= _("rot") ?></option>
                <option value="yellow"<?= $poi['color'] === "yellow" ? " selected" : "" ?>><?= _("gelb") ?></option>
                <option value="black"<?= $poi['color'] === "black" ? " selected" : "" ?>><?= _("schwarz") ?></option>
                <option value="white"<?= $poi['color'] === "white" ? " selected" : "" ?>><?= _("weiß") ?></option>
            </select>
        </td>
    </tr>
    <? endif ?>
</table>
</form>