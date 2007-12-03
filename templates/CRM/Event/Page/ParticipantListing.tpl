{if $rows}
{include file="CRM/common/pager.tpl" location="top"}
   <table enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
   <thead>  
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
   </thead>
   <tbody>  
   {foreach from=$rows item=row}
     <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.name}</td>	
{if $participantListingType eq 2}
        <td>{$row.email}</td>
{/if}
     </tr>
   {/foreach}
   </tbody>
   </table>
{include file="CRM/common/pager.tpl" location="bottom"}
{/if}