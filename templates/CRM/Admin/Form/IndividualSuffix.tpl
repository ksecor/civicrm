{* this template is used for adding/editing individual suffix  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Individual Suffix{/ts}{elseif $action eq 8}{ts}Delete Individual Suffix{/ts}{else}{ts}Edit Individual Suffix{/ts}{/if}</legend>
	{if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
          {ts}WARNING: Deleting this option will change all Individual records which use the option.{/ts} {ts}This may mean the loss of a substantial amount of data, and the action cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
     {else}

  	<dl>
	<dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
	<dt>{$form.weight.label}</dt><dd>{$form.weight.html}</dd>
        <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>        
  	</dl>
     {/if}
	<dl><dt></dt><dd>{$form.buttons.html}</dd></dl>	
</fieldset>
</div>
