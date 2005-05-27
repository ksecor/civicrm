{* add/update/view custom data group *}

<div class="form-item">
    <fieldset><legend>{ts}Activity{/ts}</legend>
    <dl>
    <dt>{$form.activity_type.label}</dt><dd>{$form.activity_type.html}</dd>
    <dt>{$form.callback.label}</dt><dd>{$form.callback.html}</dd>
    <dt>{$form.module.label}</dt><dd>{$form.module.html}</dd>
    <dt>{$form.activity_id.label}</dt><dd>{$form.activity_id.html}</dd>
    <dt>{$form.activity_summary.label}</dt><dd>{$form.activity_summary.html}</dd>
    <dt>{$form.activity_date.label}</dt><dd>{$form.activity_date.html}</dd>
    {if $action ne 4}
        <div id="crm-submit-buttons">
        <dt></dt><dd>{$form.buttons.html}</dd>
        </div>
    {else}
        <div id="crm-done-button">
        <dt></dt><dd>{$form.done.html}</dd>
        </div>
    {/if} {* $action ne view *}
    </dl>
    </fieldset>
</div>
