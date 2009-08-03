{if $action eq 1 or $action eq 2 or $action eq 8}
  {include file="CRM/Admin/Form/ParticipantStatus.tpl"}
{else}
  <div id="help">{ts}Manage event participant statuses below. Enable selected statuses to allow event waitlisting and/or participant approval.{/ts} {help id="id-disabled_statuses" file="CRM/Admin/Page/ParticipantStatus.hlp"}</div>
{/if}

<div class="form-item">
  {strip}
    {* handle enable/disable actions*}
    {include file="CRM/common/enableDisable.tpl"}
    <table cellpadding="0" cellspacing="0" border="0">
      <thead class="sticky">
        <th>{ts}Label{/ts}</th>
        <th>{ts}Name{/ts}</th>
        <th>{ts}Class{/ts}</th>
        <th>{ts}Reserved?{/ts}</th>
        <th>{ts}Active?{/ts}</th>
        <th>{ts}Counted?{/ts}</th>
        <th>{ts}Weight{/ts}</th>
        <th>{ts}Visibility{/ts}</th>
        <th></th>
      </thead>
      {foreach from=$rows item=row}
       <tr id="row_{$row.id}" class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
          <td>{$row.label}</td>
          <td>{$row.name}</td>
          <td>{$row.class}</td>
          <td class="yes-no">{if $row.is_reserved}<img src="{$config->resourceBase}/i/check.gif" alt="{ts}Reserved{/ts}" />{/if}</td>
	  <td id="row_{$row.id}_status">{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
          <td class="yes-no">{if $row.is_counted} <img src="{$config->resourceBase}/i/check.gif" alt="{ts}Counted{/ts}" />{/if}</td>
          <td>{$row.weight}</td>
          <td>{$row.visibility}</td>
          <td>{$row.action|replace:'xx':$row.id}</td>
        </tr>
      {/foreach}
    </table>
  {/strip}

  {if $action ne 1 and $action ne 2}
    <div class="action-link">
      <a href="{crmURL q="action=add&reset=1"}" class="button"><span>&raquo; {ts}New Participant Status{/ts}</span></a>
    </div>
  {/if}
</div>
