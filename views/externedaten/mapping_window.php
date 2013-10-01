<div id="mapping_window">
    <form onSubmit="STUDIP.Lagekarte.map_external_data() ;return false;">
    <input type="hidden" id="mapping_path" value="<?= htmlReady(implode(" ", Request::getArray("path"))) ?>">
    <table>
        <tbody>
            <tr>
                <td><label for="poi_id"><?= _("Zeichen") ?></label></td>
                <td>
                    <select id="poi_id" required>
                        <option value="">  --  </option>
                        <? foreach ($pois as $poi) : ?>
                        <option value="<?= $poi['first_predecessor'] ?>"><?= htmlReady($poi['title']) ?></option>
                        <? endforeach ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="poi_attribute"><?= _("Eigenschaft") ?></label></td>
                <td>
                    <select id="poi_attribute" required>
                        <option value="">  --  </option>
                        <option value="title"><?= _("Titel") ?></option>
                        <option value="longitude"><?= _("Longitude") ?></option>
                        <option value="latitude"><?= _("Latitude") ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?= _("Aktueller Wert") ?></td>
                <td><span class="value"><?= htmlReady($value) ?></span></td>
            </tr>
            <tr>
                <td></td>
                <td><?= \Studip\Button::create(_("verknüpfen")) ?></td>
            </tr>
        </tbody>
    </table>
    </form>
</div>