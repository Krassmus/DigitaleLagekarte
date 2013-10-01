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


function display_json_representation($arr, $url, $index) {
    $output = "";
    if (isAssoc($arr)) {
        $output .= "<table><tbody>";
        foreach ($arr as $key => $value) {
            $output .= "<tr data-key=\"".htmlReady($key)."\">";
            $output .= '<td class="key">'.htmlReady($key).'</td>';
            if (is_array($value)) {
                $output .= '<td>'.display_json_representation($value, $url, $index." ".$key).'</td>';
            } else {
                $output .= '<td class="value">'.htmlReady($value).'</td>';
                $output .= '<td class="match">';
                $output .= '<a title="'._("Verknüpfung verwalten").'">'.Assets::img("icons/16/blue/staple", array('class' => "text-bottom")).'</a>';
                if (isset($url['mapping'][trim($index." ".$key)])) {
                    $output .= Assets::img("icons/16/green/star", array('class' => "text-bottom", 'title' => _("verknüpft")));
                }
                $output .= '</td>';
            }
            $output .= "</tr>";
        }
        $output .= "</tbody></table>";
    } else {
        $output .= "<ol>";
        foreach ($arr as $key => $value) {
            $output .= "<li>";
            if (is_array($value)) {
                $output .= display_json_representation($value, $url, $index." ".$key);
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
<input type="hidden" id="Seminar_id" value="<?= htmlReady($_SESSION['SessionSeminar']) ?>">
<input type="hidden" id="url" value="<?= htmlReady($url['url']) ?>">
<div class="json_object_list">
<?= display_json_representation($url['last_object'], $url, "") ?>
</div>


<div style="display: none" id="mapping_window_title"><?= _("Verknüpfung zu Zeichen") ?></div>

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