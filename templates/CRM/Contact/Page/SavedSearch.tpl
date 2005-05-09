{strip}
<p>
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
            <li>{$criteria}
        {/foreach}
        </ul>
    </td>
    <td><a href="{crmURL p='civicrm/contact/search/advanced' q="ssID=`$row.id`&reset=1&force=1"}">Search</a></td>
</tr>
{/foreach}
</table>
</p>
{/strip}
