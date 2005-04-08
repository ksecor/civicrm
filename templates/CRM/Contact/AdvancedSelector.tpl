{include file="CRM/pager.tpl" location="top"}

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
    <td><input name="chk[{counter}]" type="checkbox" value="{$row.contact_id}"></td>
    <td>{$row.contact_type}</td>	
    <td><a href="{$row.view}">{$row.sort_name}</a></td>
    <td>{$row.street_address|truncate:22:"...":true}</td>
    <td>{$row.city}</td>
    <td>{$row.state}</td>
    <td>{$row.postal_code}</td>
    <td>{$row.country}</td>
    <td>{$row.email|truncate:17:"...":true}</td>
    <td>{$row.phone}</td>
    <td><a href="{$row.view}">View</a><br><a href="{$row.edit}">Edit</a></td>
  </tr>
  {/foreach}

</table>

{include file="CRM/pager.tpl" location="bottom"}
