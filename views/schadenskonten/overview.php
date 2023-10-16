<table class="default" id="overview_schadenskonten">
    <caption><?= _("Schadenskonten") ?></caption>
    <thead>
        <tr>
            <th><?= _("Name des Kontos") ?></th>
            <th class="actions"><?= _("Aktion") ?></th>
        </tr>
    </thead>
    <tbody>
        <? foreach ($schadenskonten as $schadenskonto) : ?>
        <tr id="<?= htmlReady($schadenskonto->getId()) ?>">
            <td><?= htmlReady($schadenskonto['title']) ?></td>
            <td class="actions">
                <a href="<?= PluginEngine::getLink($plugin, array(), "schadenskonten/konto/".$schadenskonto->getId()) ?>">
                    <?= Icon::create("link-intern", "clickable")->asImg(20, array('class' => "text-bottom")) ?>
                </a>
            </td>
        </tr>
        <? endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">
                <? if ($GLOBALS['perm']->have_studip_perm('tutor', Context::get()->id)) : ?>
                <?= Icon::create("add", "clickable")->asImg(20) ?>
                <? endif ?>
            </td>
        </tr>
    </tfoot>
</table>
