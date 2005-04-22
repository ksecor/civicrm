{*debug*} 

<div class="form-item">
    <div class="data-group label">List of Saved Searches.</div> 
    
    
    {strip}
    <p>
    <table>
    <tr class="columnheader">
        <th>Name</th>
        <th>Description</th>
        <th>qill</th>
        <th></th>
    </tr>
    
    {foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.name}</td>
        <td>{$row.description}</td>
        <td>{$row.qill}</td>
        <td></td>
    </tr>
    {/foreach}
    </table>
    </p>
    {/strip}

</div>
