<a accesskey="N" href="{$newItemURL}" id="newAddItem" class="button"><span>&raquo; {ts}New Item{/ts}</span></a>
<a accesskey="P" href="{$previewItemURL}" id="previewItem" class="button"><span>&raquo; {ts}Preview Items{/ts}</span></a>
<br/><br/>

{include file="CRM/Auction/Form/SearchItem.tpl"}

{if $rows}
    <div id=item_status_id>
        {strip}
        {include file="CRM/common/pager.tpl" location="top"}
        {include file="CRM/common/pagerAToZ.tpl}    
        <table class="selector">
         <tr class="columnheader">
            <th>{ts}Donor{/ts}</th>
            <th>{ts}Item{/ts}</th>
            <th>{ts}Description{/ts}</th>
            <th>{ts}Auction Type{/ts}</th>
            <th>{ts}Quantity{/ts}</th>
            <th>{ts}Retail Value{/ts}</th>
            <th>{ts}Buy Now Value{/ts}</th>
            <th>{ts}Min Bid Value{/ts}</th>
            <th>{ts}Min Bid Increment{/ts}</th>
            <th>{ts}Approved?{/ts}</th>
	    <th></th>
         </tr>
        {foreach from=$rows item=row}
          <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.donorName}</td>
            <td>{$row.title}&nbsp;&nbsp;({ts}ID:{/ts} {$row.id})</td>
            <td>{$row.description}</td>
            <td>{$row.auction_type}</td>
            <td>{$row.quantity}</td>
            <td>{$row.retail_value}</td>
            <td>{$row.buy_now_value}</td>
            <td>{$row.min_bid_value}</td>
            <td>{$row.min_bid_increment}</td>
	    <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	    <td>{$row.action}</td>
          </tr>
        {/foreach}    
        </table>
        {include file="CRM/common/pager.tpl" location="bottom"}
        {/strip}
      
    </div>
{else}
   {if $isSearch eq 1}
    <div class="status messages">
        <dl>
            <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
            {capture assign=browseURL}{crmURL p='civicrm/auction/manage' q="reset=1"}{/capture}
            <dd>
                {ts}No available Auctions match your search criteria. Suggestions:{/ts}
                <div class="spacer"></div>
                <ul>
                <li>{ts}Check your spelling.{/ts}</li>
                <li>{ts}Try a different spelling or use fewer letters.{/ts}</li>
                <li>{ts}Make sure you have enough privileges in the access control system.{/ts}</li>
                </ul>
                {ts 1=$browseURL}Or you can <a href='%1'>browse all available Current Auctions</a>.{/ts}
            </dd>
        </dl>
    </div>
   {else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
        <dd>{ts 1=$newAuctionURL}There are no auctions created yet. You can <a href='%1'>add one</a>.{/ts}</dd>
        </dl>
    </div>    
   {/if}
{/if}