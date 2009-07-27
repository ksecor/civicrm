{if $form.address.$blockId.geo_code_1 && $form.address.$blockId.geo_code_2}
   <tr>
      <td colspan="2">
          {$form.address.$blockId.geo_code_1.label},&nbsp;{$form.address.$blockId.geo_code_2.label}<br />
          {$form.address.$blockId.geo_code_1.html},&nbsp;{$form.address.$blockId.geo_code_2.html}<br />
          <span class="description font-italic"> Latitude and longitude may be automatically populated by enabling a Mapping Provider. (<a href='http://wiki.civicrm.org/confluence/display/CRMDOC/Mapping+and+Geocoding' target='_blank'>learn more...</a>)
          </span>
      </td>
   </tr>
{/if}