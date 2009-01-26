{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Member/Form/MembershipType.tpl"}
{else}
    <div id="help">
        <p>{ts}Membership types are used to categorize memberships. You can define an unlimited number of types. Each type incorporates a 'name' (Gold Member, Honor Society Member...), a description, a minimum fee (can be $0), and a duration (can be 'lifetime'). Each member type is specifically linked to the membership entity (organization) - e.g. Bay Area Chapter.{/ts} {docURL page="CiviMember Admin"}</p>
    </div>
{/if}

{if $rows}
<div id="membership_type">
    {strip}
	<table class="selector">
        <tr class="columnheader">
            <th>{ts}Membership{/ts}</th>
            <th>{ts}Period{/ts}</th>
            <th>{ts}Fixed Start{/ts}</th>
            <th>{ts}Minimum Fee{/ts}</th>
            <th>{ts}Duration{/ts}</th>
            <th>{ts}Visibility{/ts}</th>
	        <th>{ts}Order{/ts}</th>
 	        <th>{ts}Enabled?{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
           <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.name}</td>	
	        <td>{$row.period_type}</td>
	        <td>{$row.fixed_period_start_day}</td>
	        <td>{$row.minimum_fee}</td>
	        <td>{$row.duration_interval} {$row.duration_unit}</td>
	        <td>{$row.visibility}</td>
	        <td class="nowrap">{$row.weight}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{$row.action}</td>
           </tr>
        {/foreach}
    </table>
    {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}" id="newMembershipType" class="button"><span>&raquo; {ts}New Membership Type{/ts}</span></a>
        </div>
        {/if}
</div>
{else}
  {if $action ne 1}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/member/membershipType' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no membership types entered. You can <a href='%1'>add one</a>.{/ts}</dd>
        </dl>
    </div>    
  {/if}
{/if}
