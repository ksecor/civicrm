{if $action eq 1 or $action eq 2 or $action eq 4}
    {include file="CRM/Price/Form/Set.tpl"}
{elseif $action eq 1024}
    {include file="CRM/Price/Form/Preview.tpl"}
{elseif $action eq 8}
    {include file="CRM/Price/Form/DeleteSet.tpl"}
{else}
    <div id="help">
    {ts}Price Sets allow you to have optional items with associated prices.  Use this if having a single set of fee levels is not sufficient for the event.{/ts}
    </div>

    {if $rows}
    <div id="price_set">
    <p></p>
        <div class="form-item">
        {strip}
        <table dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
        <thead>
        <tr class="columnheader">
            <th field="Set Title"  dataType="String">{ts}Set Title{/ts}</th>
            <th field="Status"       dataType="String">{ts}Status?{/ts}</th>
            <th field="In Use"       dataType="String">{ts}In Use{/ts}</th>
            <th datatype="html"></th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.title}</td>
            <td>{if $row.is_active       eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
            <td>{if $row.is_used eq true} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </tbody>
        </table>
        
        {if NOT ($action eq 1 or $action eq 2) }
        <p></p>
        <div class="action-link">
        <a href="{crmURL p='civicrm/admin/price' q="action=add&reset=1"}" id="newPriceSet">&raquo;  {ts}New Set of Price Fields{/ts}</a>
        </div>
        {/if}

        {/strip}
        </div>
    </div>
    {else}
       {if $action ne 1} {* When we are adding an item, we should not display this message *}
       <div class="messages status">
       <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/> &nbsp;
         {capture assign=crmURL}{crmURL p='civicrm/admin/price' q='action=add&reset=1'}{/capture}
         {ts 1=$crmURL}No price sets have been created yet. You can <a href="%1">add one</a>.{/ts}
       </div>
       {/if}
    {/if}
{/if}
