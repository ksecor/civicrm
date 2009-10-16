<div id="help">
    {ts}Contact Subtypes provide convenient labels to differentiate contacts'. Administrators may define as many additional types as appropriate for your constituents (examples might be Student,Parent...).{/ts}
</div>

{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/ContactType.tpl"}
{/if}

{if $rows}
<div>
    {strip}
    {include file="CRM/common/jsortable.tpl"}
    <table id="options" class="display">
    <thead>
    <tr>
        <th>{ts}Contact SubTypes{/ts}</th>
        <th>{ts}Extends Basic Type{/ts}</th>
        <th id="nosort">{ts}Description{/ts}</th>
    </tr>
    </thead>
    {foreach from=$rows item=row}
        <td>{$row.subtype}</td>
        <td>{$row.parent}</td>
        <td>{$row.description}</td>
        <td>{$row.action}</td>
    </tr>
    {/foreach}
    </table>
    {/strip}
    {if $action ne 1 and $action ne 2}
    <div class="action-link">
	<a href="{crmURL q="action=add&reset=1"}" class="button"><span>&raquo; {ts}New Contact SubType{/ts}</span></a>
    </div>
    {/if}
</div>
{else}
    <div class="messages status">
     <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/ContactType' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no Contact Types entered  You can <a href='%1'>add one</a>.{/ts}</dd>
     </dl>
    </div>    
{/if}