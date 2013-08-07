<style>
    .strength {
        width: 30px;
    }
</style>
<script>
jQuery(function () {
    jQuery(".strength").bind("keydown", function (event) {
        if (event.keyCode === 32) {
            jQuery(this).next().focus();
            event.stopPropagation();
            event.preventDefault();
            return false;
        }
        jQuery("#gesamt").val(
            parseInt(jQuery("#fuehrer").val() || 0, 10)
            + parseInt(jQuery("#unterfuehrer").val() || 0, 10)
            + parseInt(jQuery("#helfer").val() || 0, 10)
        );
    });
    jQuery(".strength").bind("blur", function () {
        if (!jQuery(this).val()) {
            jQuery(this).val(0);
        }
    });
});    
</script>

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
                    <input type="text" class="strength" id="fuehrer" value="0">
                    <input type="text" class="strength" id="unterfuehrer" value="0">
                    <input type="text" class="strength" id="helfer" value="0">
                    <input type="text" class="strength" id="gesamt" value="0" disabled style="font-weight: bold;">
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
