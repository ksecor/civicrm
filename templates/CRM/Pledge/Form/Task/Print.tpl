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
    <th>{ts}Create Date{/ts}</th>
    <th>{ts}To Be Paid{/ts}</th>
    <th>{ts}Begining Date{/ts}</th>
    <th>{ts}Status{/ts}</th>
  </tr>
{foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.sort_name}</td>
        <td>{$row.pledge_amount|crmMoney}</td>	
        <td>{$row.pledge_create_date|truncate:10:''|crmDate}</td>
        <td>{$row.pledge_frequency_interval} {$row.pledge_frequency_unit|capitalize:true}(s) </td>	
        <td>{$row.pledge_start_date|truncate:10:''|crmDate}</td>
        <td>{$row.pledge_status_id}</td>	
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
