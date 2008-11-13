{* this template is used for adding/editing/deleting activity type  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Option Value{/ts}{elseif $action eq 2}{ts}Edit Option Value{/ts}{else}{ts}Delete Option Value{/ts}{/if}</legend>
  
   {if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
          {ts}WARNING: Deleting this option value will result in the loss of all records which use the option value.{/ts} {ts}This may mean the loss of a substantial amount of data, and the action cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
     {else}
      <dl>
 	    <dt>{$form.label.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_option_value' field='label' id=$id}{/if}</dt><dd>{$form.label.html}</dd>
        <dt>{$form.value.label}</dt><dd>{$form.value.html}</dd>
        <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
        <dt>{$form.grouping.label}</dt><dd>{$form.grouping.html}</dd>
    	<dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
        <dt>{$form.weight.label}</dt><dd>{$form.weight.html}</dd>
        <dt>{$form.is_default.label}</dt><dd>{$form.is_default.html}</dd>
        <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
        <dt>{$form.is_optgroup.label}</dt><dd>{$form.is_optgroup.html}</dd>
      </dl> 
     {/if}
    <dl>   
      <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
