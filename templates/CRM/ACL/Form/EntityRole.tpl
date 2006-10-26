{* this template is used for adding/editing ACL EntityRole objects *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New ACL EntityRole{/ts}{elseif $action eq 2}{ts}Edit ACL EntityRole{/ts}{else}{ts}Delete ACL EntityRole{/ts}{/if}</legend>

{if $action eq 8}
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd>    
        {ts}WARNING: Deleting this option will remove this ACL EntityRole.{/ts}{ts}Do you want to continue?{/ts}
      </dd>
    </dl>
  </div>
{else}
  <dl>
    <dt>{$form.acl_role_id.label}</dt><dd>{$form.acl_role_id.html}</dd>
    <dt>{$form.entity_id.label}</dt><dd>{$form.entity_id.html}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
  </dl>
{/if}
  <dl> 
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl> 
</fieldset>
</div>
