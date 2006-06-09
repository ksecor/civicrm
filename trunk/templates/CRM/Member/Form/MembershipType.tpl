{* this template is used for adding/editing/deleting membership type  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Membership Type{/ts}{elseif $action eq 2}{ts}Edit Membership Type{/ts}{else}{ts}Delete Membership Type{/ts}{/if}</legend>
  
   {if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
          <dd>    
          {ts}WARNING: Deleting this option will result in the loss of all membership records of this type.{/ts} {ts}This may mean the loss of a substantial amount of data, and the action cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
     {else}
      <dl>
 	<dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
    	<dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
        <dt>{$form.minimum_fee.label}</dt><dd>{$form.minimum_fee.html}</dd>
        <dt>{$form.duration_unit.label}</dt><dd>{$form.duration_unit.html}</dd>
        <dt>{$form.duration_interval.label}</dt><dd>{$form.duration_interval.html}</dd>
        <dt>{$form.period_type.label}</dt><dd>{$form.period_type.html}</dd>
        <dt>{$form.fixed_period_start_day.label}</dt><dd>{$form.fixed_period_start_day.html}</dd>
        <dt>{$form.fixed_period_rollover_day.label}</dt><dd>{$form.fixed_period_rollover_day.html}</dd>
        <dt>{$form.contribution_type_id.label}</dt><dd>{$form.contribution_type_id.html}</dd>
        <dt>{$form.relation_type_id.label}</dt><dd>{$form.relation_type_id.html}</dd>
        <dt>{$form.visibility.label}</dt><dd>{$form.visibility.html}</dd>
        <dt>{$form.is_default.label}</dt><dd>{$form.is_default.html}</dd>
        <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
      </dl> 
     {/if}
    <dl>   
      <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
