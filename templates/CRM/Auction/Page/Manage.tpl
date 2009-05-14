{capture assign=newAuctionURL}{crmURL p="civicrm/admin/auction/add" q="action=add&reset=1"}{/capture}
<a accesskey="N" href="{$newAuctionURL}" id="newManageAuction" class="button"><span>&raquo; {ts}New Auction{/ts}</span></a>
<br/><br/>
{include file="CRM/Auction/Form/SearchAuction.tpl"}

{if $rows}
    <div id=auction_status_id>
        {strip}
        {include file="CRM/common/pager.tpl" location="top"}
        {include file="CRM/common/pagerAToZ.tpl}    
        <table class="selector">
         <tr class="columnheader">
            <th>{ts}Auction{/ts}</th>
            <th>{ts}Public?{/ts}</th>
            <th>{ts}Starts{/ts}</th>
            <th>{ts}Ends{/ts}</th>
            <th>{ts}Active?{/ts}</th>
	    <th></th>
         </tr>
        {foreach from=$rows item=row}
          <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.title}&nbsp;&nbsp;({ts}ID:{/ts} {$row.id})</td>
            <td>{if $row.is_public eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>    
    	    <td>{$row.start_date|crmDate}</td>
            <td>{$row.end_date|crmDate}</td>
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
