<div id="help">
    {ts}Import / Export Mappings.{/ts}
</div>

{if $action eq 1 or $action eq 2 or $action eq 8}
    {include file="CRM/Admin/Form/Mapping.tpl"}	
{/if}

{if $rows}
<div id="mapping">
<p></p>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
	        <th>{ts}Name{/ts}</th>
	        <th>{ts}Description{/ts}</th>
            <th>{ts}Mapping Type{/ts}</th>
	        <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}">
            <td>{$row.name}</td>	
            <td>{$row.description}</td>
            <td>{$row.mapping_type}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}
    </div>
</div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts}There are no Saved Mappings.{/ts}</dd>
        </dl>
    </div>    
{/if}
