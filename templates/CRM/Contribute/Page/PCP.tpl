<div id="help">
<p>{ts}This screen shows all the Personal Campaign Pages created in the system and allows administrator to review them and change their status.{/ts}</p>
</div>
{if $action ne 8} 
{include file="CRM/Contribute/Form/PCP/PCP.tpl"} 
{else}
{include file="CRM/Contribute/Form/PCP/Delete.tpl"} 
{/if} 
{if $rows}
<div id="ltype">
<p></p>
{include file="CRM/common/pager.tpl" location="top"}
{include file="CRM/common/pagerAToZ.tpl}
<div class="form-item">
{strip}
<table cellpadding="0" cellspacing="0" border="0">
	<tr class="columnheader">
		<th>{ts}Page Title{/ts}</th>
		<th>{ts}Supporter{/ts}</th>
		<th>{ts}Contribution Page{/ts}</th>
		<th>{ts}Page Active From{/ts}</th>
		<th>{ts}Page Active Until{/ts}</th>
		<th>{ts}Status{/ts}</th>
		<th></th>
	</tr>
	{foreach from=$rows item=row}
	<tr class="{cycle values="odd-row,even-row"} {$row.class}">
		<td><a href="{crmURL p='civicrm/contribute/pcp/info' q="reset=1&id=`$row.id` "}" title="{ts}View Personal Campaign Page{/ts}">{$row.title}</a></td>
		<td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.supporter_id`"}" title="{ts}View contact record{/ts}">{$row.supporter}</a></td>
		<td>{$row.contribution_page_id}</td>
		<td>{$row.start_date|truncate:10:''|crmDate}</td>
		<td>{$row.end_date|truncate:10:''|crmDate}</td>
		<td>{$row.status_id}</td>
		<td>{$row.action}</td>
	</tr>
	{/foreach}
</table>
{/strip}
</div>
</div>
{else}
<div class="messages status">
<dl>
	<dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
	<dd>{ts}There are no Personal Campaign Pages configured in the system.{/ts}</dd>
</dl>
</div>
{/if}
