{* this template is used for adding/editing location type  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Location Type{/ts}{elseif $action eq 2}{ts}Edit Location Type{/ts}{else}{ts}Delete Location Type{/ts}{/if}</legend>

{if $action eq 8}
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd>    
        {ts}WARNING: Deleting this option will result in the loss of all location type records which use the option.{/ts} {ts}This may mean the loss of a substantial amount of data, and the action cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
      </dd>
    </dl>
  </div>
{else}
  <dl>
    <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
    <dt>{$form.vcard_name.label}</dt><dd>{$form.vcard_name.html}</dd>
    <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
    <dt>{$form.is_default.label}</dt><dd>{$form.is_default.html}</dd>
  </dl>
{/if}
  <dl> 
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl> 
</fieldset>
</div>
