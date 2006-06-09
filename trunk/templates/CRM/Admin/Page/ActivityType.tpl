{* Admin page for browsing Activity Types *}
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/ActivityType.tpl"}
{else}
<div id="help">
    <p>{ts}Activities are 'interactions with contacts' which you want to record and track. CiviCRM has several reserved (e.g. 'built-in') activity types (meetings, phone calls, emails sent). Create additional 'activity types' here if you need to record other types of activities. For example, you might want to include 'New Client Intake', or 'Site Audit', etc. ...as types of trackable activites.{/ts}</p>
    <p>{ts}Subject, location, date/time and description fields are provided for all activity types. You can add custom fields for tracking additional information about activities <a href="{crmURL p='civicrm/admin/custom/group' q='reset=1'}">here</a>.{/ts}</p>
    <p>{ts}Completed activities are searchable by type and/or activity date using 'Advanced Search'. Other applications may record activities for CiviCRM contacts using our APIs. For more information, refer to our Administrator Documentation.{/ts}</p>
</div>
{/if}

{if $rows}
<div id="browseValues">
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Name{/ts}</th>
            <th>{ts}Description{/ts}</th>
            <th>{ts}Reserved?{/ts}</th>
            <th>{ts}Enabled?{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.name}</td>	
	        <td>{$row.description}</td>
	        <td>{if $row.is_reserved eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}" id="newActivityType">&raquo; {ts}New Activity Type{/ts}</a>
        </div>
        {/if}
    </div>
</div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/activityType' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no custom Activity Types entered. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
