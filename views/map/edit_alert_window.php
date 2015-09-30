<form action="?" method="post">
    <textarea name="alert_window_text" id="alert_window_text" style="width: 100%; height: 500px;"><?= htmlReady($map['alert_window_text']) ?></textarea>
    <div align="center" data-dialog-button>
        <div class="button-group">
            <?= \Studip\Button::create(_("Speichern"), 'save') ?>
        </div>
    </div>
</form>


<script>
    jQuery(function () {
        if (jQuery("#alert_window_text").length > 0) {
            STUDIP.addWysiwyg(jQuery("#alert_window_text"));
        }
    });
</script>