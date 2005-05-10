{* this template is used for adding/editing a tag (admin)  *}
<form {$form.attributes}>
<div class="form-item">
<fieldset><legend>{if $action eq 1}New{elseif $action eq 2}Edit {else} Delete{/if} Tag</legend>

   <dl>
   {if $action eq 1 or $action eq 2 }
      <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
      <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
   {else}
      <div class="status">Are you sure you want to delete <b>{$delName}</b> Tag?</div>
   {/if}   
	<dt></dt><dd>{$form.buttons.html}</dd>
   <div class="spacer"></div>
   </dl>

</fieldset>
</div>
</form>
