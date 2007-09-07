{* this template is used for adding/editing a mailing component  *}
<div class="form-item">
<fieldset>
{if $single}
<legend>{ts 1=$groupName}Subscribe to Group %1{/ts}</legend>
{else}
<legend>{ts}Mailing List Subscription{/ts}</legend>
{/if}
  <dl>
    <dt>{$form.email.label}</dt><dd>{$form.email.html}</dd>
{if ! $single}
    <dt>{$form.group_id.label}</dt><dd>{$form.group_id.html}</dd>
{/if}
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
