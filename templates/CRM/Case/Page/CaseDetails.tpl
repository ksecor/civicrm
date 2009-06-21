{strip}
{if $rows}
  <table class="nestedActivitySelector">
    <tr class="columnheader">
      <th>{ts}Date{/ts}</th>
      <th>{ts}Subject{/ts}</th>
      <th>{ts}Type{/ts}</th>
      <th>{ts}Reporter/ Assignee{/ts}</th>
      <th>{ts}Status{/ts}</th>
      <th></th>
    </tr>

    {counter start=0 skip=1 print=false}
    {foreach from=$rows item=row}
    <tr class="{$row.class}">
      <td>{$row.display_date}</td>
      <td>{$row.subject}</td>
      <td>{$row.type}</td>
      <td>{$row.reporter}</td>
      <td>{$row.status}</td>
      <td style="white-space: nowrap;">{$row.links}</td>
    </tr>
    {/foreach}

  </table>
{else}
    <strong>{ts}There are no activities defined for this case.{/ts}</strong>
{/if}
{/strip}