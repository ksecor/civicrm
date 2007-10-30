{if $action eq 8 or $action eq 64} 
<fieldset><legend>{if $action eq 8}{ts}Delete{/ts}{else}{ts}Cancel{/ts}{/if} {ts} Mailing{/ts}</legend>
<div class=status>{ts 1=$subject}Are you sure you want to {if $action eq 8}{ts}delete{/ts}{else}{ts}cancel{/ts}{/if} the mailing "%1"?{/ts}</div>
<dl><dt></dt><dd>{$form.buttons.html}</dd></dl>
</fieldset>
{/if}

<div class="action-link">
    <a href="{crmURL p='civicrm/mailing/send' q='reset=1'}">&raquo; {ts}New Mailing{/ts}</a>
</div>

{include file="CRM/Mailing/Form/Search.tpl"}

{if $rows}
{include file="CRM/common/pager.tpl" location="top"}
{include file="CRM/common/pagerAToZ.tpl}

{strip}
<table>
  <tr class="columnheader">
  {foreach from=$columnHeaders item=header}
    <th>
      {if $header.sort}
        {assign var='key' value=$header.sort}
        {$sort->_response.$key.link}
      {else}
        {$header.name}
      {/if}
    </th>
  {/foreach}
  </tr>

  {counter start=0 skip=1 print=false}
  {foreach from=$rows item=row}
  <tr class="{cycle values="odd-row,even-row"}">
    <td>{$row.name}</td>
    <td>{$row.status}</td>
    <td>{$row.scheduled}</td>
    <td>{$row.start}</td>
    <td>{$row.end}</td>
    <td>{$row.action}</td>
  </tr>
  {/foreach}
</table>
{/strip}

{include file="CRM/common/pager.tpl" location="bottom"}

<div class="action-link">
    <a href="{crmURL p='civicrm/mailing/send' q='reset=1'}">&raquo; {ts}New Mailing{/ts}</a>
</div>


{* No mailings to list. Check isSearch flag to see if we're in a search or not. *}
{elseif $isSearch eq 1}
    <div class="status messages">
        <dl>
            <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
            {capture assign=browseURL}{crmURL p='civicrm/mailing/browse' q="reset=1"}{/capture}
            <dd>
               {ts}No Sent Mailings match your search criteria. Suggestions:{/ts} 
                <div class="spacer"></div>
                <ul>
                <li>{ts}Check your spelling.{/ts}</li>
                <li>{ts}Try a different spelling or use fewer letters.{/ts}</li>
                </ul>
                {ts 1=$browseURL}Or you can <a href="%1">browse all Sent Mailings</a>.{/ts}
            </dd>
        </dl>
    </div>
{else}
    <div class="messages status">
        <dl>
            <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
            {capture assign=crmURL}{crmURL p='civicrm/mailing/send' q='reset=1'}{/capture}
            <dd>{ts 1=$crmURL}There are no Sent Mailings. You can <a href='%1'>create and send one</a>.{/ts}</dd>
        </dl>
   </div>
{/if}
