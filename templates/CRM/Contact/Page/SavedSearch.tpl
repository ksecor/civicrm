{strip}
<p>
{if $rows}
    <table>
    <tr class="columnheader">
        <th>Saved Search</th>
        <th>Description</th>
        <th>Criteria</th>
        <th></th>
    </tr>

    {foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.name}</td>
        <td>{$row.description}</td>
        <td><ul>
            {foreach from=$row.query_detail item=criteria}
                <li>{$criteria}</li>
            {/foreach}
            </ul>
        </td>
        <td>{$row.action}</td>
    </tr>
    {/foreach}
    </table>
{else}
    <div class="messages status">
      <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
        <dd>
            There are currently no Saved Searches. To create a Saved search:
            <p>
            <ul>
            <li>Use <a href="{crmURL p='civicrm/contact/search' q='reset=1'}">Find</a> or
                <a href="{crmURL p='civicrm/contact/search/advanced' q='reset=1'}"> Advanced Search</a> form to enter search criteria
            <li>Run and refine the search criteria as necessary
            <li>Select 'New Saved Search' from the '-more actions' drop-down menu and click 'Go'
            <li>Enter a name and description for your Saved Search
            </ul>
            </p>
        </dd>
      </dl>
    </div>
{/if}
</p>
{/strip}
