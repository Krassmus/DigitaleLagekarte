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
    <tr>
        <td><strong><?= _("Bezeichnung") ?></strong></td>
        <td><?= htmlReady($poi['title']) ?></td>
    </tr>
</table>
