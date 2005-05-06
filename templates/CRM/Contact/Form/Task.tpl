Number of selected contacts: {$totalSelectedContacts}

{if $rows } 
<div class="form-item">
<table width="30%">
  <tr class="columnheader">
    <th>Name</th>
  </tr>
{foreach from=$rows item=row}
<tr class="{cycle values="odd-row,even-row"}">
<td>{$row.displayName}</td>
</tr>
{/foreach}
</table>
</div>
{/if}
