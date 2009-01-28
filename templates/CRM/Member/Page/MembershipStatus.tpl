{capture assign=crmURL}{crmURL p='civicrm/admin/member/membershipStatus' q="action=add&reset=1"}{/capture}
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Member/Form/MembershipStatus.tpl"}
{else}
    <div id="help">
        <p>{ts}CiviMember automatically calculates the current status of each contact's membership based on the status names and rules configured here. The status 'rule' tells CiviMember what status to assign based on the start and end dates of a given membership. For example, the default <strong>Grace</strong> status rule says: 'assign Grace status if the membership period ended sometime within the past month.'{/ts} {docURL page="CiviMember Admin"}
        <p>{ts 1=$crmURL}The status rules provided by default may be sufficient for your organization. However, you can easily change the status names and/or adjust the rules by clicking the Edit links below. Or you can <a href='%1'>add a new status and rule</a>.{/ts}
    </div>
{/if}

{if $rows}
<div id="ltype">
<p></p>
    <div class="form-item" id=membership_status_id>
        {strip}
        <table cellpadding="0" cellspacing="0" border="0">
        <tr class="columnheader">
            <th>{ts}Status{/ts}</th>
            <th>{ts}Start Event{/ts}</th>
            <th>{ts}End Event{/ts}</th>
            <th>{ts}Member{/ts}</th>
            <th>{ts}Admin{/ts}</th>
	        <th>{ts}Weight{/ts}</th>
	        <th>{ts}Enabled?{/ts}</th>
	        <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.name}</td>	
	        <td>{$row.start_event}</td>
	        <td>{$row.end_event}</td>
	        <td>{if $row.is_current_member eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{if $row.is_admin eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td class="nowrap">{$row.weight}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{$row.action}</td>
          </tr>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}" id="newMembershipStatus" class="button"><span>&raquo; {ts}New Membership Status{/ts}</span></a>
        </div>
        {/if}
    </div>
</div>
{else}
  {if $action ne 1}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts 1=$crmURL}There are no custom membership status entered. You can <a href='%1'>add one</a>.{/ts}</dd>
        </dl>
    </div>    
  {/if}
{/if}
