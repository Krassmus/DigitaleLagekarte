<table class="default">
    <thead>
        <tr>
            <th><?= _("Name des Schadenskontos") ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>EAL1</td>
            <td><?= Assets::img("icons/16/blue/link-intern", array('class' => "text-bottom")) ?></td>
        </tr>
        <tr>
            <td>EAL2</td>
            <td><?= Assets::img("icons/16/blue/link-intern", array('class' => "text-bottom")) ?></td>
        </tr>
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