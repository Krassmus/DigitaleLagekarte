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
                $output .= '<td class="structure">'.display_json_representation($value, $url, $index." ".$key).'</td>';
            } else {
                $output .= '<td class="value">'.htmlReady($value).'</td>';
                $output .= '<td class="match '.(isset($url['mapping'][trim($index." ".$key)]) ? " matched" : "").'">';
                $output .= '<a title="'._("Verknüpfung verwalten").'">';
                $output .= Assets::img("icons/20/red/staple", array('class' => "text-bottom active"));
                $output .= Assets::img("icons/20/grey/staple", array('class' => "text-bottom inactive"));
                $output .= '</a>';
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
<input type="hidden" id="Seminar_id" value="<?= htmlReady(Context::get()->id) ?>">
<input type="hidden" id="url" value="<?= htmlReady($url['url']) ?>">

<div class="accordion" style="width: 100%;">
    <h2 style="width: calc(100% - 30px);"><?= _("Eigenschaften") ?></h2>
    <div>
        <form action="<?= URLHelper::getLink("?", array('url' => $url['url'])) ?>" method="post">
        <table width="95%" class="default">
            <tbody>
                <tr>
                    <td>
                        <label for="name"><?= _("Name") ?></label>
                    </td>
                    <td>
                        <input type="text" name="name" id="name" value="<?= htmlReady($url['name']) ?>" style="width: 100%;">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="new_url"><?= _("URL") ?></label>
                    </td>
                    <td>
                        <input type="text" name="new_url" id="new_url" value="<?= htmlReady($url['url']) ?>" style="width: 100%;">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="interval"><?= _("Aktualisieren alle x Minuten") ?></label>
                    </td>
                    <td>
                        <input type="number" name="interval" id="interval" value="<?= htmlReady($url['interval']) ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="auth_user"><?= _("HTTP-Auth (otional)") ?></label>
                    </td>
                    <td>
                        <label>
                            <?= _("Nutzername") ?>
                            <br>
                            <input type="text" name="auth_user" id="auth_user" value="<?= htmlReady($url['auth_user']) ?>">
                        </label>
                        <br>
                        <br>
                        <label>
                            <?= _("Passwort") ?>
                            <br>
                            <input type="password" name="auth_pw" id="auth_pw" value="<?= htmlReady($url['auth_pw']) ?>">
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>
            <div style="text-align: center;"><?= \Studip\Button::create(_("speichern"))?></div>
        </form>
    </div>
    <h2 style="width: calc(100% - 30px);"><?= _("Datenmapping") ?></h2>
    <div class="json_object_list">
        <?= display_json_representation($url['last_object'], $url, "") ?>
    </div>
    <h2 style="width: calc(100% - 30px);"><?= _("Löschen") ?></h2>
    <div>
        <form action="<?= URLHelper::getLink("?", array('url' => $url['url'])) ?>" method="post">
            <div style="text-align: center;">
                <?= \Studip\Button::create(_("Wirklich löschen?"), "delete")?>
            </div>
        </form>
    </div>
</div>
<script>
    jQuery(function () { jQuery(".accordion").accordion({
        'active': 1,
        'heightStyle': "content",
        'collapsible': true
    }); });
</script>

<div style="display: none" id="mapping_window_title"><?= _("Verknüpfung zu Zeichen") ?></div>

<?
$sidebar = Sidebar::get();
$sidebar->setImage($plugin->getPluginURL()."/assets/sidebar.png");
