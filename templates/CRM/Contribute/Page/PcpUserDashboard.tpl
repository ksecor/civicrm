<div class="view-content">

{if $pcpInfo}
<div id="ltype">
{strip}

<table class="selector">
	<tr class="columnheader">
		<th>{ts}Your Title{/ts}</th>
		<th>{ts}In Support of{/ts}</th>
		<th>{ts}Status{/ts}</th>
		<th>{ts}Action{/ts}</th>
	</tr>

	{foreach from=$pcpInfo item=row}
	<tr class="{cycle values="odd-row,even-row"}">
	       <td class="bold"><a href="{crmURL p='civicrm/contribute/pcp/info' q="reset=1&id=`$row.pcpId`" a=1}" title="{ts}Preview your Personal Campaign Page{/ts}">{$row.pcpTitle}</a></td>
	       <td>{$row.pageTitle}</td>
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
	<dd>{ts}You do not have any active Personal Campaign pages.{/ts}</dd>
</dl>
</div>
{/if}


{if $pcpBlock} 
{strip}

<div class="label">{ts}Become a supporter by creating a Personal Campaign Page{/ts}</div>
<table class="selector">
	<tr class="columnheader">
		<th>{ts}Campaign{/ts}</th>
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
