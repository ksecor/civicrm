<div class="view-content">
{strip}

  <table class="selector">
    <tr class="columnheader">
      <th>{ts}Title{/ts}</th>
      <th>{ts}Active From{/ts}</th>
      <th>{ts}Active Until{/ts}</th>
      <th>{ts}Status{/ts}</th>
      <th>{ts}Action{/ts}</th>
    </tr>

     {foreach from=$pcpInfo item=row}
       <tr class="{cycle values="odd-row,even-row"}">
       <td>{if $row.pcpTitle}{$row.pcpTitle}{else}{$row.pageTitle} {ts}( Till not configured ){/ts}{/if}</td>
       <td>{$row.start_date}</td>
       <td>{$row.end_date|truncate:10:''|crmDate}</td>
       <td>{$row.pcpStatus}</td>
       <td>{$row.id}</td>	
       </tr>
      {/foreach}
  </table>
  {/strip}
</div>
