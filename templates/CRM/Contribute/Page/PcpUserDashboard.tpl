<div class="view-content">

{if $pcpInfo}
<div id="ltype">
{strip}

<table class="selector">
	<tr class="columnheader">
		<th>{ts}Your Page{/ts}</th>
		<th>{ts}In Support of{/ts}</th>
		<th>{ts}Campaign Ends{/ts}</th>
		<th>{ts}Status{/ts}</th>
		<th></th>
	</tr>

	{foreach from=$pcpInfo item=row}
	<tr class="{cycle values="odd-row,even-row"} {$row.class}">
        <td class="bold"><a href="{crmURL p='civicrm/contribute/pcp/info' q="reset=1&id=`$row.pcpId`" a=1}" title="{ts}Preview your Personal Campaign Page{/ts}">{$row.pcpTitle}</a></td>
        <td>{$row.pageTitle}</td>
        <td>{if $row.end_date}{$row.end_date|truncate:10:''|crmDate}{else}({ts}ongoing{/ts}){/if}</td>
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
{if $pcpInfo} {* Change layout and text if they already have a PCP. *}
    <br />
    <div class="float-right" style="width: 65%">
    <div>{ts}Create a Personal Campaign Page for another campaign:{/ts}</div>
{else}
    <div style="width: 65%">
    <div class="label">{ts}Become a supporter by creating a Personal Campaign Page:{/ts}</div>
{/if}
<table class="selector">
	<tr class="columnheader">
		<th>{ts}Campaign{/ts}</th>
		<th>{ts}Ends{/ts}</th>
		<th></th>
	</tr>

	{foreach from=$pcpBlock item=row}
	<tr class="{cycle values="odd-row,even-row"}">
		<td><a href="{crmURL p='civicrm/contribute/transact' q="id=`$row.pageId`&reset=1"}" title="{ts}View campaign page{/ts}">{$row.pageTitle}</a></td>
        <td>{if $row.end_date}{$row.end_date|truncate:10:''|crmDate}{else}({ts}ongoing{/ts}){/if}</td>
		<td class="nowrap">{$row.action}</td>
	</tr>
	{/foreach}
</table>
{/strip}
</div> 
{/if} 

</div>
