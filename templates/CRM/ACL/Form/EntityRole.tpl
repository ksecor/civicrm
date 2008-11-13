{* this template is used for adding/editing ACL EntityRole objects *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}Assign ACL Role{/ts}{elseif $action eq 2}{ts}Assign ACL Role{/ts}{else}{ts}Delete ACL Role Assignment{/ts}{/if}</legend>

{if $action eq 8}
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd>    
        {ts}WARNING: Deleting this option will remove this ACL Role Assignment.{/ts} {ts}Do you want to continue?{/ts}
      </dd>
    </dl>
  </div>
{else}
  <dl>
    <dt>{$form.acl_role_id.label}</dt><dd>{$form.acl_role_id.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select an ACL Role to assign.{/ts}</dd>
    <dt>{$form.entity_id.label}</dt><dd>{$form.entity_id.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select a group of contacts who should have this role when logged in to your site. Groups must be assigned the 'Access Control' type (Manage Groups &raquo; Settings) to be included in this list.{/ts}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
  </dl>
{/if}
  <dl> 
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl> 
</fieldset>
</div>
