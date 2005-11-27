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
    <th>{ts}Contribution Type{/ts}</th>
    <th>{ts}Receive Date{/ts}</th>
    <th>{ts}Thank you Date{/ts}</th>
    <th>{ts}Cancel date{/ts}</th>
    <th>{ts}Source{/ts}</th>
  </tr>
{foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.sort_name}</td>
        <td>{$row.total_amount}</td> 
        <td>{$row.contribution_type}</td>  
        <td>{$row.contribution_source}</td> 
        <td>{$row.receive_date}</td> 
        <td>{$row.thankyou_date}</td> 
        <td>{$row.cancel_date}</td>
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
