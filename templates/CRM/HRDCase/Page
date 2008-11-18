{literal}
<style>
  #crm-container table.nestedSelector
  {
    margin: 0px;
    width: 100%;
    border: 2px solid #5A8FDB;
  }

  #crm-container table.nestedSelector tr.columnheader
  {
    background-color: #4D94E3;
  }

  #crm-container table.nestedSelector tr.columnheader th
  {
    border: 0px;
  }
</style>
{/literal}

{strip}
{if $rows}
  <table class="nestedSelector">
    <tr class="columnheader">
      <th>{ts}Due date{/ts}</th>
      <th>{ts}Actual date{/ts}</th>
      <th>{ts}Subject{/ts}</th>
      <th>{ts}Category{/ts}</th>
      <th>{ts}Type{/ts}</th>
      <th>{ts}Reporter{/ts}</th>
      <th>{ts}Status{/ts}</th>
    </tr>

    {counter start=0 skip=1 print=false}
    {foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"} {$row.class}">
      <td>{$row.due_date}</td>
      <td>{$row.actual_date}</td>
      <td>{$row.subject}</td>
      <td>{$row.category}</td>
      <td>{$row.type}</td>
      <td>{$row.reporter}</td>
      <td>{$row.status}</td>
    </tr>
    {/foreach}

  </table>
{else}
    <strong>There is no activities defined for this case.</strong>
{/if}
{/strip}