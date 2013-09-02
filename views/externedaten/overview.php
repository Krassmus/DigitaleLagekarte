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
<table class="default">
    <caption><?= _("Externe Daten") ?></caption>
    <thead>
        <tr>
            <th><?= _("Name") ?></th>
            <th><?= _("Letztes Update") ?></th>
            <th><?= _("Aktiv") ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <? if (count($urls)) : ?>
        <? foreach ($urls as $url) : ?>
        <tr>
            <td><?= htmlReady($url['name']) ?></td>
            <td><?= date("j.m.Y", $url['last_update']) ?></td>
            <td><?= $url['active'] && count($url['mapping'])
                    ? Assets::img("icons/16/black/checkbox-checked", array("class" => "text-bottom"))
                    : Assets::img("icons/16/black/checkbox-checked", array("class" => "text-bottom")) ?></td>
            <td><?= Assets::img("icons/16/blue/edit", array("class" => "text-bottom")) ?></td>
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
            <td colspan="4"><?= Assets::img("icons/16/blue/add") ?></td>
        </tr>
    </tfoot>
</table>

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