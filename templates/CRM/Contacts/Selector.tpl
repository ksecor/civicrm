<table>
<tr>
{foreach from=$columnHeaders item=header}
<th>{$header.label}</th>
{/foreach}
</tr>
{foreach from=$rows item=row}
<tr>
<td>{$row.first_name}</td><td>{$row.last_name}</td><td>{$row.email}</td>
</tr>
{/foreach}
</table>