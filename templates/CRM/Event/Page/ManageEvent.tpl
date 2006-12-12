{capture assign=crmURL}{crmURL p='civicrm/admin/event/manageEvent' q="action=add&reset=1"}{/capture}
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Event/Form/ManageEvent.tpl"}
{else}
    <div id="help">
        <p>{ts}ManageEvent Page lists all current and upcoming events (where End Date is greater than oe equal to current date + 1month).{/ts}
    </div>
{/if}

{capture assign=eventWizard}{crmURL p='civicrm/admin/event/manageEvent' q="action=add&reset=1"}{/capture}
{ts 1=$eventWizard}<a href="%1">&raquo; New Event Wizard</a>{/ts}

{if $rows}
<div id="ltype">
<p></p>
    <div class="form-item" id=eventship_status_id>
        {strip}
        <table dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
	<thead>
        <tr class="columnheader">
            <th field="Status" dataType="String">{ts}Status{/ts}</th>
            <th field="Start Event" dataType="String">{ts}Start Event{/ts}</th>
            <th field="End Event" dataType="String">{ts}End Event{/ts}</th>
            <th field="Event" dataType="String">{ts}Event{/ts}</th>
            <th field="Admin" dataType="String">{ts}Admin{/ts}</th>
	    <th field="Weight" dataType="Number" sort="asc">{ts}Weight{/ts}</th>
	    <th field="Enabled"  dataType="String" >{ts}Enabled?{/ts}</th>
	    <th datatype="html"></th>
        </tr>
	</thead>
        <tbody> 
        {foreach from=$rows item=row}
          <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.name}</td>	
	        <td>{$row.start_event}</td>
	        <td>{$row.end_event}</td>
	        <td>{if $row.is_current_event eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{if $row.is_admin eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{$row.weight}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{$row.action}</td>
          </tr>
        {/foreach}
        </tbody>
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}" id="newManageEvent">&raquo; {ts}New Eventship Status{/ts}</a>
        </div>
        {/if}
    </div>
</div>
{else}
  {if $action ne 1}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts 1=$eventWizard}There are no events created yet. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
  {/if}
{/if}
