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
    <th>{ts}Name{/ts}</th>
    <th>{ts}Type{/ts}</th>
    <th>{ts}Member Since{/ts}</th>
    <th>{ts}Start/Renew Date{/ts}</th>
    <th>{ts}End Date{/ts}</th>
    <th>{ts}Source{/ts}</th>
    <th>{ts}Status{/ts}</th>
  </tr>
{foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.sort_name}</td>
	<td>{$row.membership_type}</td>
        <td>{$row.join_date|truncate:10:''|crmDate}</td>
        <td>{$row.start_date|truncate:10:''|crmDate}</td>
        <td>{$row.end_date|truncate:10:''|crmDate}</td>
        <td>{$row.source}</td>
        <td>{$row.status}</td>
    </tr>
{/foreach}
</table>

<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>

{else}
   <div class="messages status">
    <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
    <dd>
        {ts}There are no records selected for Print.{/ts}
    </dd>
    </dl>
   </div>
{/if}
