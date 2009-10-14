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
<table id="options" class="display">
	<thead>
    <tr>
		<th>{ts}Page Title{/ts}</th>
		<th>{ts}Supporter{/ts}</th>
		<th>{ts}Contribution Page{/ts}</th>
		<th id="sortable">{ts}Starts{/ts}</th>
		<th>{ts}Ends{/ts}</th>
		<th>{ts}Status{/ts}</th>
		<th></th>
    </tr>
	</thead>
	{foreach from=$rows item=row}
	<tr class="{cycle values="odd-row,even-row"} {$row.class}">
		<td><a href="{crmURL p='civicrm/contribute/pcp/info' q="reset=1&id=`$row.id` "}" title="{ts}View Personal Campaign Page{/ts}">{$row.title}</a></td>
		<td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.supporter_id`"}" title="{ts}View contact record{/ts}">{$row.supporter}</a></td>
		<td><a href="{crmURL p='civicrm/contribute/transact' q="id=`$row.contribution_page_id`&reset=1"}" title="{ts}View contribution page{/ts}">{$row.contribution_page_title}</td>
		<td>{$row.start_date|crmDate:"%b %d, %Y %l:%M %P"}</td>
		<td>{if $row.end_date}{$row.end_date|crmDate:"%b %d, %Y %l:%M %P"}{else}({ts}ongoing{/ts}){/if}</td>
		<td>{$row.status_id}</td>
		<td class="btn-slide" id={$row.id}>{$row.action|replace:'xx':$row.id}</td>
		<td style="display:none;">{$row.start_date|truncate:10:''|crmDate}</td>
		<td style="display:none;">{if $row.end_date}{$row.end_date|truncate:10:''|crmDate}{else}({ts}ongoing{/ts}){/if}</td>
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
    {if $isSearch}
        <dd>{ts}There are no Personal Campaign Pages which match your search criteria.{/ts}</dd>
    {else}
	<dd>{ts}There are currently no Personal Campaign Pages.{/ts}</dd>
    {/if}
</dl>
</div>
{/if}

{literal}
<script type="text/javascript">
    cj( function( ) {
        var id = count = 0;
        cj('#options th').each(function(){ if( cj(this).attr('id') == 'sortable') { id = count; } count++; });
        cj('#options').dataTable( {
            "aaSorting": [[ id, "asc" ],[ 5, "asc" ]],
            "bPaginate": false,
    		"bLengthChange": true,
    		"bFilter": false,
    		"bInfo": false,
    		"bAutoWidth": false,
    		"aoColumns": [
    		            null,
    		            null,
    		            null,
    		            { "sType": 'date',
                          "fnRender": function ( oObj ) { return oObj.aData[7]; }, 
                          "bUseRendered": false},
                        { "sType": 'date',
                          "fnRender": function ( oObj ) { return oObj.aData[8]; }, 
                          "bUseRendered": false},
    		            null,
            			{ "bSortable": false },
            			{ "bSortable": false },
            			{ "bSortable": false }
            		]
        } );        
    });
</script>
{/literal}