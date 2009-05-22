{* this template is used for adding/editing CiviCRM Menu *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Menu{/ts}{elseif $action eq 2}{ts}Edit Menu{/ts}{else}{ts}Delete Menu{/ts}{/if}</legend>
<table class="form-layout-compressed">
{if $action eq 8}
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd>    
        {ts}WARNING: This may mean the loss of a substantial amount of data, and the action cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
      </dd>
    </dl>
  </div>
{else}
    <tr><td class="label">{$form.label.label}</td><td>{$form.label.html}</td></tr>
    <tr><td class="label">{$form.url.label}</td><td>{$form.url.html}</td></tr>
    <tr><td class="label">{$form.permission.label}</td><td>{$form.permission.html}</td></tr>
    <tr><td class="label">&nbsp;</td><td>{$form.permission_operator.html}&nbsp;{$form.permission_operator.label}</td></tr>
    <tr><td class="label">{$form.parent_id.label}</td><td>{$form.parent_id.html}</td></tr>
    <tr><td class="label">{$form.has_separator.label}</td><td>{$form.has_separator.html}<br/><span class="description">{ts}Check to add separator after this menu item{/ts}</span></td></tr>
    <tr><td class="label">{$form.is_active.label}</td><td>{$form.is_active.html}</td></tr>
{/if}
  <tr><td></td></tr>    
  <tr> 
    <td colspan="2">{$form.buttons.html}</td>
  </tr>
</table>   
</fieldset>
</div>