<div id="help">
    {ts}When recording Instant Messenger (IM) 'screen names' for contacts, it is useful to include the IM Service Provider (e.g. AOL, Yahoo, etc.). CiviCRM is installed with the most commonly encountered service providers. Administrators may define as many additional providers as needed.{/ts}
</div>

{if $action eq 1 or $action eq 2}
   {include file="CRM/Admin/Form/IMProvider.tpl"}
{/if}

{if $rows}
<div id="improvider">
<p>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Instant Messenger Service{/ts}</th>
            <th>{ts}Reserved?{/ts}</th>
            <th>{ts}Enabled?{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.name}</td>	
 	        <td>{if $row.is_reserved eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
        <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}">&raquo; {ts}New IM Service Provider{/ts}</a>
        </div>
        {/if}
    </div>
</p>
</div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/IMProvider' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no IMProviders entered for this Contact. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
