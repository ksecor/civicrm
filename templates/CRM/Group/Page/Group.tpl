{if $action eq 4 }
  {if $members}
    <div id="memb">
     <p>
      <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
	<th>Name</th>
       </tr>
       {foreach from=$members item=data }
         <tr class="{cycle values="odd-row,even-row"}">
	    <td> {$data}  </td>	
         </tr>
       {/foreach}
       </table>
       {/strip}
      </div>
     </p>
    </div>
  {else}	
    <div class= "status">There are no members for this group.</div>
  {/if}

    <div class="action-link">
       <a href="{crmURL p='civicrm/group' q="reset=1"}">Back</a>
    </div>

{else}

   <div id="group">
    <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
	<th>Title</th>
	<th>Description</th>
	<th></th>
       </tr>
       {foreach from=$rows item=row}
         <tr class="{cycle values="odd-row,even-row"}">
	    <td> {$row.title}
	    </td>	
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

{/if}
