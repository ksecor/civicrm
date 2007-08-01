{* this template is used for adding/editing/deleting membership type  *}
<fieldset>
<legend>{if $action eq 1}{ts}New Membership Type{/ts}{elseif $action eq 2}{ts}Edit Membership Type{/ts}{else}{ts}Delete Membership Type{/ts}{/if}</legend>
<div class="form-item" id="membership_type_form">
    {if $action eq 8}   
        <div class="messages status">
        {ts}WARNING: Deleting this option will result in the loss of all membership records of this type.{/ts} {ts}This may mean the loss of a substantial amount of data, and the action cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
         </div>
     <dl><dt>&nbsp;</dt><dd>{$form.buttons.html}</dd></dl>
    {else}
    <table class="form-layout-compressed"> 
        <tr><td>{$form.name.label}</td><td class="html-adjust">{$form.name.html}</td></tr>
        <tr><td>&nbsp;</dt><td class="description html-adjust">{ts}e.g. "Student", "Senior", "Honor Society"...{/ts}</td></tr>
    	<tr><td>{$form.description.label}</td><td class="html-adjust">{$form.description.html}</td></tr>
        <tr><td>&nbsp;</td><td class="description html-adjust">{ts}Description of this membership type for display on signup forms. May include eligibility, benefits, terms, etc.{/ts}</td></tr>
    {if !$searchDone or !$searchCount or !$searchRows}
        <tr><td>{$form.member_org.label}<span class="marker"> *</span></td><td class="html-adjust"><label>{$form.member_org.html}</label>&nbsp;&nbsp;{$form._qf_MembershipType_refresh.html}</td></tr>
        <tr><td>&nbsp;</td><td class="description html-adjust">{ts}Members assigned this membership type belong to which organization (e.g. this is for membership in "Save the Whales - Northwest Chapter"). NOTE: This organization/group/chapter must exist as a CiviCRM Organization type contact.{/ts}</td></tr>
        {/if} 
    </table>
    <div class="spacer"></div>	
        {if $searchDone} {* Search button clicked *}
            {if $searchCount}
                {if $searchRows} {* we've got rows to display *}
                    <fieldset><legend>{ts}Select Target Contact for the Membership-Organization{/ts}</legend>
	    <dl>                
	    <dt>{$form.member_org.label}</dt><dd class="html-adjust">{$form.member_org.html}&nbsp;&nbsp;{$form._qf_MembershipType_refresh.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Organization, who is the owner for this membership type.{/ts}</dd>
	    </dl>
		    <br class="spacer"/>
                <div class="description">
                    {ts}Select the target contact for this membership-organization if it appears below. Otherwise you may modify the search name above and click Search again.{/ts}
                        </div>
                        {strip}
                        <table>
                        <tr class="columnheader">
                        <th>&nbsp;</th>
                        <th>{ts}Name{/ts}</th>
                        <th>{ts}City{/ts}</th>
                        <th>{ts}State{/ts}</th>
                        <th>{ts}Email{/ts}</th>
                        <th>{ts}Phone{/ts}</th>
                        </tr>
                        {foreach from=$searchRows item=row}
                        <tr class="{cycle values="odd-row,even-row"}">
                            <td>{$form.contact_check[$row.id].html}</td>
                            <td>{$row.type} {$row.name}</td>
                            <td>{$row.city}</td>
                            <td>{$row.state}</td>
                            <td>{$row.email}</td>
                            <td>{$row.phone}</td>
                        </tr>
                        {/foreach}
                        </table>
                        {/strip}
                        </fieldset>{*End of Membership Organization Block*}

                    {else} {* too many results - we're only displaying 50 *}
                        {capture assign=infoMessage}{ts}Too many matching results. Please narrow your search by entering a more complete target contact name.{/ts}{/capture}
                        {include file="CRM/common/info.tpl"}
                    {/if}
                {else} {* no valid matches for name + contact_type *}
                        {capture assign=infoMessage}{ts 1=$form.member_org.value 2=Organization}No matching results for <ul><li>Name like: %1</li><li>Contact type: %2</li></ul>Check your spelling, or try fewer letters for the target contact name.{/ts}{/capture}
                        {include file="CRM/common/info.tpl"}                
                {/if} {* end if searchCount *}
              {/if} {* end if searchDone *}

    <table class="form-layout-compressed"> 
        <tr><td>{$form.minimum_fee.label}</td><td class="html-adjust">{$config->defaultCurrencySymbol()}&nbsp;{$form.minimum_fee.html}</td></tr>
        <tr><td>&nbsp;</td><td class="description html-adjust">&nbsp;&nbsp;{ts}Minimum fee required for this membership type. For free/complimentary memberships - set minimum fee to zero (0).{/ts}</td></tr>
       	<tr><td>{$form.contribution_type_id.label}<span class="marker"> *</span></td><td class="html-adjust">&nbsp;&nbsp;{$form.contribution_type_id.html}</td></tr>
        <tr><td>&nbsp;</td><td class="description html-adjust">&nbsp;&nbsp;{ts}Select the contribution type assigned to fees for this membership type (for example "Membership Fees"). This is required for all membership types - including free or complimentary memberships.{/ts}</td></tr>
        <tr><td>{$form.duration_unit.label}<span class="marker">*</span></td><td class="html-adjust">&nbsp;&nbsp;{$form.duration_interval.html}&nbsp;&nbsp;{$form.duration_unit.html}</td></tr>
        <tr><td>&nbsp;</td><td class="description html-adjust">&nbsp;&nbsp;{ts}Duration of this membership (e.g. 30 days, 2 months, 5 years, 1 lifetime){/ts}</td></tr>

        <tr><td>{$form.period_type.label}<span class="marker"> *</span></td><td class="html-adjust">&nbsp;&nbsp;{$form.period_type.html}</td></tr>     
        <tr><td>&nbsp;</td><td class="description html-adjust">&nbsp;&nbsp;{ts}Select "rolling" if membership periods begin at date of signup. Select "fixed" if membership periods begin on a set calendar date.{/ts}</td></tr>
    </table>   
    	
    <table id="fixed_period_settings" class="form-layout-compressed">
        <tr><td>{$form.fixed_period_start_day.label}</td><td class="html-adjust">{$form.fixed_period_start_day.html}</td></tr>
        <tr><td>&nbsp;</td><td class="description html-adjust">{ts}Month and day on which a <strong>fixed</strong> period membership or subscription begins. Example: A fixed period membership with Start Day set to Jan 01 means that membership periods would be 1/1/06 - 12/31/06 for anyone signing up during 2006.{/ts}</td></tr>
        <tr><td>{$form.fixed_period_rollover_day.label}</td><td class="html-adjust">{$form.fixed_period_rollover_day.html}</td></tr>
        <tr><td>&nbsp;</td><td class="description html-adjust">{ts}Membership signups after this date cover the following calendar year as well. Example: If the rollover day is November 31, membership period for signups during December will cover the following year.{/ts}</td></tr>
    </table>

    <table class="form-layout-compressed"> 	
        <tr><td>{$form.relationship_type_id.label}</td><td class="html-adjust">&nbsp;&nbsp;&nbsp;&nbsp;{$form.relationship_type_id.html}</td></tr>
        <tr><td>&nbsp;</td><td class="description html-adjust">&nbsp;&nbsp;{ts}Select relationship type for this membership type. EXAMPLE: Select 'Household Member is' for memberships where the  <strong>direct member is a Household</strong> and you want all <strong>Household Members</strong> to be automatically granted memberships.{/ts}</td></tr>
        <tr><td>{$form.visibility.label}</td><td class="html-adjust">&nbsp;&nbsp;&nbsp;{$form.visibility.html}</td></tr>
        <tr><td>&nbsp;</td><td class="description html-adjust">{ts}&nbsp;&nbsp;Is this membership type available for self-service signups ("Public") or assigned by CiviCRM "staff" users only ("Admin"){/ts}</td></tr>
        <tr><td>{$form.weight.label}</td><td class="html-adjust">&nbsp;&nbsp;{$form.weight.html}</td></tr>
        <tr><td>{$form.is_active.label}</td><td class="html-adjust">&nbsp;&nbsp;{$form.is_active.html}</td></tr>
        {*</dl>*}
        </table>{*End of table*}
        <div class="spacer"></div>
        <fieldset><legend>{ts}Renewal Reminders{/ts}</legend>
        {capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
        <div class="description">
            {ts 1="http://wiki.civicrm.org/confluence//x/ui" 2=$docURLTitle}If you would like Membership Renewal Reminder emails sent to members automatically, you need to create a reminder message template and you need to configure and periodically run a "cron" job on your server (<a href="%1" target="_blank" title="%2">more info...</a>).{/ts}
        </div>
        {if $noMsgTemplates}
            {capture assign=msgTemplate}{crmURL p='civicrm/admin/messageTemplates' q="action=add&reset=1"}{/capture}
            <div class="status message">
                {ts 1=$msgTemplate}No message templates have been created yet. If you want renewal reminders to be sent, <a href="%1">click here</a> to create a reminder email template. Then return to this screen to assign the renewal reminder message, and set reminder date.{/ts}
            </div>
        {else}
        <dl>
          <dt>{$form.renewal_msg_id.label}</dt><dd class="html-adjust">{$form.renewal_msg_id.html}</dd>
          <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Select the renewal reminder message to be sent to the members of this membership type.{/ts}</dd>
          <dt>{$form.renewal_reminder_day.label}</dt><dd class="html-adjust">{$form.renewal_reminder_day.html}</dd>
          <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Send Reminder these many days prior to membership expiration.{/ts}</dd>
        </dl>
        {/if}
        </fieldset>
        <dl>
        <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>
       </dl>
    {/if}
  <div class="spacer"></div>
</div>
</fieldset>

{literal}
    <script type="text/javascript">
	if (document.getElementsByName("period_type")[0].value == "fixed") {
	   show('fixed_period_settings');
	} else {
	   hide('fixed_period_settings');
	}
	function showHidePeriodSettings(){
	   if (document.getElementsByName("period_type")[0].value == "fixed") {
		show('fixed_period_settings');
		document.getElementsByName("fixed_period_start_day[M]")[0].value = "1";
		document.getElementsByName("fixed_period_start_day[d]")[0].value = "1";
		document.getElementsByName("fixed_period_rollover_day[M]")[0].value = "12";
		document.getElementsByName("fixed_period_rollover_day[d]")[0].value = "31";
	   } else {
		hide('fixed_period_settings');
		document.getElementsByName("fixed_period_start_day[M]")[0].value = "";
		document.getElementsByName("fixed_period_start_day[d]")[0].value = "";
		document.getElementsByName("fixed_period_rollover_day[M]")[0].value = "";
		document.getElementsByName("fixed_period_rollover_day[d]")[0].value = "";
	   }
	} 
    </script>
{/literal}
