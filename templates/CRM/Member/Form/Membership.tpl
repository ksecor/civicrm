{* this template is used for adding/editing/deleting memberships for a contact  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Membership{/ts}{elseif $action eq 2}{ts}Edit Membership{/ts}{else}{ts}Delete Membership{/ts}{/if}</legend>
  
   {if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
          <dd>    
          {ts}WARNING: Deleting this membership will also delete related membership log and payment records. This action can not be undone. Consider modifying membership status instead if you want to maintain a record of this membership.{/ts}
          {ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
     {else}
      <dl>
 	<dt>{$form.membership_type_id.label}</dt><dd class="html-adjust">{$form.membership_type_id.html}</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Membership type for this membership.{/ts}</dd> 	
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
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Source of this membership sign-up. for self-service sign-ups, Source is set to the name of the Online Contribution page.{/ts}</dd>
    	<dt>{$form.status_id.label}</dt><dd class="html-adjust">{$form.status_id.html}</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Status will be automatically set based on Membership Status rules unless you check the Status Hold box.{/ts}</dd>
        <dt>{$form.is_override.label}</dt><dd class="html-adjust">{$form.is_override.html}</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Check this box if you want to bypass automatic status updates. The selected status will remain in force unless it is modified manually on this screen.{/ts}</dd>
      </dl> 
     {/if}
    <dl>   
      <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
