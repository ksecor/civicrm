{*debug*}

<div>
{include file="CRM/pager.tpl" location="top"}
</div>

<table border="0">
 <tr>
   {foreach from=$columnHeaders item=header}
   <th>
     {if $header.sort}
     {assign var='key' value=$header.sort}
   <a href={$sort->_response.$key.link}>{$header.name}</a>&nbsp;{$sort->_response.$key.direction}
   {else}
   {$header.name}
   {/if}
   </th>
 {/foreach}
 </tr>

{foreach from=$rows item=row}
<tr>
<td>{$row.contact_id}</td>
<td>{$row.sort_name}</td>
<td>{$row.email}</td>
<td>{$row.phone}</td>
<td>{$row.street_address}</td>
<td>{$row.city}</td>
<td>{$row.state}</td>
<td><a href={$row.edit}>Edit</a>&nbsp; <a href={$row.view}>View</a></td>
</tr>
{/foreach}
</table>

<div>
{include file="CRM/pager.tpl" location="bottom"}
</div>
