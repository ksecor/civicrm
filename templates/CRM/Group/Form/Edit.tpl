{* this template is used for adding/editing group (name and description only)  *}
<div id="help">
    {if $action eq 2}
        You can edit the Name and Description for this group here. Click 'Show Members' to view, add
        or remove contacts in this group.
    {else}
        Enter a unique name and a description for your new group here. Then click 'Continue' to
        find contacts to add to your new group.
    {/if}
</div>
<form {$form.attributes}>
<div class="form-item">
<fieldset><legend>Group Name and Description</legend>
    <dl>
        <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
        <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
        <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
    </fieldset>
</div>
</form>
