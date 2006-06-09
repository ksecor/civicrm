{* this template is used for deleting an activity history *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Activity History{/ts}{elseif $action eq 2}{ts}Edit Activity History{/ts}{else}{ts}Delete Activity History{/ts}{/if}</legend>
   <dl>
   {if $action eq 1 or $action eq 2 }
      <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
      <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
   {else}
      <div class="status">{ts 1=$activity_type 2=$activity_date|crmDate 3=$activity_summary}Are you sure you want to delete the Activity History dated "%2" of type "%1" with description "%3" ?{/ts}</div>
   {/if}   
	<dt></dt><dd>{$form.buttons.html}</dd>
   <div class="spacer"></div>
   </dl>
</fieldset>
</div>
