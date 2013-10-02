<?php

/*
 *  Copyright (c) 2013  Rasmus Fuhse <fuhse@data-quest.de>
 * 
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */
?>
<input type="hidden" id="Seminar_id" value="<?= $_SESSION['SessionSeminar'] ?>">
<table class="default">
    <caption><?= _("Externe Daten") ?></caption>
    <thead>
        <tr>
            <th><?= _("Name") ?></th>
            <th><?= _("Letztes Update") ?></th>
            <th><?= _("Aktiv") ?></th>
            <th><?= _("Verknüpft") ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <? if (count($urls)) : ?>
        <? foreach ($urls as $url) : ?>
        <tr data-url="<?= htmlReady($url['url']) ?>">
            <td><?= htmlReady($url['name']) ?></td>
            <td><?= date("G:i j.n.Y", $url['last_update']) ?></td>
            <td><?= $url['active']
                    ? Assets::img("icons/16/black/checkbox-checked", array("class" => "text-bottom"))
                    : Assets::img("icons/16/black/checkbox-unchecked", array("class" => "text-bottom")) ?></td>
            <td><?= count($url['mapping']) ? Assets::img("icons/16/red/star") : "" ?></td>
            <td><a href="<?= PluginEngine::getLink($plugin, array('url' => $url['url']), "externedaten/details") ?>"><?= Assets::img("icons/16/blue/edit", array("class" => "text-bottom")) ?></a></td>
        </tr>
        <? endforeach ?>
        <? else : ?>
        <tr>
            <td colspan="4" style="text-align: center;"><?= _("Noch keine externe Daten initialisiert") ?></td>
        </tr>
        <? endif ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4"><a href="" onClick="STUDIP.Lagekarte.new_external_data_url(); return false;"><?= Assets::img("icons/16/blue/add") ?></td>
        </tr>
    </tfoot>
</table>

<div id="new_external_data_url_window_title" style="display: none;"><?= _("Neue externe URL") ?></div>
<div id="new_external_data_url_window" style="display: none;">
    <form>
        <table>
            <tr>
                <td><label><?= _("Name") ?></label></td>
                <td><input type="text" name="name"></td>
            </tr>
            <tr>
                <td><label><?= _("URL") ?></label></td>
                <td><input type="text" name="url"></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <?= \Studip\Button::create(_("erstellen"), '', array('onClick' => "STUDIP.Lagekarte.create_external_data_url(); return false;")) ?>
                </td>
            </tr>
        </table>
    </form>
</div>

<?
$infobox = array(
    array("kategorie" => _("Information"),
          "eintrag"   =>
        array(
            array(
                "icon" => "icons/16/black/activity",
                "text" => _("Rufen Sie Daten aus dem Internet per URL ab (sie müssen ein JSON-Format haben) und verknüpfen Sie diese mit Markern in der Lagekarte. Die Lagekarte aktualisiert sich dann alle fünf Minuten von alleine.")
            )
        )
    )
);
$infobox = array(
    'picture' => $assets_url."pegelstaende.jpg",
    'content' => $infobox
);