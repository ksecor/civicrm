{if $form.address.$blockId.supplemental_address_1}
  <tr>
     <td colspan="2">
         {$form.address.$blockId.supplemental_address_1.label}<br />
         {$form.address.$blockId.supplemental_address_1.html} <br >
         <span class="description font-italic">Supplemental address info, e.g. c/o, department name, building name, etc.</span>
     </td>
  </tr>
{/if}