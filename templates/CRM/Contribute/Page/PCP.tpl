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
{include file="CRM/common/jsortable.tpl}
{strip}
<table id="options" class="display">
	<thead>
    <tr>
		<th>{ts}Page Title{/ts}</th>
		<th>{ts}Supporter{/ts}</th>
		<th id="sortable">{ts}Contribution Page{/ts}</th>
		<th id="start_date">{ts}Starts{/ts}</th>
		<th id="end_date">{ts}Ends{/ts}</th>
		<th>{ts}Status{/ts}</th>
		<th></th>
    </tr>
	</thead>
	<tbody>
	{foreach from=$rows item=row}
	<tr class="{cycle values="odd-row,even-row"} {$row.class}">
		<td><a href="{crmURL p='civicrm/contribute/pcp/info' q="reset=1&id=`$row.id` "}" title="{ts}View Personal Campaign Page{/ts}">{$row.title}</a></td>
		<td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.supporter_id`"}" title="{ts}View contact record{/ts}">{$row.supporter}</a></td>
		<td><a href="{crmURL p='civicrm/contribute/transact' q="id=`$row.contribution_page_id`&reset=1"}" title="{ts}View contribution page{/ts}">{$row.contribution_page_title}</td>
		<td>{$row.start_date|crmDate:"%b %d, %Y %l:%M %P"}</td>
		<td>{if $row.end_date}{$row.end_date|crmDate:"%b %d, %Y %l:%M %P"}{else}({ts}ongoing{/ts}){/if}</td>
		<td>{$row.status_id}</td>
		<td id={$row.id}>{$row.action|replace:'xx':$row.id}</td>
		<td class="start_date hiddenElement">{$row.start_date|truncate:10:''|crmDate}</td>
		<td class="end_date hiddenElement">{if $row.end_date}{$row.end_date|truncate:10:''|crmDate}{else}({ts}ongoing{/ts}){/if}</td>
	</tr>
	{/foreach}
	</tbody>
</table>
{/strip}
</div>
{else}
<div class="messages status">
<dl>
	<dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    {if $isSearch}
        <dd>{ts}There are no Personal Campaign Pages which match your search criteria.{/ts}</dd>
    {else}
	<dd>{ts}There are currently no Personal Campaign Pages.{/ts}</dd>
    {/if}
</dl>
</div>
{/if}