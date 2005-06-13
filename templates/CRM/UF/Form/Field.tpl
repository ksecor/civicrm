<fieldset><legend>{ts}User Framework Field{/ts}</legend>

    <div class="form-item">
        <dl>
        <dt>{$form.field_name.label}</dt><dd>&nbsp;{$form.field_name.html}</dd>
        <dt>{$form.visibility.label}</dt><dd>&nbsp;{$form.visibility.html}</dd>
        <dt>{$form.listings_title.label}</dt><dd>&nbsp;{$form.listings_title.html}</dd>
        <dt>{$form.is_required.label}</dt><dd>&nbsp;{$form.is_required.html}</dd>
        <dt>{$form.is_active.label}</dt><dd>&nbsp;{$form.is_active.html}</dd>
        <dt>{$form.is_view.label}</dt><dd>&nbsp;{$form.is_view.html}</dd>
        <dt>{$form.is_registration.label}</dt><dd>&nbsp;{$form.is_registration.html}</dd>
        <dt>{$form.is_match.label}</dt><dd>&nbsp;{$form.is_match.html}</dd>
        </dl>
    </div>
    
    <div id="crm-submit-buttons" class="form-item">
    <dl>
    {if $action ne 4}
        <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>
    {else}
        <dt>&nbsp;</dt><dd>{$form.done.html}</dd>
    {/if} {* $action ne view *}
    <dl>
    </div>

</fieldset>
