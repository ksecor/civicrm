{* this template is used for adding/editing location type  *}
<form {$form.attributes}>
<div class="form-item">
<fieldset><legend>{if $op eq 'add'}New{else}Edit{/if} Location Type(s)</legend>
	<div>{$form.name.label}{$form.name.html}</div>
	<div>{$form.description.label}{$form.description.html}</div>
        <div class="horizontal-position">
        <span class="two-col1">
            <span class="fields">{$form.buttons.html}</span>
        </span>
        <div class="spacer"></div>
        </div>

    </fieldset>
</div>
</form>
