<div id="name" class="data-group form-item">
    <p>
        <label>{$displayName}</label>
    </p>
</div>

<div id="groupContact">
 <p>
    <div class="form-item">
    {if $groupCount > 0 }  	
       <table>
       <tr class="columnheader"><th>Group Listings</th><th>In Date</th><th>Out Date</th><th></th></tr>
       {foreach from=$groupContact item=row}
         <tr class="{cycle values="odd-row,even-row"}">
            <td> {$row.name}</td>
            <td>{$row.in_date|date_format:"%B %e, %Y"}</td>
            <td>{$row.out_date|date_format:"%B %e, %Y"}</td>
	    <td><a href="#">View</a></td>   
         </tr>
       {/foreach}
       </table>
     {else}
     <div class="message status">	
     <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
      This contact does not belong to any groups.
     </div>	
     {/if}
    </div>
 </p>
  <span class="float-right">
   <a href="#">Add this contact to one or more groups...</a>
  </span>

</div>
