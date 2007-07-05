<div id="electedOfficals" class="view-content">
    <p></p>
    <div class="bold">{ts}Elected Officals:{/ts} {$displayName}</div>
    <div class="form-item">
     {if $rowCount > 0 }  	
       <table>
       <tr class="columnheader">
          <th>{ts}Image{/ts}</th>
          <th>{ts}Name{/ts}</th>
          <th>{ts}Party{/ts}</th>
          <th>{ts}Address{/ts}</th>
          <th>{ts}Phone{/ts}</th>
          <th>{ts}Email{/ts}</th>
       </tr>
       {foreach from=$rows item=row}
         <tr class="{cycle values="odd-row,even-row"}">
            <td><a href="{$row.url}"><img src="{$row.image_url}"></a></td>
            <td>{$row.title} {$row.first_name} {$row.last_name}</td>
            <td>{$row.party}</td>
            <td>{$row.address}</td>
            <td>{$row.phone}</td>
            <td>{$row.email}</td>
         </tr>
       {/foreach}
       </table>
     {else}
     <div class="messages status">	
     <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"> &nbsp;
      {ts}No data available for this contact. Please check city/state/zipcode{/ts}
     </div>	
     {/if}
    </div>
 </p>
</div>
