{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update or view *}              
    {include file="CRM/Member/Form/Membership.tpl"}
{elseif $action eq 4}
    {include file="CRM/Member/Form/MembershipView.tpl"}
{else}
{capture assign=newContribURL}{crmURL p="civicrm/contact/view/membership" q="reset=1&action=add&cid=`$contactId`&context=membership"}{/capture}
<div id="help">
<p>{ts 1=$newContribURL}This page lists all memberships received from {$display_name} since inception.
Click <a href="%1">New Membership</a> to record a new offline membership for this contact.{/ts}.
</div>

{if $rows}
<div id="ltype">
<p></p>
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
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}">
	        <td>{$row.membership_type_id}</td>
	        <td>{$row.start_date}</td>
	        <td>{$row.end_date}</td>
	        <td>{$row.calculated_status_id}</td>
	        <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}" id="newMembershipType">&raquo; {ts}New Membership Type{/ts}</a>
        </div>
        {/if}
    </div>
</div>
{else}
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

{/if}
