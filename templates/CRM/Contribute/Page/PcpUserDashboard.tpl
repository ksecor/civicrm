<div class="view-content">
{if $pcpInfo}
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
       <td>{if $row.pcpStatus}{$row.pcpStatus}{else}{ts}No PCP{/ts}{/if}</td>
       <td class="nowrap">{$row.action}</td>
       </tr>
      {/foreach}
  </table>
  {/strip}
{else}
   <div class="messages status">
       <dl>
       <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
       <dd>
         {ts}There are no Personal Campaign Page for you.{/ts}
       </dd>
       </dl>
  </div>
{/if}
</div>
