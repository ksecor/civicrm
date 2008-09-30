<div class="view-content">
{if $pcpBlock}
{strip}

  <table class="selector">
    <tr class="columnheader">
      <th>{ts}Title{/ts}</th>
      <th>{ts}Start Date{/ts}</th>
      <th>{ts}End Date{/ts}</th>
      <th>{ts}Action{/ts}</th>
    </tr>

    {foreach from=$pcpBlock item=row}
      <tr class="{cycle values="odd-row,even-row"}">
       <td>{$row.pageTitle}</td>
       <td>{$row.start_date|truncate:10:''|crmDate}</td>
       <td>{$row.end_date|truncate:10:''|crmDate}</td>
       <td class="nowrap">{$row.action}</td>
      </tr>
    {/foreach}
  </table>
{/strip}
{/if}

{if $pcpInfo}
<div id="ltype">
<p></p>
<div class="label" style=color:green >{ts}Personal Campaign Page{/ts}</div>
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
        <td class="bold"><a href="{crmURL p='civicrm/contribute/campaign/info' q="reset=1&id=`$row.pcpId`"}">{$row.pcpTitle}</a></td>
        <td>{$row.start_date|truncate:10:''|crmDate}</td>
        <td>{$row.end_date|truncate:10:''|crmDate}</td>
        <td>{$row.pcpStatus}</td>
        <td class="nowrap">{$row.action}</td>
       </tr>
    {/foreach}
  </table>
{/strip}
</div>
{else}
   <div class="messages status">
      <dl>
       <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
       <dd>{ts}There are no Personal Campaign Page for you.{/ts}
       </dd>
       </dl>
  </div>
{/if}
</div>
