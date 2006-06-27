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
 	<dt>{$form.membership_type_id.label}</dt><dd class="html-adjust">{$form.membership_type_id.html}</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Membership type to be associated with this membership.{/ts}</dd> 	
	<dt>{$form.join_date.label}</dt><dd class="html-adjust">{$form.join_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger1}
		{include file="CRM/common/calendar/body.tpl" dateVar=join_date startDate=currentYear endDate=endYear offset=5 trigger=trigger1}
		</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Beginning of initial membership period.{/ts}</dd>
 	<dt>{$form.start_date.label}</dt><dd class="html-adjust">{$form.start_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger1}
		{include file="CRM/common/calendar/body.tpl" dateVar=start_date startDate=currentYear endDate=endYear offset=5 trigger=trigger1}
		</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Latest membership period start/renew date. Start Date will be automatically set based on Membership Type if you don't select a date.{/ts}</dd>
 	<dt>{$form.end_date.label}</dt><dd class="html-adjust">{$form.end_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger1}
		{include file="CRM/common/calendar/body.tpl" dateVar=end_date startDate=currentYear endDate=endYear offset=5 trigger=trigger1}
		</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Latest membership period expire date. End Date will be automatically set based on Membership Type if you don't select a date.{/ts}</dd>
    	<dt>{$form.source.label}</dt><dd class="html-adjust">{$form.source.html}</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Arbitrary source string. Name of CiviContribute page or Profile is assigned for self-service sign-ups.{/ts}</dd>
    	<dt>{$form.status_id.label}</dt><dd class="html-adjust">{$form.status_id.html}</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Calculated Status based on rules. Status can be overriden by setting a value in the 'Override' field{/ts}</dd>
        <dt>{$form.is_override.label}</dt><dd class="html-adjust">{$form.is_override.html}</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}You may over-ride the calculated status by setting a value in this field (e.g. may be used to set a "Suspended" or other exception status).{/ts}</dd>
      </dl> 
     {/if}
    <dl>   
      <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
