{include file="CRM/Contribute/Form/ContributionPage/Premium.tpl"}
{capture assign=managePremiumsURL}{crmURL p='civicrm/admin/contribute/managePremiums' q="reset=1"}{/capture}
{if $rows}
<div id="ltype">
    <div class="description">
        <p>{ts 1=$managePremiumsURL}The premiums listed below are currently offered on this Contribution Page. If you have other premiums which are not already being offered on this page, you will see a link below to offer another premium. Use <a href="%1">Administer CiviCRM &raquo; Manage Premiums</a> to create or enable additional premium choices which can be used on any Contribution page.{/ts}</p>
    </div>
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
	        <td>{$row.product_name}</td>	
	        <td>{$row.sku}</td>
                <td>{$row.price }</td>
	        <td>{$row.min_contribution}</td>
	        <td>{$row.weight}</td>
	        <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}
    </div>
    {if $products ne null }
        <div class="action-link">
            <a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=$id&subPage=AddProductToPage"}">&raquo; {ts}Offer Another Premium on this Contribution Page{/ts}</a>
        </div>
	{/if}
</div>
{else}
    <div class="messages status">
    <dl>
	{if $products ne null }
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
          {capture assign=crmURL}{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=$id&subPage=AddProductToPage"}{/capture}
          <dd>{ts 1=$crmURL}There are no premiums offered on this contribution page yet. You can <a href="%1">add one</a>.{/ts}</dd>
	{else}
	   <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
           <dd>{ts 1=$managePremiumsURL}There are no active premiums for your site. You can <a href="%1">create and/or enable premiums here</a>.{/ts}</dd>
	
	{/if}
        </dl>
    </div>  
   
{/if}
