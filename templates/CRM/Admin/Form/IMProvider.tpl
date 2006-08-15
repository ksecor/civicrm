{* this template is used for adding/editing IM Provider  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New IM Service Provider{/ts}{elseif $action eq 8}{ts}Delete IM Service Provider{/ts}{else}{ts}Edit IM Service Provider{/ts}{/if}</legend>
    {if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
          {ts}WARNING: Deleting this option will result in the loss of all IM Service Provider type records which use the option.{/ts} {ts}This may mean the loss of a substantial amount of data, and the action cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
     {else}
    <dl>
	<dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
    </dl>
    {/if}
 <dl><dt></dt><dd>{$form.buttons.html}</dd></dl>
</fieldset>
</div>
