{if $action eq 1 or $action eq 2 or $action eq 4}
    {include file="CRM/Price/Form/Field.tpl"}
{elseif $action eq 8 and !$usedBy}
    {include file="CRM/Price/Form/DeleteField.tpl"}
{elseif $action eq 1024 }
    {include file="CRM/Price/Form/Preview.tpl"}
{else}

 {if $usedBy}
    <div class='spacer'></div>
    <div id="price_set_used_by" class="messages status">
      <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>      
      <dd>
        {if $action eq 8}
            {ts 1=$usedPriceSetTitle}Unable to delete the '%1' Price Field - it is currently in use by one or more active events.{/ts}
       	{/if}
        {ts}If you no longer want to use this Price Field, click the event title below, and modify the fees for that event.{/ts}<br />
        
        {if $usedBy.civicrm_event_page} {* If and when Price Sets and Price Fields are used by entities other than events, add condition here and change text above. *}
            <table class="report">
            <tr class="columnheader-dark">
                <th scope="col">{ts}Event{/ts}</th>
                <th scope="col">{ts}Type{/ts}</th>
                <th scope="col">{ts}Public{/ts}</th>
                <th scope="col">{ts}Date(s){/ts}</th>
            </tr>

            {foreach from=$usedBy.civicrm_event_page item=event key=id}
                <tr>
                    <td><a href="{crmURL p="civicrm/admin/event" q="action=update&reset=1&subPage=Fee&id=`$id`"}">{$event.title}</a></td>
                    <td>{$event.eventType}</td>
                    <td>{if $event.isPublic}{ts}Yes{/ts}{else}{ts}No{/ts}{/if}</td>
                    <td>{$event.startDate}{if $event.endDate}&nbsp;to&nbsp;{$event.endDate}{/if}</td>
                </tr>
            {/foreach}
            </table>
        {/if}
      </dd>
      </dl>
    </div>
    {/if}



    {if $priceField}
    
    <div id="field_page">
     <p></p>
        {strip}
         <table class="selector">
         <tr class="columnheader">
            <th>{ts}Field Label{/ts}</th>
            <th>{ts}Field Type{/ts}</th>
            <th>{ts}Order{/ts}</th>
            <th>{ts}Req?{/ts}</th>
            <th>{ts}Status?{/ts}</th>
{*
            <th>{ts}Active On{/ts}</th>
            <th>{ts}Expire On{/ts}</th>
*}
            <th>{ts}Price{/ts}</th>
            <th>&nbsp;</th>
        </tr>
        {foreach from=$priceField key=fid item=row}
        <tr class="{cycle values="odd-row,even-row"} {if NOT $row.is_active} disabled{/if}">
            <td>{$row.label}</td>
            <td>{$row.html_type}</td>
            <td class="nowrap">{$row.weight}</td>
            <td>{if $row.is_required eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{if $row.is_active eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
{*
            <td>{if $row.active_on}{$row.active_on|date_format:"%Y-%m-%d"}{/if}</td>
            <td>{if $row.expire_on}{$row.expire_on|date_format:"%Y-%m-%d"}{/if}</td>
*}
            <td>{if $row.html_type eq "Text"}{$row.price|crmMoney}{else}<a href="{crmURL p="civicrm/admin/price/field/option" q="action=browse&reset=1&sid=$sid&fid=$fid"}">{ts}Edit Price Options{/ts}</a>{/if}</td>
            <td class="btn-slide" id={$row.id}>{$row.action|replace:'xx':$row.id}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}
        
        <div class="action-link">
            <a href="{crmURL q="reset=1&action=add&sid=$sid"}" id="newPriceField" class="button"><span>&raquo; {ts}New Price Field{/ts}</span></a>
        </div>
     </div>

    {else}
        {if $action eq 16}
        <div class="messages status">
        <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/price/field q="action=add&reset=1&sid=$sid"}{/capture}
        <dd>{ts 1=$groupTitle 2=$crmURL}There are no fields for price set '%1', <a href='%2'>add one</a>.{/ts}</dd>
        </dl>
        </div>
        {/if}
    {/if}
{/if}
