<div id="help">
    {ts}When recording mobile phone numbers for contacts, it may be useful to include the Mobile Phone Service Provider (e.g. Cingular, Sprint, etc.). CiviCRM is installed with the most commonly encountered service providers. Administrators may define as many additional providers as needed.{/ts}
</div>

{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/MobileProvider.tpl"}
{/if}

{if $rows}
<div id="mobprovider">
<p></p>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
	        <th>{ts}Mobile Phone Provider{/ts}</th>
            <th>{ts}Enabled?{/ts}</th>
	        <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td> {$row.name}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{$row.action}</td>	
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}" id="newMobileProvider">&raquo; {ts}New Mobile Phone Provider{/ts}</a>
        </div>
        {/if}
    </div>
</div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/mobileProvider' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no Mobile Providers entered for this Contact. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
