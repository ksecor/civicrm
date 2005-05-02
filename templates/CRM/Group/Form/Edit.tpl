{* this template is used for adding/editing a group  *}
<form {$form.attributes}>
<div class="form-item">
<fieldset><legend>{if $action eq 2}Edit{else}New{/if} Group</legend>
        <div>{$form.title.label}{$form.title.html}</div>
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
