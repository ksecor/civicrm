{* this template is used for adding/editing a group  *}
<form {$form.attributes}>
<div class="form-item">
<fieldset><legend>Delete Group</legend>
Are your sure you want to delete the group {$name}. This group currently has {$count} members
in it.
        <div class="horizontal-position">
        <span class="two-col1">
            <span class="fields">{$form.buttons.html}</span>
        </span>
        <div class="spacer"></div>
        </div>
    </fieldset>
</div>
</form>
