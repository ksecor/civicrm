<div class="messages status">
<p>{ts count=$totalSelectedContributions plural='%count contribution records selected for update.'}One contribution record selected for export.{/ts}</p>
</div>

<div class="form-item">
<fieldset>
    <legend>{ts}Update Contribution Status{/ts}</legend>
    <dl>
        <dt>{$form.contribution_status_id.label}</dt><dd>{$form.contribution_status_id.html}</dd>
        <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
