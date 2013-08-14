<table class="default zebra zebra-hover" id="overview_schadenskonten">
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
                <? if ($GLOBALS['perm']->have_studip_perm('tutor', $_SESSION['SessionSeminar'])) : ?>
                <?= Assets::img("icons/16/blue/add") ?>
                <? endif ?>
            </td>
        </tr>
    </tfoot>
</table>

<? 
$infobox = array(
    array("kategorie" => _("Information"),
          "eintrag"   =>
        array(
            array(
                "icon" => "icons/16/black/info-circle",
                "text" => _("Wählen Sie ein Schadenskonto aus.")
            )
        )
    )
);
$infobox = array(
    'picture' => $assets_url."Lagekarte-4.jpg",
    'content' => $infobox
);