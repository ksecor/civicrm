{if $action eq 1 or $action eq 2 or $action eq 8}
  {include file="CRM/Admin/Form/DedupeFind.tpl"}
{else}
{if $rows}
<div id="browseValues">
  <div class="form-item">
    {strip}
      <table>
        <tr class="columnheader">
          <th>{ts}Contact Type{/ts}</th>
          <th></th>
        </tr>
        {foreach from=$rows item=row}
          <tr class="{cycle values="odd-row,even-row"} {$row.class}">
            <td>{$row.contact_type_display}</td>	
            <td>{$row.action}</td>
          </tr>
        {/foreach}
      </table>
    {/strip}
  </div>
</div>
{/if}
{/if}
