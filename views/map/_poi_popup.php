<h3 style="margin: 0px;"><?= htmlReady($poi['title']) ?></h3>
<hr>
<table>
    <tr>
        <td><strong><?= _("Schadenskonto") ?></strong></td>
        <td><?= htmlReady(Schadenskonto::find($poi['schadenskonto_id'])->title) ?></td>
    </tr>
    <tr>
        <td><strong><?= _("Bezeichnung") ?></strong></td>
        <td><?= htmlReady($poi['title']) ?></td>
    </tr>
</table>
