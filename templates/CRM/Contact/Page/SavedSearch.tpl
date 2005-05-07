<div class="form-item">
    <div class="data-group label">List of Saved Searches.</div> 
    
    
    {strip}
    <p>
    <table>
    <tr class="columnheader">
        <th>Name</th>
        <th>Description</th>
        <th>Query Detail</th>
        <th>Action</th>
    </tr>
    
    {foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.name}</td>
        <td>{$row.description}</td>
        <td>{$row.query_detail}</td>
	<td><a href="{crmURL p='civicrm/contact/search/advanced' q="ssID=`$row.id`&reset=1&force=1"}">Run</a></td>
    </tr>
    {/foreach}
    </table>
    </p>
    {/strip}

</div>