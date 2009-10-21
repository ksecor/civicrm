{* Admin page for browsing Option Group value*}
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/OptionValue.tpl"}
{else}
<div id="help">
    {ts}The existing option choices for this option group are listed below. You can add, edit or delete them from this screen.{/ts}
</div>
{/if}

{if $rows}

<div id="browseValues">
    <div class="form-item">
        {strip}
	 {* handle enable/disable actions*}
 	 {include file="CRM/common/enableDisable.tpl"}
 	 {include file="CRM/common/jsortable.tpl"}
         <table id="options" class="display">
         <thead>
         <tr>
            <th>{ts}Title{/ts}</th>
            <th>{ts}Value{/ts}</th>
            <th>{ts}Description{/ts}</th>
            <th>{ts}Weight{/ts}</th>
           {if $showIsDefault} 
            <th>{ts}Default{/ts}</th>
           {/if}
            <th>{ts}Reserved?{/ts}</th>
            <th>{ts}Enabled?{/ts}</th>
            <th></th>
        </tr>
        </thead>
        {foreach from=$rows item=row}
	<tr id="row_{$row.id}"class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.label}</td>
	    <td>{$row.value}</td>	
	    <td>{$row.description}</td>
            <td class="nowrap">{$row.weight}</td>
           {if $showIsDefault} 
            <td>{$row.default_value}</td> 
           {/if}
	        <td>{if $row.is_reserved eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td id="row_{$row.id}_status">{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{$row.action|replace:'xx':$row.id}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1&gid=$gid"}" id="newOptionValue">&raquo; {ts}New Option Value{/ts}</a>
        </div>
        {/if}
    </div>
</div>
{elseif $action ne 1}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/optionValue' q="action=add&reset=1&gid=$gid"}{/capture}
        <dd>{ts 1=$crmURL}There are no option choices entered for this option group. You can <a href='%1'>add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
