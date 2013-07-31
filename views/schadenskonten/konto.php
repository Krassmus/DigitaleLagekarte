<h1><?= htmlReady($schadenskonto['title']) ?></h1>

<div class="accordion">
    <h3><?= _("Beschreibung") ?></h3>
    <div>
        <table>
            <tr>
                <td><?= _("Stärkemeldung") ?></td>
                <td></td>
            </tr>
            <tr>
                <td><?= _("Lage vor Ort") ?></td>
                <td>
                    <?= formatReady("Keine Vorkommnisse") ?>
                </td>
            </tr>
        </table>
    </div>
    <h3><?= _("Elemente") ?></h3>
    <div>
        <ul class="pois">
        <? foreach ($schadenskonto->getPOIs() as $poi) : ?>
            <li><?= htmlReady($poi['title']) ?></li>
        <? endforeach ?>
        </ul>
    </div>
</div>

<script>
jQuery(function () {
    jQuery(".accordion").accordion();
});
</script>
