<input type="hidden" id="seminar_id" value="<?= $_SESSION['SessionSeminar'] ?>">


<h1><?= htmlReady($schadenskonto['title']) ?></h1>

<div class="accordion">
    <h3><?= _("Beschreibung") ?></h3>
    <div>
        <table>
            <tr>
                <td><label for="kurzbeschreibung"><?= _("Kurzbeschreibung") ?></label></td>
                <td>
                    <textarea rows="2" cols="40" id="kurzbeschreibung" name="kurzbeschreibung"></textarea>
                </td>
            </tr>
            <tr>
                <td><?= _("Stärkemeldung") ?></td>
                <td>
                    <input type="text" class="troopstrength" id="fuehrer" value="0">
                    <input type="text" class="troopstrength" id="unterfuehrer" value="0">
                    <input type="text" class="troopstrength" id="helfer" value="0">
                    <input type="text" class="troopstrength" id="gesamt" value="0" disabled style="font-weight: bold;">
                </td>
            </tr>
            <tr>
                <td><label for="lage"><?= _("Ausführliche Lage") ?></label></td>
                <td>
                    <textarea name="lage" id="lage" style="width: 100%; height: 100px;"></textarea>
                </td>
            </tr>
        </table>
    </div>
    <h3><?= _("Elemente") ?></h3>
    <div>
        <ul class="pois">
        <? foreach ($schadenskonto->getPOIs() as $poi) : ?>
            <li><?= htmlReady($poi['title']) ?> (<?= $poi['shape'] ?>)</li>
        <? endforeach ?>
        </ul>
    </div>
</div>

<script>
jQuery(function () {
    jQuery(".accordion").accordion();
});
</script>
<? 
$select = '<select name="schadenkonto_id" id="select_schadenskonto" size="10">';
foreach ($schadenskonten as $sch) {
    $select .= '<option value="'.htmlReady($sch->getId()).'"'.($schadenskonto->getId() === $sch->getId() ? " selected" : "").'>'.htmlReady($sch['title']).'</option>';
}
$select .= '</select>';

$infobox = array(
    array("kategorie" => _("Schadenskonten"),
          "eintrag"   =>
        array(
            array(
                "icon" => "icons/16/black/link-intern",
                "text" => $select
            )
        )
    )
);
$infobox = array(
    'picture' => $assets_url."Lagekarte-4.jpg",
    'content' => $infobox
);