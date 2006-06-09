<div id="name" class="data-group form-item">
    <p>
        <label>{$displayName}</label>
    </p>
</div>

<div id="groupContact">
 <p>
    <div class="form-item">
    {if $logCount > 0 }  	
       <table>
       <tr class="columnheader"><th>{ts}Modified By{/ts}</th><th>{ts}Modified Date{/ts}</th></tr>
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
      {ts}No recorded modification log for this contact.{/ts}
     </div>	
     {/if}
    </div>
 </p>
</div>
