{* Actions: 1=add, 2=edit, browse=16, delete=8 *}
<div id="group">
<p>
<div class="form-item">
   {strip}
   <table>
   <tr class="columnheader">
    <th>Name</th>
    <th>Description</th>
    <th></th>
   </tr>
   {foreach from=$rows item=row}
     <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.title}</td>	
        <td>
            {$row.description|truncate:80:"...":true}
        </td>
        <td>{$row.action}</td>
     </tr>
   {/foreach}
   </table>
   {/strip}

   {if $action ne 1 and $action ne 2}
    <br/>
    <div class="action-link">
        <a href="{crmURL p='civicrm/group/edit'}">New Group</a>
    </div>
   {/if}
</div>
</p>
</div>

