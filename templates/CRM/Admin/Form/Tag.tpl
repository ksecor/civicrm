{* this template is used for adding/editing a tag (admin)  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Tag{/ts}{elseif $action eq 2}{ts}Edit Tag{/ts}{else}{ts}Delete Tag{/ts}{/if}</legend>

   <dl>
   {if $action eq 1 or $action eq 2 }
      <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
      <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
   {else}
      <div class="status">{ts 1=$delName}Are you sure you want to delete <b>%1</b> Tag?{/ts}</div>
   {/if}   
	<dt></dt><dd>{$form.buttons.html}</dd>
   <div class="spacer"></div>
   </dl>

</fieldset>
</div>
