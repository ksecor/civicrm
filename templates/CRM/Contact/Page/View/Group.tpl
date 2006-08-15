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
       <tr class="columnheader"><th>{ts}Group Listings{/ts}</th><th>{ts}In Date{/ts}</th><th>{ts}Out Date{/ts}</th><th></th></tr>
       {foreach from=$groupContact item=row}
         <tr class="{cycle values="odd-row,even-row"}">
            <td> {$row.name}</td>
            <td>{$row.in_date|crmDate}</td>
            <td>{$row.out_date|crmDate}</td>
	    <td><a href="#">{ts}View{/ts}</a></td>
         </tr>
       {/foreach}
       </table>
     {else}
     <div class="messages status">	
     <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /> &nbsp;
      {ts}This contact does not belong to any groups.{/ts}
     </div>	
     {/if}
    </div>
 </p>
  <span class="float-right">
   <a href="#">{ts}Add this contact to one or more groups...{/ts}</a>
  </span>

</div>
