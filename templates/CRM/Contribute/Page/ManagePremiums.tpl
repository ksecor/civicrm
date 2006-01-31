{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Contribute/Form/ManagePremiums.tpl"}
{else}
    <div id="help">
        <p>{ts} need to wirte description...    {/ts}</p>
    </div>
{/if}

{if $rows}
<div id="ltype">
<p></p>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Name{/ts}</th>
            <th>{ts}SKU{/ts}</th>
            <th>{ts}Market Value{/ts}</th>
            <th>{ts}Min Contribution{/ts}</th>
            <th>{ts}Is Active{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.name}</td>	
	        <td>{$row.sku}</td>
                <td>{$row.price }</td>
	        <td>{$row.min_contribution}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}
        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}">&raquo; {ts}New Premium{/ts}</a>
        </div>
        {/if}
    </div>
</div>
{else}
    {if $action ne 1 and $action ne 2}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/contribute/managePremiums' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}No premium products have been created for your site. You can<a href="%1"> add one </a>.{/ts}</dd>
        </dl>
    </div>  
    {/if}	  
{/if}
