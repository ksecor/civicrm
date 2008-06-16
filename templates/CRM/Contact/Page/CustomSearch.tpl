{strip}
<p>
{if $rows}
    <table>
    <tr class="columnheader">
        <th>{ts}Custom Search{/ts}</th>
        <th></th>
    </tr>

    {foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.name}</td>
        <td>{$row.action}</td>
    </tr>
    {/foreach}
    </table>
{else}
    <div class="messages status">
      <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>
            {ts}There are currently no Custom Searches.{/ts}
        </dd>
      </dl>
    </div>
{/if}
</p>
{/strip}
