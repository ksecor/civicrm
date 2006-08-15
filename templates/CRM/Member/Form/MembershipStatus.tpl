{* this template is used for adding/editing/deleting membership status  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Membership Status{/ts}{elseif $action eq 2}{ts}Edit Membership Status{/ts}{else}{ts}Delete Membership Status{/ts}{/if}</legend>
  
   {if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
          {ts}WARNING: Deleting this option will result in the loss of all membership records of this status.{/ts} {ts}This may mean the loss of a substantial amount of data, and the action cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
     {else}
      <dl>
 	<dt>{$form.name.label}</dt><dd class="html-adjust">{$form.name.html}</dd>
    <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Name of the Membership status.For eg.New,Expired, Renewed Grace, Unrenewed...{/ts}</dd>
 	<dt>{$form.start_event.label}</dt><dd class="html-adjust">{$form.start_event.html}</dd>
    <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Select the event when the status starts.For eg.If the event is new then select the join date.{/ts}</dd>
 	<dt>{$form.start_event_adjust_unit.label}</dt><dd class="html-adjust">{$form.start_event_adjust_interval.html}&nbsp;&nbsp;{$form.start_event_adjust_unit.html}</dd>
    <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Event used for adjusting (add or substracting time) from the start event (e.g. 30 day, 2 month, 5 year).{/ts}</dd>
 	<dt>{$form.end_event.label}</dt><dd class="html-adjust">{$form.end_event.html}</dd>
    <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Event after which this status ends.{/ts}</dd>
 	<dt>{$form.end_event_adjust_unit.label}</dt><dd class="html-adjust">{$form.end_event_adjust_interval.html}&nbsp;&nbsp;{$form.end_event_adjust_unit.html}</dd>
    <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Event used for adjusting (add or substracting time) from the ending event (e.g. 25 day, 3 month, 4 year).{/ts}</dd>
        <dt>{$form.is_current_member.label}</dt><dd class="html-adjust">{$form.is_current_member.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Check this, if status is "current members" (e.g. using the sample status optiions below... New, Renewed, Grace - and possibly Pending could be TRUE, while Unrenewed, Lapsed, Inactive would be false).{/ts}</dd>
        <dt>{$form.is_admin.label}</dt><dd class="html-adjust">{$form.is_admin.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Is this status for admin/manual assignment only.{/ts}</dd>
        <dt>{$form.weight.label}</dt><dd class="html-adjust">{$form.weight.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Used for status display - and to resolve overlapping status calcs (e.g. could set "New" status with lower weight then "Current" - and memberships that meet both status range criteria are assigned the lower weight status).{/ts}</dd>   
        <dt>{$form.is_default.label}</dt><dd class="html-adjust">{$form.is_default.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Is this status default.{/ts}</dd>   
        <dt>{$form.is_active.label}</dt><dd class="html-adjust">{$form.is_active.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Is this status enabled.{/ts}</dd>   
      </dl> 
     {/if}
    <dl>   
      <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
