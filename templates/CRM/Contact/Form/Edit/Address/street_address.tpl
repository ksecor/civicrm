{if $form.address.$blockId.street_address}
    <tr>
       <td colspan="2">
           {$form.address.$blockId.street_address.label}<br />
           {$form.address.$blockId.street_address.html}<br />
           <span class="description font-italic">Street number, street name, apartment/unit/suite - OR P.O. box</span>
       </td>
    </tr>
{/if}