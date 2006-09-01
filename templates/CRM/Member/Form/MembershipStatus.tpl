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
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Display name for this Membership status (e.g. New, Current, Grace, Expired...).{/ts}</dd>
        <dt>{$form.start_event.label}</dt><dd class="html-adjust">{$form.start_event.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}When does this status begin? EXAMPLE: <strong>New</strong> status begins at the membership "join date".{/ts}</dd>
        <dt>{$form.start_event_adjust_unit.label}</dt><dd class="html-adjust">{$form.start_event_adjust_interval.html}&nbsp;&nbsp;{$form.start_event_adjust_unit.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Optional adjustment period added or subtracted from the Start Event. EXAMPLE: <strong>Current</strong> status might begin at "join date" PLUS 3 months (to distinguish Current from New members).{/ts}</dd>
        <dt>{$form.end_event.label}</dt><dd class="html-adjust">{$form.end_event.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}When does this status end? EXAMPLE: <strong>Current</strong> status ends at the membership "end date".{/ts}</dd>
        <dt>{$form.end_event_adjust_unit.label}</dt><dd class="html-adjust">{$form.end_event_adjust_interval.html}&nbsp;&nbsp;{$form.end_event_adjust_unit.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Optional adjustment period added or subtracted from the End Event. EXAMPLE: <strong>Grace</strong> status might end at "end date" PLUS 1 month.{/ts}</dd>
        <dt>{$form.is_current_member.label}</dt><dd class="html-adjust">{$form.is_current_member.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Should this status be considered a current membership in good standing. EXAMPLE: New, Current and Grace could all be considered "current".{/ts}</dd>
        <dt>{$form.is_admin.label}</dt><dd class="html-adjust">{$form.is_admin.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Is this status for use by administrative staff only? If checked, this status is never automatically assigned by CiviMember. EXAMPLE: This setting can be useful for special case statuses like "Non-expiring", "Barred" or "Expelled", etc.{/ts}</dd>
        <dt>{$form.weight.label}</dt><dd class="html-adjust">{$form.weight.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Weight sets the order of precedence for automatic assignment of status to a membership. It also sets the order for status displays. EXAMPLE: The default "New" and "Current" statuses have overlapping ranges.  Memberships that meet both status range criteria are assigned the status with the lower weight.{/ts}</dd> 
        <dt>{$form.is_default.label}</dt><dd class="html-adjust">{$form.is_default.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}The default status is assigned when there are no matching status rules for a membership.{/ts}</dd>   
        <dt>{$form.is_active.label}</dt><dd class="html-adjust">{$form.is_active.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Is this status enabled.{/ts}</dd>
        </dl> 
  {/if}
  <dl>   
      <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>
  </dl>
  <br clear="all" />
</fieldset>
</div>
