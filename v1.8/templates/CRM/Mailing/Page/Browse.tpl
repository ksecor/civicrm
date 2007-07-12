{if $action eq 8 or $action eq 64} 
<fieldset><legend>{if $action eq 8}{ts}Delete{/ts}{else}{ts}Cancel{/ts}{/if} {ts} Mailing{/ts}</legend>
<div class=status>{ts 1=$subject}Are you sure you want to {if $action eq 8}{ts}delete{/ts}{else}{ts}cancel{/ts}{/if} the mailing "%1"?{/ts}</div>
<dl><dt></dt><dd>{$form.buttons.html}</dd></dl>
</fieldset>
{/if}

{if $rows}
{include file="CRM/common/pager.tpl" location="top"}

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
  {foreach from=$row item=value}
    <td>{$value}</td>
  {/foreach}
  </tr>
  {/foreach}
</table>
{/strip}

{include file="CRM/common/pager.tpl" location="bottom"}

<div class="action-link">
    <a href="{crmURL p='civicrm/mailing/send' q='reset=1'}">&raquo; {ts}New Mailing{/ts}</a>
</div>

{else}
<div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
        {capture assign=crmURL}{crmURL p='civicrm/mailing/send' q='reset=1'}{/capture}
        <dd>{ts 1=$crmURL}There are no sent mails. You can <a href="%1">send one</a>.{/ts}</dd>
    </dl>
   </div>
{/if}
