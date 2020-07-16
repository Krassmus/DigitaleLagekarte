<table class="default" id="overview_schadenskonten">
    <caption><?= _("Schadenskonten") ?></caption>
    <thead>
        <tr>
            <th><?= _("Name des Schadenskontos") ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <? foreach ($schadenskonten as $schadenskonto) : ?>
        <tr id="<?= htmlReady($schadenskonto->getId()) ?>">
            <td><?= htmlReady($schadenskonto['title']) ?></td>
            <td><a href="<?= PluginEngine::getLink($plugin, array(), "schadenskonten/konto/".$schadenskonto->getId()) ?>"><?= Assets::img("icons/16/blue/link-intern", array('class' => "text-bottom")) ?></a></td>
        </tr>
        <? endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">
                <? if ($GLOBALS['perm']->have_studip_perm('tutor', Context::get()->id)) : ?>
                <?= Assets::img("icons/16/blue/add") ?>
                <? endif ?>
            </td>
        </tr>
    </tfoot>
</table>
