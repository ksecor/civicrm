{include file="CRM/common/pager.tpl" location="top"}

{if $rows }
{strip}
<table>
  <tr class="columnheader">
  {foreach from=$columnHeaders item=header}
    <th>
      {$header.name}
    </th>
  {/foreach}
  </tr>

  {counter start=0 skip=1 print=false} 
  {foreach from=$rows item=row}
  <tr class="{cycle values="odd-row,even-row"}">
  {foreach from=$row item=value}
    <td>{$value}</td>
  {/foreach}
  </tr>
  {/foreach}
</table>
{/strip}
{else}
   <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts}There are currently no {$title}.{/ts}</dd>
        </dl>
    </div>    
{/if}  

        <div class="action-link">
        <a href="{crmURL p='civicrm/mailing/report' q="mid=`$mailing_id`&reset=1"}" >&raquo; {ts}Back to Report{/ts}</a>
        </div>

{include file="CRM/common/pager.tpl" location="bottom"}
