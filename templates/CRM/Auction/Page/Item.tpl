{if $manageItemURL}<a accesskey="N" href="{$manageItemURL}" id="manageItem" class="button"><span>&raquo; {ts}Manage Items{/ts}</span></a><br/><br/>{/if}

{include file="CRM/Auction/Form/SearchItem.tpl"}

{if $rows}
    <div id=item_status_id>
        {strip}
        {include file="CRM/common/pager.tpl" location="top"}
        {include file="CRM/common/pagerAToZ.tpl}    
        <table class="selector">
         <tr class="columnheader">
            <th></th>
            <th>{ts}Item{/ts}</th>
            <th>{ts}Max Bid{/ts}</th>
            <th>{ts}Retail Value{/ts}</th>
            <th>{ts}Buy Now Price{/ts}</th>
         </tr>
        {foreach from=$rows item=row}
          <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td><img src="{$config->resourceBase}i/contribute/default_premium.jpg" width="70px" height="50px"/></td>
            <td>{$row.title}</td>
            <td>{$row.max_bid|crmMoney}</td>
            <td>{$row.retail_value|crmMoney}</td>
            <td>{if $row.buy_now_value}{$row.buy_now_value|crmMoney} &nbsp;&raquo; <a href="#">buy now</a>{/if}</td>
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
