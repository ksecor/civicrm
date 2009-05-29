{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/EventTemplate.tpl"}
{/if}

{if $rows}
<div id="mSettings">
  <p></p>
  <div class="form-item">
    {strip}
      <table cellpadding="0" cellspacing="0" border="0">
        <tr class="columnheader">
            <th>{ts}Title{/ts}</th>
            <th>{ts}Event Type{/ts}</th>
            <th>{ts}Participant Role{/ts}</th>
            <th>{ts}Participant Listing{/ts}</th>
            <th>{ts}Public Event{/ts}</th>
            <th>{ts}Paid Event{/ts}</th>
            <th>{ts}Allow Online Registration{/ts}</th>
	    <th>{ts}Is Active?{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
          <tr id='rowid{$row.id}' class="{cycle values="odd-row,even-row"}">
              <td>{$row.template_title}</td>	
              <td>{$row.event_type}</td>	
              <td>{$row.participant_role}</td>	
              <td>{$row.participant_listing}</td>	
              <td>{if $row.is_public eq 1}{ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
              <td>{if $row.is_monetary eq 1}{ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
              <td>{if $row.is_online_registration eq 1}{ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
              <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
              <td>{$row.action|replace:'xx':$row.id}</td>
          </tr>
        {/foreach}
      </table>
    {/strip}

    {if $action ne 1 and $action ne 2}
      <div class="action-link">
        <a href="{crmURL p="civicrm/event/manage" q="action=add&is_template=1&reset=1"}" id="newEventTemplate" class="button"><span>&raquo; {ts}New Event Template{/ts}</span></a>
      </div>
    {/if}
  </div>
</div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/event/manage' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no Event Templates present. You can <a href='%1'>add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
