<form action="index.php?q=crm/contact/list" method="post" name="list" id="list">
<table border=1 cellpadding=2 cellspacing=2 id = "datagrid" width = "100%" border-color="#000000">
<tr>
<td colspan=5>{include file="CRM/pager.tpl" location="top"}</td>
</tr>
<tr>
{foreach from=$columnHeaders item=header}
<td align="center">
{if $header.sort}
{assign var='key' value=$header.sort}
<a href={$sort.$key.link}>{$header.name}</a>&nbsp;{$sort.$key.direction}
{else}
{$header.name}
{/if}
</td>
{/foreach}
</tr>

{foreach from=$rows item=row}
<tr>
<td align="center">{$row.contact_id}</td>
<td align="center">{$row.domain_id}</td>
<td>{$row.sort_name}</td>
<td align="center">{$row.contact_type}</td>
<td align="center">{$row.preferred_communication_method}</td>
</tr>
{/foreach}
<tr>
</tr>
<tr>
<td colspan=5>{include file="CRM/pager.tpl" location="top"}</td>
</tr>
</table>
</form>
