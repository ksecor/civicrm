{* this template is used for adding/editing IM Provider  *}
<form {$form.attributes}>
<div class="form-item">
<fieldset><legend>{if $op eq 'add'}New{else}Edit{/if} IM Provider</legend>
	<div>{$form.name.label}{$form.name.html}</div>
        <div class="horizontal-position">
        <span class="two-col1">
            <span class="fields">{$form.buttons.html}</span>
        </span>
        <div class="spacer"></div>
        </div>

    </fieldset>
</div>
</form>
