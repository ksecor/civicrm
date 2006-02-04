{include file="CRM/Contribute/Form/ContributionPage/Premium.tpl"}
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
            <th>{ts}Weight{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.name}</td>	
	        <td>{$row.sku}</td>
                <td>{$row.price }</td>
	        <td>{$row.min_contribution}</td>
	        <td>{$row.weight}</td>
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
	<div class="action-link">
    	<a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=$id&subPage=AddProductToPage"}">&raquo; {ts}Add Product to this Contribution Page{/ts}</a>
        </div>
</div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=$id&subPage=AddProductToPage"}{/capture}
        <dd>{ts 1=$crmURL}There are no premiums linked to this contribution page yet. You can <a href="%1"> add one </a>.{/ts}</dd>
        </dl>
    </div>  
   
{/if}
