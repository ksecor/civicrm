{* this template is used for adding/editing/deleting membership type  *}
<fieldset>
<legend>{if $action eq 1}{ts}New Membership Type{/ts}{elseif $action eq 2}{ts}Edit Membership Type{/ts}{else}{ts}Delete Membership Type{/ts}{/if}</legend>
<div class="form-item">
  
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
 	<dt>{$form.name.label}</dt><dd class="html-adjust">{$form.name.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}e.g. "Student", "Senior", "Honor Society"...{/ts}</dd>
    	<dt>{$form.description.label}</dt><dd class="html-adjust">{$form.description.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Additional display for Profile field radio buttons. May include cost, terms, etc.{/ts}</dd>
       	<dt>{$form.contribution_type_id.label}</dt><dd class="html-adjust">{$form.contribution_type_id.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Organization which is the owner for this membership type.{/ts}</dd>
        <dt>{$form.member_org.label}</dt><dd class="html-adjust">{$form.member_org.html}&nbsp;&nbsp;{$form._qf_MembershipType_refresh.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Select the contribution type for this membership type.{/ts}</dd>
       </dl>
       <div class="spacer"></div>	
              {if $searchDone} {* Search button clicked *}
                {if $searchCount}
                    {if $searchRows} {* we've got rows to display *}
                        <fieldset>
			<legend>{ts}Select Target Contact for the Membership-Organization{/ts}</legend>
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
                        </fieldset>
                    {else} {* too many results - we're only displaying 50 *}
                        {include file="CRM/common/info.tpl"}
                    {/if}
                {else} {* no valid matches for name + contact_type *}
                        {capture assign=infoMessage}{ts 1=$form.member_org.value 2=Organization}No matching results for <ul><li>Name like: %1</li><li>Contact type: %2</li></ul>Check your spelling, or try fewer letters for the target contact name.{/ts}{/capture}
                        {include file="CRM/common/info.tpl"}                
                {/if} {* end if searchCount *}
              {/if} {* end if searchDone *}
	
       <dl>
        <dt>{$form.minimum_fee.label}</dt><dd class="html-adjust">{$form.minimum_fee.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Free/complimentary memberships have minimum_fee = 0{/ts}</dd>
        <dt>{$form.duration_unit.label}</dt><dd class="html-adjust">{$form.duration_unit.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}What is the unit used for the duration of this membership (e.g. day, month, year, lifetime){/ts}</dd>
        <dt>{$form.duration_interval.label}</dt><dd class="html-adjust">{$form.duration_interval.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}How many units in the duration (e.g. 12 months){/ts}</dd>
        <dt>{$form.period_type.label}</dt><dd class="html-adjust">{$form.period_type.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}"rolling" (default - means at date of payment), or "fixed" membership period{/ts}</dd>
       </dl>
	   <div id="fixed_period_settings">	
             <dt>{$form.fixed_period_start_day.label}</dt><dd class="html-adjust">{$form.fixed_period_start_day.html}</dd>
             <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Month and day (MMDD) on which a fixed period subscription or membership will start. Default 0101. EXAMPLE: A fixed period membership with Start Day set to 0101 means that the membership period would be 1/1/06 - 12/31/06 for anyone signing up during 2006.{/ts}</dd>
             <dt>{$form.fixed_period_rollover_day.label}</dt><dd class="html-adjust">{$form.fixed_period_rollover_day.html}</dd>
             <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Fixed Membership Payments after this date cover following calendar year as well (default to 1231 - December 31- i.e. no extra coverage; Joe's organization uses October 31).{/ts}</dd>
	   </div>
       <dl>	
        <dt>{$form.relation_type_id.label}</dt><dd class="html-adjust">{$form.relation_type_id.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}FK to civicrm_relationship_type. If NOT NULL, then contacts who have the specified relationship to the direct member are also considered to be members. (e.g. if relationship_type_id = Household Member, and the direct member is a household, then all household members for that household are also considered to be members.{/ts}</dd>
        <dt>{$form.visibility.label}</dt><dd class="html-adjust">{$form.visibility.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}is this membership type available for self-service signups ("Public") or assigned by CiviCRM "staff" users only ("Admin"){/ts}</dd>
        <dt>{$form.weight.label}</dt><dd class="html-adjust">{$form.weight.html}</dd>
        <dt>{$form.is_active.label}</dt><dd class="html-adjust">{$form.is_active.html}</dd>
        <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>
       </dl>
    {/if}
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
	   } else {
		hide('fixed_period_settings');
	   }
	} 
    </script>
{/literal}
