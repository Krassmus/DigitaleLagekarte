<?php

/*
 *  Copyright (c) 2013  Rasmus Fuhse <fuhse@data-quest.de>
 * 
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

function isAssoc($arr) {
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function display_json_representation($arr) {
    $output = "";
    if (isAssoc($arr)) {
        $output .= "<table><tbody>";
        foreach ($arr as $key => $value) {
            $output .= "<tr data-key=\"".htmlReady($key)."\">";
            $output .= '<td class="key">'.htmlReady($key).'</td>';
            if (is_array($value)) {
                $output .= '<td>'.display_json_representation($value).'</td>';
            } else {
                $output .= '<td class="value">'.htmlReady($value).'</td>';
                $output .= '<td class="match">'.Assets::img("icons/16/blue/staple", array('class' => "text-bottom")).'</td>';
            }
            $output .= "</tr>";
        }
        $output .= "</tbody></table>";
    } else {
        $output .= "<ol>";
        foreach ($arr as $value) {
            $output .= "<li>";
            if (is_array($value)) {
                $output .= display_json_representation($value);
            } else {
                $output .= $value;
            }
            $output .= "</li>";
        }
        $output .= "</ol>";
    }
    return $output;
}

?>
<h1><?= htmlReady($url['name']) ?></h1>
<div class="json_object_list">
<?= display_json_representation($url['last_object']) ?>
</div>


<?
$infobox = array(
    array("kategorie" => _("Information"),
          "eintrag"   =>
        array(
            array(
                "icon" => "icons/16/black/tools",
                "text" => _("Verknüpfen Sie einzelne Datensätze mit Attributen von Markern auf der Lagekarte. Sie werden dann regelmäßig automatisch aktualisiert.")
            )
        )
    )
);
$infobox = array(
    'picture' => $assets_url."pegelstaende.jpg",
    'content' => $infobox
);