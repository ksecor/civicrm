{*debug*}

{include file="CRM/pager.tpl" location="top"}

<div id="crm-container" class="form-item">
<table border="0" cellspacing="4" cellpadding="4" width="100%">
 <tr class="search-columnheader">
   {foreach from=$columnHeaders item=header}
   <td>
     {if $header.sort}
     {assign var='key' value=$header.sort}
   <a href={$sort->_response.$key.link}>{$header.name}</a>&nbsp;{$sort->_response.$key.direction}
   {else}
   {$header.name}
   {/if}
   </td>
 {/foreach}
 </tr>

{foreach from=$rows item=row}
<tr class="{cycle values="odd-row,even-row"}">
<td><input type=checkbox name=chk[{$row.contact_id}] value={$row.contact_id}></td>
<td valign="top" align="center">{$row.contact_id}</td>
<td valign="top">{$row.sort_name}</td>
<td valign="top">{$row.email}</td>
<td valign="top">{$row.phone}</td>
<td valign="top">{$row.street_address}</td>
<td valign="top">{$row.city}</td>
<td valign="top">{$row.state}</td>
<td valign="top"><a href={$row.edit}>Edit</a>&nbsp; <a href={$row.view}>View</a></td>
</tr>
{/foreach}
</table>
</div>

{include file="CRM/pager.tpl" location="bottom"}

