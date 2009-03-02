<div id="help">
    {ts}Location types provide convenient labels to differentiate contacts' location(s). Administrators may define as many additional types as appropriate for your constituents (examples might be Main Office, School, Vacation Home...).{/ts}
</div>

{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/LocationType.tpl"}
{/if}

{if $rows}
<div id="ltype">
<p></p>
    <div class="form-item">
        {strip}
        <table cellpadding="0" cellspacing="0" border="0">
        <tr class="columnheader">
            <th>{ts}Name{/ts}</th>
            <th>{ts}vCard{/ts}</th>
            <th>{ts}Description{/ts}</th>
            <th>{ts}Enabled?{/ts}</th>
	        <th>{ts}Default?{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.name}</td>	
	        <td>{$row.vcard_name}</td>	
            <td>{$row.description}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{if $row.is_default eq 1}<img src="{$config->resourceBase}/i/check.gif" alt="{ts}Default{/ts}" />{/if}&nbsp;</td>
	        <td class="btn-slide" id={$row.id}>{$row.action|replace:'xx':$row.id}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}" id="newLocationType" class="button"><span>&raquo; {ts}New Location Type{/ts}</span></a>
        </div>
        {/if}
    </div>
</div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/locationType' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no Location Types entered for this Contact. You can <a href='%1'>add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
