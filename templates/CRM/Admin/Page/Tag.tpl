<div id="help">
    {ts}Tags can be assigned to any contact record, and are a convenient way to find contacts. You can create as many tags as needed to organize and segment your records.{/ts}
</div>

{if $action eq 1 or $action eq 2 or $action eq 8}
    {include file="CRM/Admin/Form/Tag.tpl"}	
{/if}

{if $rows}
<div id="cat">
<p></p>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
	        <th>{ts}Tag{/ts}</th>
	        <th>{ts}Description{/ts}</th>
	        <th></th>
        </tr>
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
        <dd>{ts 1=$crmURL}There are no Tags entered for this Contact. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
