{* this template is used for adding/editing ACL  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New ACL{/ts}{elseif $action eq 2}{ts}Edit ACL{/ts}{else}{ts}Delete ACL{/ts}{/if}</legend>

{if $action eq 8}
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd>    
        {ts}WARNING: Delete will remove this permission from the specified ACL Role.{/ts} {ts}Do you want to continue?{/ts}
      </dd>
    </dl>
  </div>
{else}
  <dl>
    <dt>{$form.object_table.label}</dt><dd>{$form.object_table.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select the core ACL.{/ts}</dd>
  </dl>
  <dl>
    <dt>{$form.entity_id.label}</dt><dd>{$form.entity_id.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select a Role to assign (grant) this permission to. Select the special role "Everyone" if you want to grant this permission to ALL users. "Anyone" includes anonymous (i.e. not logged in) users.{/ts}</dd>
    <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Enter a descriptive name for this permission (e.g. "Edit Advisory Board Contacts").{/ts}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
  </dl>
{/if}
  <dl> 
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl> 
</fieldset>
</div>

