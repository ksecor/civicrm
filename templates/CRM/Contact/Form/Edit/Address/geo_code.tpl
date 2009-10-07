{if $form.address.$blockId.geo_code_1 && $form.address.$blockId.geo_code_2}
    {capture assign=docLink}{docURL page="Mapping and Geocoding"}{/capture}
   <tr>
      <td colspan="2">
          {$form.address.$blockId.geo_code_1.label},&nbsp;{$form.address.$blockId.geo_code_2.label}<br />
          {$form.address.$blockId.geo_code_1.html},&nbsp;{$form.address.$blockId.geo_code_2.html}<br />
          <span class="description font-italic">
            {ts}Latitude and longitude may be automatically populated by enabling a Mapping Provider.{/ts} {$docLink}
          </span>
      </td>
   </tr>
{/if}