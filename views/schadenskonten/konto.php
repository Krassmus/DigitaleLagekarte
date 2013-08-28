<input type="hidden" id="seminar_id" value="<?= $_SESSION['SessionSeminar'] ?>">


<h1><?= htmlReady($schadenskonto['title']) ?></h1>

<h2><?= _("Beschreibung") ?></h2>
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
<h2><?= _("Elemente") ?></h2>
<div>
    <table class="default">
        <tbody>
            <tr>
                <th><?= _("Schäden") ?></th>
                <th><?= _("Gefahren") ?></th>
                <th><?= _("Memo") ?></th>
                <th><?= _("Maßnahmen") ?></th>
                <th><?= _("Kräfte") ?></th>
            </tr>
            <tr>
                <td>
                    <ul class="pois"></ul>
                </td>
                <td><ul class="pois"></ul></td>
                <td></td>
                <td></td>
                <td>
                    <ul class="pois">
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>
    <h3><?= _("Allgemeine Kräfte") ?></h3>
    <ul class="pois">
        <? foreach ($schadenskonto->getPOIs() as $poi) {
            echo $this->render_partial("schadenskonten/_poi_batch.php", compact('poi', 'assets_url'));
        } ?>
    </ul>
</div>

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