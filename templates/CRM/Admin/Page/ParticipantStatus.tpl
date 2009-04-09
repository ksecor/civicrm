{if $action eq 1 or $action eq 2 or $action eq 8}
  {include file="CRM/Admin/Form/ParticipantStatus.tpl"}
{/if}

<div class="form-item">
  {strip}
    <table cellpadding="0" cellspacing="0" border="0">
      <tr class="columnheader">
        <th>{ts}Name{/ts}</th>
        <th>{ts}Label{/ts}</th>
        <th>{ts}Class{/ts}</th>
        <th>{ts}Reserved?{/ts}</th>
        <th>{ts}Active?{/ts}</th>
        <th>{ts}Counted?{/ts}</th>
        <th>{ts}Weight{/ts}</th>
        <th>{ts}Visibility{/ts}</th>
        <th></th>
      </tr>
      {foreach from=$rows item=row}
        <tr id="rowid{$row.id}" class="{cycle values="odd-row,even-row"}">
          <td>{$row.name}</td>
          <td>{$row.label}</td>
          <td>{$row.class}</td>
          <td>{if $row.is_reserved}{ts}Yes{/ts}{else}{ts}No{/ts}{/if}</td>
          <td>{if $row.is_active}  {ts}Yes{/ts}{else}{ts}No{/ts}{/if}</td>
          <td>{if $row.is_counted} {ts}Yes{/ts}{else}{ts}No{/ts}{/if}</td>
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
