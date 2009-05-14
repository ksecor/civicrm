<div class="view-content">
{if $activeMembers}
<div id="memberships">
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
	        <td>{if $activeMember.renewPageId}<a href="{crmURL p='civicrm/contribute/transact' q="id=`$activeMember.renewPageId`&mid=`$activeMember.id`&reset=1"}">[ {ts}Renew Now{/ts} ]</a>{/if}</td>
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
    <div class="label font-red">{ts}Expired / Inactive Memberships{/ts}</div>
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
	        <td>{if $inActiveMember.renewPageId}<a href="{crmURL p='civicrm/contribute/transact' q="id=`$inActiveMember.renewPageId`&mid=`$inActiveMember.id`&reset=1"}">[ {ts}Renew Now{/ts} ]</a>{/if}</td>

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
            {ts}There are no memberships on record for you.{/ts}
       </dd>
       </dl>
  </div>
{/if}
</div>