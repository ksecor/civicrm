{if $rows}
{include file="CRM/common/pager.tpl" location="top"}
   <table cellpadding="0" cellspacing="0" border="0">
     <tr class="columnheader">
    {foreach from=$headers item=header}
    <th scope="col">
    {if $header.sort}
      {assign var='key' value=$header.sort}
      {$sort->_response.$key.link}
    {else}
      {$header.name}
    {/if}
    </th>
  {/foreach}
     </tr>
  {foreach from=$rows item=row}
     <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.name}</td>	
        {if $participantListingType eq 2}
        <td>{$row.email}</td>
        {/if}
     </tr>
  {/foreach}
  </table>
{include file="CRM/common/pager.tpl" location="bottom"}
{/if}