<div class="view-content">
<div id="help">
    {if ($activeMembers or $inActiveMembers) }
        {ts}Current and inactive memberships for {$displayName} are listed below.{/ts}
    {/if}

</div>

{if $activeMembers}
<div id="memberships">
<p></p>
    <div><label>{ts}Active Memberships{/ts}</label></div>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Membership{/ts}</th>
            <th>{ts}Start Date{/ts}</th>
            <th>{ts}End Date{/ts}</th>
            <th>{ts}Status{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$activeMembers item=activeMember}
        <tr class="{cycle values="odd-row,even-row"} {$activeMember.class}">
	        <td>{$activeMember.membership_type}</td>
	        <td>{$activeMember.start_date|crmDate}</td>
	        <td>{$activeMember.end_date|crmDate}</td>
	        <td>{$activeMember.status}</td>
	        <td>{$activeMember.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

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
            <th>{ts}Membership{/ts}</th>
            <th>{ts}Start Date{/ts}</th>
            <th>{ts}End Date{/ts}</th>
            <th>{ts}Status{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$inActiveMembers item=inActiveMember}
        <tr class="{cycle values="odd-row,even-row"} {$inActiveMember.class}">
	        <td>{$inActiveMember.membership_type}</td>
	        <td>{$inActiveMember.start_date|crmDate}</td>
	        <td>{$inActiveMember.end_date|crmDate}</td>
	        <td>{$inActiveMember.status}</td>
	        <td>{$inActiveMember.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

    </div>
</div>
{/if}

{if NOT ($activeMembers or $inActiveMembers)}
   <div class="messages status">
       <dl>
       <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
       <dd>
            {ts}You are not currently recorded for any memberships.{/ts}
       </dd>
       </dl>
  </div>
{/if}
</div>