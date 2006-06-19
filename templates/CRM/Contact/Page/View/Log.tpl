<div id="changeLog">
    <p></p>
    <div class="bold">{ts}Change Log:{/ts} {$displayName}</div>
    <div class="form-item">
     {if $logCount > 0 }  	
       <table>
       <tr class="columnheader"><th>{ts}Changed By{/ts}</th><th>{ts}Change Date{/ts}</th></tr>
       {foreach from=$log item=row}
         <tr class="{cycle values="odd-row,even-row"}">
            <td> {$row.image}&nbsp;<a href="{crmURL p='civicrm/contact/view' q="action=view&reset=1&cid=`$row.id`"}">{$row.name}</a></td>
            <td>{$row.date|crmDate}</td>
         </tr>
       {/foreach}
       </table>
     {else}
     <div class="messages status">	
     <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"> &nbsp;
      {ts}No modifications have been logged for this contact.{/ts}
     </div>	
     {/if}
    </div>
 </p>
</div>
