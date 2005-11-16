<div id="help">
    <p>{ts}CiviCRM is pre-configured with standard options for Duplicate Matching rule .{/ts}</p>
</div>

{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/DupeMatch.tpl"}
{/if}

{if $rows}
<div id="dupematch">
<p></p>
    <div class="form-item">
        {strip}
        <table>
        
	<tr class="columnheader">
            <th>{ts} Duplicate Matching Rule {/ts}</th>            
	<th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}">
	        <td>{$row.rule}</td>	
	        
	        <td>{$row.action}</td>
        </tr>
        {/foreach}
	
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=update&reset=1&advance=0"}" id="newDupeMatch">&raquo; {ts}Edit DupeMatch Rule{/ts}</a>
        </div>
        {/if}
    </div>
</div>
    
{/if}
