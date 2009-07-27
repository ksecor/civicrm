{if $form.address.$blockId.supplemental_address_2}
   <tr>
      <td colspan="2">
          {$form.address.$blockId.supplemental_address_2.label}<br />
          {$form.address.$blockId.supplemental_address_2.html} <br >
          <span class="description font-italic">Supplemental address info, e.g. c/o, department name, building name, etc.</span>
      </td>
   </tr>
{/if}