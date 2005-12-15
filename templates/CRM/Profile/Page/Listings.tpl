{* make sure there are some fields in the selector *}
{ if empty( $columnHeaders ) }

{include file="CRM/Profile/Form/Search.tpl"}

{include file="CRM/pager.tpl" location="top"}

{* show profile listings criteria *}
{if $criteria}
 <p>
 <div id="search-status">
    {ts}Displaying contacts where:{/ts}
    <ul>
    {foreach from=$criteria item=item}
      <li>{$item}</li>
    {/foreach}
    </ul>
 </div>
 </p>
{/if}

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
  <tr id='rowid_{$row.contact_id}' class="{cycle values="odd-row,even-row"}">
  {foreach from=$row item=value}
    <td>{$value}</td>
  {/foreach}
  </tr>
  {/foreach}
</table>
{/strip}

{include file="CRM/pager.tpl" location="bottom"}

{else}
<div class="messages status">
{ts}No fields have been selected to display in the listings.{/ts}
</div>
{/if}