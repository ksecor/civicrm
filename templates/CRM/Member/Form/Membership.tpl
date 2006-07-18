{* this template is used for adding/editing/deleting memberships for a contact  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Membership{/ts}{elseif $action eq 2}{ts}Edit Membership{/ts}{else}{ts}Delete Membership{/ts}{/if}</legend>
  
   {if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
          {ts}WARNING: Deleting this membership will also delete related membership log and payment records. This action can not be undone. Consider modifying membership status instead if you want to maintain a record of this membership.{/ts}
          {ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
     {else}
      <dl>
 	<dt>{$form.membership_type_id.label}</dt><dd class="html-adjust">{$form.membership_type_id.html}</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Membership type for this membership. Membership types are configurable under Administer CiviCRM menu.{/ts}</dd> 	
	<dt>{$form.join_date.label}</dt><dd class="html-adjust">{$form.join_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_membership_1}
		{include file="CRM/common/calendar/body.tpl" dateVar=join_date startDate=currentYear endDate=endYear offset=5 trigger=trigger_membership_1}
		</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}When did this contact first become a member (defaults to today's date)?{/ts}</dd>
 	<dt>{$form.start_date.label}</dt><dd class="html-adjust">{$form.start_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_membership_2}
		{include file="CRM/common/calendar/body.tpl" dateVar=start_date startDate=currentYear endDate=endYear offset=5 trigger=trigger_membership_2}
		</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}First day of current continuous membership period. Start Date will be automatically set based on Membership Type if you don't select a date.{/ts}</dd>
 	<dt>{$form.end_date.label}</dt><dd class="html-adjust">{$form.end_date.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_membership_3}
		{include file="CRM/common/calendar/body.tpl" dateVar=end_date startDate=currentYear endDate=endYear offset=5 trigger=trigger_membership_3}
		</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Latest membership period expiration date. End Date will be automatically set based on Membership Type if you don't select a date.{/ts}</dd>
    <dt>{$form.source.label}</dt><dd class="html-adjust">{$form.source.html}</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Source of this membership sign-up. For self-service sign-ups, source is set to the name of the Online Contribution page.{/ts}</dd>
    <dt>{$form.is_override.label}</dt><dd class="html-adjust">{$form.is_override.html}</dd>
	<dt>&nbsp;</dt><dd class="description html-adjust">{ts}Membership status is set and updated automatically based on your configured membership status rules. Check this box if you want to bypass this process, and manually set a status for this membership. The selected status which will remain in force unless it is again modified on this screen.{/ts}</dd>
    </dl>

    {* Show read-only Status block - when action is UPDATE and is_override is FALSE *}
    <div id="memberStatus_show">
        {if $action eq 2}
        <dl>
        <dt>{$form.status_id.label}</dt><dd class="html-adjust">{$membershipStatus}</dd>
        </dl>
        {/if}
    </div>
    
    {* Show editable status field when is_override is TRUE *}
    <div id="memberStatus">
        <dl>
        <dt>{$form.status_id.label}</dt><dd class="html-adjust">{$form.status_id.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}If <strong>Status Hold?</strong> is checked, the selected status will be in in force (it will NOT be modified by the automated status update script).{/ts}</dd>
        </dl>
    </div>
  {/if}
  <dl>   
    <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>

{literal}
<script type="text/javascript">
showHideMemberStatus();
    
function showHideMemberStatus() {
	if (document.getElementsByName("is_override")[0].checked == true) {
	   show('memberStatus');
       hide('memberStatus_show');
	} else {
	   hide('memberStatus');
       show('memberStatus_show');
	}
}
</script>
{/literal}
