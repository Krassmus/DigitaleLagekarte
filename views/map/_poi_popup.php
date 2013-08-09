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
</table>
