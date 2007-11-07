<div class="view-content">
{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update or delete *}              
    {include file="CRM/Member/Form/Membership.tpl"}
{elseif $action eq 4}
    {include file="CRM/Member/Form/MembershipView.tpl"}
{elseif $action eq 32768}  {* renew *}
    {include file="CRM/Member/Form/MembershipRenewal.tpl"}
{else}
    {if $permission EQ 'edit'}{capture assign=newURL}{crmURL p="civicrm/contact/view/membership" q="reset=1&action=add&cid=`$contactId`&context=membership"}{/capture}{/if}

    <div id="help">
        {ts 1=$newURL}Current and inactive memberships for {$displayName} are listed below.{/ts}
        {if $permission EQ 'edit'}{ts 1=$newURL}Click <a href="%1">New Membership</a> to record a new membership.{/ts}{/if}
    </div>
{/if}

{if $activeMembers}
<div id="memberships">
    {if $action ne 1 and $action ne 2 and $permission EQ 'edit'}
	    <div class="action-link">
    	<a href="{$newURL}">&raquo; {ts}New Membership{/ts}</a>
        </div>
    {/if}
    <div><label>{ts}Active Memberships{/ts}</label></div>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Membership{/ts}</th>
            <th>{ts}Start Date{/ts}</th>
            <th>{ts}End Date{/ts}</th>
            <th>{ts}Status{/ts}</th>
            <th>{ts}Source{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$activeMembers item=activeMember}
        <tr class="{cycle values="odd-row,even-row"} {$activeMember.class}">
	        <td>{$activeMember.membership_type}</td>
	        <td>{$activeMember.start_date|crmDate}</td>
	        <td>{$activeMember.end_date|crmDate}</td>
	        <td>{$activeMember.status}</td>
	        <td>{$activeMember.source}</td>
	        <td>{$activeMember.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

    </div>
</div>
{/if}

{if $inActiveMembers}
  {if NOT ($activeMembers)}
    <div class="action-link">
    	<a href="{$newURL}">&raquo; {ts}New Membership{/ts}</a>
    </div>
  {/if}

<div id="ltype">
<p></p>
    <div class="label font-red">{ts}Inactive Memberships{/ts}</div>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Membership{/ts}</th>
            <th>{ts}Start Date{/ts}</th>
            <th>{ts}End Date{/ts}</th>
            <th>{ts}Status{/ts}</th>
            <th>{ts}Source{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$inActiveMembers item=inActiveMember}
        <tr class="{cycle values="odd-row,even-row"} {$inActiveMember.class}">
	        <td>{$inActiveMember.membership_type}</td>
	        <td>{$inActiveMember.start_date|crmDate}</td>
	        <td>{$inActiveMember.end_date|crmDate}</td>
	        <td>{$inActiveMember.status}</td>
	        <td>{$inActiveMember.source}</td>
	        <td>{$inActiveMember.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

    </div>
</div>
{/if}

{if $membershipTypes}
<div id="memberships">
    <div><label>{ts}Membership Types{/ts}</label></div>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Name{/ts}</th>
            <th>{ts}Period{/ts}</th>
	    <th>{ts}Fixed Start{/ts}</th>		
            <th>{ts}Minimum Fee{/ts}</th>
            <th>{ts}Duration{/ts}</th>            
            <th>{ts}Visibility{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$membershipTypes item=membershipType}
        <tr class="{cycle values="odd-row,even-row"} {$membershipType.class}">
	        <td>{$membershipType.name}</td>
		<td>{$membershipType.period_type}</td>
		<td>{$membershipType.fixed_period_start_day}</td>
	        <td>{$membershipType.minimum_fee}</td>
	        <td>{$membershipType.duration_unit}</td>	        
	        <td>{$membershipType.visibility}</td>
	        <td>{$membershipType.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

    </div>
</div>
{/if}

{if NOT ($activeMembers or $inActiveMembers) and $action ne 2 and $action ne 1 and $action ne 8 and $action ne 4 and $action ne 32768}
   <div class="messages status">
       <dl>
       <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
       <dd>
            {if $permission EQ 'edit'}
                {ts 1=$newURL}There are no memberships recorded for this contact. You can <a href="%1">enter one now</a>.{/ts}
            {else}
                {ts}There are no memberships recorded for this contact.{/ts}
            {/if}
       </dd>
       </dl>
  </div>
{/if}
</div>