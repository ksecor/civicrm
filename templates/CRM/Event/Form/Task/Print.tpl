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
    <th>{ts}Event{/ts}</th>
    <th>{ts}Fee Level{/ts}</th>
    <th>{ts}Event Date{/ts}</th>
    <th>{ts}Participation Status{/ts}</th>
  </tr>
{foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.sort_name}</td>
        <td>{$row.event_title}</td>  
        <td>{$row.event_level}</td> 
        <td>{$row.start_date|truncate:10:''|crmDate}<br/>
            {$row.end_date|truncate:10:''|crmDate}
        </td>
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
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        {ts}There are no records selected for Print.{/ts}
    </dd>
    </dl>
   </div>
{/if}
