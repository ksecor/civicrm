<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

<div class="form-item">
<fieldset>
    <legend>{if $group.id}Confirm{else}Choose{/if} Group</legend>
    <dl>
        <dt>{$form.group_id.label}</dt><dd>{$form.group_id.html}</dd>
        <dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
        <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>

</form>
