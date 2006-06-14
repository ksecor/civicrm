{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update or delete *}              
    {include file="CRM/Member/Form/Membership.tpl"}
{else}
{capture assign=newContribURL}{crmURL p="civicrm/contact/view/membership" q="reset=1&action=add&cid=`$contactId`&context=membership"}{/capture}
<div id="help">
<p>{ts 1=$newContribURL}This page lists all memberships received from {$display_name} since inception.
Click <a href="%1">New Membership</a> to record a new offline membership for this contact.{/ts}.
</div>
{/if}

{if $activeMembers}
<div id="ltype">
<p></p>
    <div><label>{ts}Active Memberships{/ts}</label></div>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Membership Type{/ts}</th>
            <th>{ts}Start Date{/ts}</th>
            <th>{ts}End Date{/ts}</th>
            <th>{ts}Status{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$activeMembers item=activeMember}
        <tr class="{cycle values="odd-row,even-row"} {$activeMember.class}">
	        <td>{$activeMember.membership_type}</td>
	        <td>{$activeMember.start_date}</td>
	        <td>{$activeMember.end_date}</td>
	        <td>{$activeMember.calculated_status}</td>
	        <td>{$activeMember.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{$newContribURL}">&raquo; {ts}New Membership Type{/ts}</a>
        </div>
        {/if}
    </div>
</div>
{/if}

{if $inActiveMembers}
<div id="ltype">
<p></p>
    <div class="label font-red">{ts}Inactive Memberships{/ts}</div>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Membership Type{/ts}</th>
            <th>{ts}Start Date{/ts}</th>
            <th>{ts}End Date{/ts}</th>
            <th>{ts}Status{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$inActiveMembers item=inActiveMember}
        <tr class="{cycle values="odd-row,even-row"} {$inActiveMember.class}">
	        <td>{$inActiveMember.membership_type}</td>
	        <td>{$inActiveMember.start_date}</td>
	        <td>{$inActiveMember.end_date}</td>
	        <td>{$inActiveMember.calculated_status}</td>
	        <td>{$inActiveMember.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

    </div>
</div>
{/if}

{if NOT ($activeMembers and $inActiveMembers) and $action ne 2}
   <div class="messages status">
       <dl>
       <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
       <dd>
            {if $permission EQ 'edit'}
                {ts 1=$newContribURL}There are no memberships recorded for this contact. You can <a href="%1">enter one now</a>.{/ts}
            {else}
                {ts}There are no memberships recorded for this contact.{/ts}
            {/if}
       </dd>
       </dl>
  </div>
{/if}
