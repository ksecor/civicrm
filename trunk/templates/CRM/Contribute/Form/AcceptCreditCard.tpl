{* this template is used for adding/editing/deleting Credit Card  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Credit Card{/ts}{elseif $action eq 2}{ts}Edit Credit Card{/ts}{else}{ts}Delete Credit Card{/ts}{/if}</legend>
  
   {if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
          {ts}WARNING: If you delete this option, contributors will not be able to use this credit card type on your Online Contribution pages.{/ts} {ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
     {else}
      <dl>
 	    <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}The name for this credit card type as it should be provided to your payment processor.{/ts}</dd>
    	<dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}The name for this credit card type as it is displayed to contributors. This may be the same value as the Name above, or a localised title.{/ts}</dd>
        <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
      </dl> 
     {/if}
    <dl>   
      <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
