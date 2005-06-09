{if $rows}

<form name="activity_pager" action="/drupal/civicrm/contact/view/activity" method="post">

{include file="CRM/pager.tpl" location="top"}

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
    <td>{$row.activity_type}</td>
    <td>{$row.activity_summary|mb_truncate:33:"...":true}</td>
    <td>{$row.activity_date|crmDate}</td>
    <td>{$row.action}</td>
  </tr>
  {/foreach}
</table>
{/strip}

{include file="CRM/pager.tpl" location="bottom"}
</form>
{else}
<div class="message status">
<dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
    <dd>Currently there are no Activities for this Contact.</dd>
</dl>
</div>
{/if}
