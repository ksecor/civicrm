<tr>
   {if $form.address.$blockId.country_id}
     <td>
        {$form.address.$blockId.country_id.label}<br />
        {$form.address.$blockId.country_id.html}
     </td>
   {/if}
   {if $form.address.$blockId.state_province_id} 
     <td>
        {$form.address.$blockId.state_province_id.label}<br />
        {$form.address.$blockId.state_province_id.html}
     </td>
   {/if}
</tr>
