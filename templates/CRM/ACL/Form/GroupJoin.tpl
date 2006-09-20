{* this template is used for adding/editing ACL GroupJoin objects *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New ACL GroupJoin{/ts}{elseif $action eq 2}{ts}Edit ACL GroupJoin{/ts}{else}{ts}Delete ACL GroupJoin{/ts}{/if}</legend>

{if $action eq 8}
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd>    
        {ts}WARNING: Deleting this option will remove this ACL GroupJoin.{/ts}{ts}Do you want to continue?{/ts}
      </dd>
    </dl>
  </div>
{else}
  <dl>
    <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
    <dt>{$form.acl_group_id.label}</dt><dd>{$form.acl_group_id.html}</dd>
    <dt>{$form.entity_table.label}</dt><dd>{$form.entity_table.html}</dd>
    <dt>{$form.entity_id.label}</dt><dd>{$form.entity_id.html}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
  </dl>
{/if}
  <dl> 
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl> 
</fieldset>
</div>
