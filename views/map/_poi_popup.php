<form>
<input type="hidden" name="poi_id" value="<?= $poi->getId() ?>">
<table class="default poi_popup nohover" style="margin: 0px;">
    <caption><?= htmlReady($poi['title']) ?></caption>
    <tbody>
        <tr>
            <td><strong><?= _("Schadenskonto") ?></strong></td>
            <td>
                <a href="<?= PluginEngine::getLink($plugin, array(), "schadenskonten/konto/".$poi['schadenskonto_id']) ?>">
                    <?= Icon::create("link-intern", "clickable")->asImg(16, array('class' => "text-bottom")) ?>
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
        <? if ($poi['shape'] !== "marker" && $GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id)) : ?>
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
    </tbody>
    <? if ($GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id) && $poi['shape'] === "marker") : ?>
    <tbody>
        <tr>
            <td><strong><label for="poi_<?= $poi->getId() ?>_image"><?= _("Zeichen") ?></label></strong></td>
            <td>
                <select name="poi_image" id="poi_<?= $poi->getId() ?>_image">
                    <option value=""> --- </option>
                    <? foreach ($images as $path => $image) : ?>
                    <? if (is_array($image)) : ?>
                    <? foreach ($image as $p => $i) : ?>
                    <option value="<?= htmlReady($p) ?>"<?= $poi['image'] === $p ? " selected" : "" ?>><?= htmlReady($i) ?></option>
                    <? endforeach ?>
                    <? else : ?>
                    <option value="<?= htmlReady($path) ?>"<?= $poi['image'] === $path ? " selected" : "" ?>><?= htmlReady($image) ?></option>
                    <? endif ?>
                    <? endforeach ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <strong><?= _("Größe") ?></strong>
                <div id="poi_<?= $poi->getId() ?>_slider" class="poi_slider"></div>
                <div></div>
            </td>
        </tr>
    </tbody>
    <? endif ?>
    <tbody class="datafields">
    <? foreach ($poi->datafields as $datafield) : ?>
        <tr data-datafield_id="<?= $datafield->getId() ?>">
            <td>
                <? if ($GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id)) : ?>
                    <input type="text" class="datafield_name" name="datafield[<?= $datafield->getId() ?>][name]" value="<?= htmlReady($datafield['name']) ?>">
                <? else : ?>
                    <strong><?= htmlReady($datafield['name']) ?></strong>
                <? endif ?>
            </td>
            <td>
                <? if ($GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id)) : ?>
                    <input type="text" class="datafield_content" name="datafield[<?= $datafield->getId() ?>][content]" value="<?= htmlReady($datafield['content']) ?>">
                    <a href="#" class="delete">
                        <?= Icon::create("trash", "clickable")->asImg(20, array('class' => "text-bottom")) ?>
                    </a>
                <? else : ?>
                    <?= htmlReady($datafield['content']) ?>
                <? endif ?>
            </td>
        </tr>
    <? endforeach ?>
    </tbody>
    <? if ($GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id) && $poi['shape'] === "marker") : ?>
        <tfoot>
        <tr style="display: none" class="poi_datafield_template">
            <td>
                <input type="text" name="datafield[new][name]" class="datafield_name" value="" placeholder="<?= _("Attribut") ?>">
            </td>
            <td>
                <input type="text" name="datafield[new][content]" class="datafield_content" value="<?= htmlReady($datafield['content']) ?>">
                <a href="#" class="delete">
                    <?= Icon::create("trash", "clickable")->asImg(20, array('class' => "text-bottom")) ?>
                </a>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <a href="#" onClick="STUDIP.Lagekarte.add_poi_datafield.call(this); return false;" title="<?= _("Weiteres Attribut hinzufügen") ?>">
                    <?= Icon::create("add", "clickable") ?>
                </a>
            </td>
        </tr>
        </tfoot>
    <? endif ?>
</table>
</form>
