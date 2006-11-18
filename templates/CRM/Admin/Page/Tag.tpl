<div id="help">
    {ts}Tags can be assigned to any contact record, and are a convenient way to find contacts. You can create as many tags as needed to organize and segment your records.{/ts}
</div>

{if $action eq 1 or $action eq 2 or $action eq 8}
    {include file="CRM/Admin/Form/Tag.tpl"}	
{/if}

{if $rows}
{include file="CRM/common/dojo.tpl"}
<div id="cat">
<p></p>
    <div class="form-item">
        {strip}
        <table dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
	    <thead> 
        <tr class="columnheader">
	        <th field="Tag" dataType="String" >{ts}Tag{/ts}</th>
	        <th field="Description" dataType="String" >{ts}Description{/ts}</th>
	        <th datatype="html"></th>
        </tr>
        </thead> 
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}">
            <td>{$row.name}</td>	
            <td>{$row.description} </td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}
        
        {if !($action eq 1 and $action eq 2)}
	    <div class="action-link">
        <a href="{crmURL q="action=add&reset=1"}" id="newTag">&raquo; {ts}New Tag{/ts}</a>
        </div>
        {/if}
    </div>
</div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/tag' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no Tags present. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
