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
        <table>
        <tr class="columnheader">
            <th>{ts}Title{/ts}</th>
            <th>{ts}Name{/ts}</th>
            <th>{ts}Description{/ts}</th>
            <th>{ts}Weight{/ts}</th>
            <th>{ts}Default{/ts}</th>
            <th>{ts}Reserved?{/ts}</th>
            <th>{ts}Enabled?{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.label}</td>
	        <td>{$row.name}</td>	
	        <td>{$row.description}</td>
            <td>{$row.weight}</td>
            <td>{$row.default_value}</td>
	        <td>{if $row.is_reserved eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{$row.action}</td>
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
        <dd>{ts 1=$crmURL}There are no option choices entered for this option group. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
