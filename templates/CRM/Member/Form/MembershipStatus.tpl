{* this template is used for adding/editing/deleting membership status  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Membership Status{/ts}{elseif $action eq 2}{ts}Edit Membership Status{/ts}{else}{ts}Delete Membership Status{/ts}{/if}</legend>
  
   {if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
          <dd>    
          {ts}WARNING: Deleting this option will result in the loss of all membership records of this status.{/ts} {ts}This may mean the loss of a substantial amount of data, and the action cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
     {else}
      <dl>
 	<dt>{$form.name.label}</dt><dd class="html-adjust">{$form.name.html}</dd>
 	<dt>{$form.start_event.label}</dt><dd class="html-adjust">{$form.start_event.html}</dd>
 	<dt>{$form.start_event_adjust_unit.label}</dt><dd class="html-adjust">{$form.start_event_adjust_unit.html}</dd>
 	<dt>{$form.start_event_adjust_interval.label}</dt><dd class="html-adjust">{$form.start_event_adjust_interval.html}</dd>
 	<dt>{$form.end_event.label}</dt><dd class="html-adjust">{$form.end_event.html}</dd>
 	<dt>{$form.end_event_adjust_unit.label}</dt><dd class="html-adjust">{$form.end_event_adjust_unit.html}</dd>
 	<dt>{$form.end_event_adjust_interval.label}</dt><dd class="html-adjust">{$form.end_event_adjust_interval.html}</dd>
        <dt>{$form.is_current_member.label}</dt><dd class="html-adjust">{$form.is_current_member.html}</dd>
        <dt>{$form.is_admin.label}</dt><dd class="html-adjust">{$form.is_admin.html}</dd>
        <dt>{$form.is_default.label}</dt><dd class="html-adjust">{$form.is_default.html}</dd>
        <dt>{$form.is_active.label}</dt><dd class="html-adjust">{$form.is_active.html}</dd>
      </dl> 
     {/if}
    <dl>   
      <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
