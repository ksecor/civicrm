{* this template is used for adding/editing ACL  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New ACL{/ts}{elseif $action eq 2}{ts}Edit ACL{/ts}{else}{ts}Delete ACL{/ts}{/if}</legend>

{if $action eq 8}
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd>    
        {ts}WARNING: Deleting this option will remove this permission from all ACL Roles.{/ts} {ts}Do you want to continue?{/ts}
      </dd>
    </dl>
  </div>
{else}
  <dl>
    <dt>{$form.operation.label}</dt><dd>{$form.operation.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Which action to permit.{/ts}</dd>
    <dt>Permissioning</dt><dd>Please select the object you want permissioning applied on</dd>
    <dt>{$form.group_id.label}</dt><dd>{$form.group_id.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Permitted on this group of contacts.{/ts}</dd>
    <dt>{$form.custom_group_id.label}</dt><dd>{$form.custom_group_id.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Permitted on these custom groups.{/ts}</dd>
    <dt>{$form.uf_group_id.label}</dt><dd>{$form.uf_group_id.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Permitted on these profiles.{/ts}</dd>
    <dt>{$form.entity_id.label}</dt><dd>{$form.entity_id.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Permission granted to this role.{/ts}</dd>
    <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
  </dl>
{/if}
  <dl> 
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl> 
</fieldset>
</div>
