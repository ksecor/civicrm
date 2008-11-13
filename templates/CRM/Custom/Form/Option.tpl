<div class="form-item">
<fieldset><legend>{if $action eq 8 }{ts}Selection Options{/ts}{else}{ts}Selection Options{/ts}{/if}</legend>
      {if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
          {ts}WARNING: Deleting this custom option will result in the loss of all data.{/ts} {ts}This action cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
     {else}
	<dl>
        <dt>{$form.label.label}</dt><dd>&nbsp;{$form.label.html}</dd>
        <dt>{$form.value.label}</dt><dd>&nbsp;{$form.value.html}</dd>
        <dt>{$form.weight.label}</dt><dd>&nbsp;{$form.weight.html}</dd>
        <dt>{$form.is_active.label}</dt><dd>&nbsp;{$form.is_active.html}</dd>
	    <dt>{$form.default_value.label}</dt><dd>&nbsp;{$form.default_value.html}</dd>
        <dt>&nbsp;</dt><dd class="description"><span>{ts}Make this option value 'selected' by default?{/ts}</span></dd>
	</dl>
      {/if}
    
    
    <div id="crm-submit-buttons" class="form-item">
    <dl>
    {if $action ne 4}
        <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>
    {else}
        <dt>&nbsp;</dt><dd>{$form.done.html}</dd>
    {/if} {* $action ne view *}
    </dl>
    </div>

</fieldset>
</div>
