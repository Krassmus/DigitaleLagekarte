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
        $output .= "<ul>";
        foreach ($arr as $key => $value) {
            $output .= "<li data-key=\"".htmlReady($key)."\">";
            if (is_array($value)) {
                $output .= display_json_representation($value);
            } else {
                $output .= '<span class="key">'.htmlReady($key).'<span>: <span class="value">'.htmlReady($value).'</span>';
            }
            $output .= "</li>";
        }
        $output .= "</ul>";
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