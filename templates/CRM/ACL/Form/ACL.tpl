{* this template is used for adding/editing ACL  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New ACL{/ts}{elseif $action eq 2}{ts}Edit ACL{/ts}{else}{ts}Delete ACL{/ts}{/if}</legend>

{if $action eq 8}
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd>    
        {ts}WARNING: Deleting this option will remove this ACL from all ACL group permissions.{/ts}{ts}Do you want to continue?{/ts}
      </dd>
    </dl>
  </div>
{else}
  <dl>
    <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
    <dt>{$form.deny.label}</dt><dd>{$form.deny.html}</dd>
    <dt>{$form.operation.label}</dt><dd>{$form.operation.html}</dd>
    <dt>{$form.entity_table.label}</dt><dd>{$form.entity_table.html}</dd>
    <dt>{$form.entity_id.label}</dt><dd>{$form.entity_id.html}</dd>
    <dt>{$form.object_table.label}</dt><dd>{$form.object_table.html}</dd>
    <dt>{$form.object_id.label}</dt><dd>{$form.object_id.html}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
  </dl>
{/if}
  <dl> 
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl> 
</fieldset>
</div>
