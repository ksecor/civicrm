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
    <th>{ts}Amount{/ts}</th>
    <th>{ts}Type{/ts}</th>
    <th>{ts}Source{/ts}</th>
    <th>{ts}Received{/ts}</th>
    <th>{ts}Thank-you Sent{/ts}</th>
    <th>{ts}Cancelled{/ts}</th>
  </tr>
{foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.sort_name}</td>
        <td class="right bold" nowrap>{$row.total_amount|crmMoney}</td>
        <td>{$row.contribution_type}</td>  
        <td>{$row.source}</td> 
        <td>{$row.receive_date|truncate:10:''|crmDate}</td>
        <td>{$row.thankyou_date|truncate:10:''|crmDate}</td>
        <td>{$row.cancel_date|truncate:10:''|crmDate}</td>
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
