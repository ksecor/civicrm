<p>

{if $rows } 
<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>
<div class="spacer"></div>
<br />
<p>
<table>
  <tr class="columnheader">
    <th>{ts}Client{/ts}</th>
    <th>{ts}Status{/ts}</th>
    <th>{ts}Case Type{/ts}</th>
    <th>{ts}My Role{/ts}</th>
    <th>{ts}Most Recent Activity{/ts}</th>
    <th>{ts}Next Scheduled Activity{/ts}</th>
  </tr>
{debug}
{foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.sort_name}<br /><span class="description">{ts}Case ID{/ts}: {$row.case_id}</span></td>
        <td>{$row.case_status_id}</td>
        <td>{$row.case_type_id}</td>
        <td>{if $row.case_role}{$row.case_role}{else}---{/if}</td>
        <td>{if $row.case_recent_activity_type}
    	{$row.case_recent_activity_type}<br />{$row.case_recent_activity_date|crmDate}{else}---{/if}</td>
        <td>{if $row.case_scheduled_activity_type}
    	{$row.case_scheduled_activity_type}<br />{$row.case_scheduled_activity_date|crmDate}{else}---{/if}</td>
    </tr>
{/foreach}
</table>

<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>

{else}
   <div class="messages status">
    <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        {ts}There are no records selected for Print.{/ts}
    </dd>
    </dl>
   </div>
{/if}
