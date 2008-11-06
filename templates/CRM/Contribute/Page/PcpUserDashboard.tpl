<div class="view-content">

{if $pcpInfo}
<div id="ltype">
{strip}

<table class="selector">
	<tr class="columnheader">
		<th>{ts}Title{/ts}</th>
		<th>{ts}Contribution Page{/ts}</th>
		<th>{ts}Active From{/ts}</th>
		<th>{ts}Active Until{/ts}</th>
		<th>{ts}Status{/ts}</th>
		<th>{ts}Action{/ts}</th>
	</tr>

	{foreach from=$pcpInfo item=row}
	<tr class="{cycle values="odd-row,even-row"}">
		{ if $row.pcpStatusId eq 2}
		<td class="bold"><a href="{crmURL p='civicrm/contribute/pcp/info' q="reset=1&id=`$row.pcpId`"}">{$row.pcpTitle}</a></td>
		{else}
		<td class="bold">{$row.pcpTitle}</strong></td>
		{/if}
		<td>{$row.pageTitle}</td>
		<td>{$row.start_date|truncate:10:''|crmDate}</td>
		<td>{$row.end_date|truncate:10:''|crmDate}</td>
		<td>{$row.pcpStatus}</td>
		<td class="nowrap">{$row.action}</td>
	</tr>
	{/foreach}
</table>
{/strip}
</div>
{else}
<div class="messages status">
<dl>
	<dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
	<dd>{ts}You do not own any Personal Campaign Pages.{/ts}</dd>
</dl>
</div>
{/if}


{if $pcpBlock} 
{strip}

<div class="label">{ts}Contribution Pages with Personal Campaign Pages enabled{/ts}</div>
<table class="selector">
	<tr class="columnheader">
		<th>{ts}Contribution Page{/ts}</th>
		<th>{ts}Start Date{/ts}</th>
		<th>{ts}End Date{/ts}</th>
		<th>{ts}Action{/ts}</th>
	</tr>

	{foreach from=$pcpBlock item=row}
	<tr class="{cycle values="odd-row,even-row"}">
		<td>{$row.pageTitle}</td>
		<td>{$row.start_date|truncate:10:''|crmDate}</td>
		<td>{$row.end_date|truncate:10:''|crmDate}</td>
		<td class="nowrap">{$row.action}</td>
	</tr>
	{/foreach}
</table>
{/strip} 
{/if} 

</div>
