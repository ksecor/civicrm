<h3 class="head"> 
    <span class="ui-icon ui-icon-triangle-1-e"></span><a href="#">{ts}{$title}{/ts}</a>
</h3>

<div></div>

{* FIXME
<div id="addressBlock">
  <table class="form-layout-compressed">   
     <tr>
        <td>{$form.use_household_address.html}{$form.use_household_address.label}<img src="../../i/quiz.png" / alt="help"> &nbsp;&nbsp;
        </td>
        <td></td>
     </tr>
     {if $form.location.$index.address.street_address}
     <tr>
        <td colspan="2">
           {$form.location.$index.address.street_address.label}<br />
           {$form.location.$index.address.street_address.html}<br />
           <span class="description font-italic">Street number, street name, apartment/unit/suite - OR P.O. box</span>
        </td>
     </tr>
     {/if}
     {if $form.location.$index.address.supplemental_address_1}
     <tr>
        <td colspan="2">
           {$form.location.$index.address.supplemental_address_1.label}<br />
           {$form.location.$index.address.supplemental_address_1.html} <br >
            <span class="description font-italic">Supplemental address info, e.g. c/o, department name, building name, etc.</span>
        </td>
     </tr>
     {/if}
     <tr>
        {if $form.location.$index.address.city}
        <td>
           {$form.location.$index.address.city.label}<br />
           {$form.location.$index.address.city.html}
        </td>
        {/if}
        {if $form.location.$index.address.postal_code}
        <td>
           {$form.location.$index.address.postal_code.label}<br />
           {$form.location.$index.address.postal_code.html}
           {$form.location.$index.address.postal_code_suffix.html}<br />
           <span class="description font-italic">Enter optional 'add-on' code after the dash ('plus 4' code for U.S. addresses).</span>
        </td>
        {/if}
     </tr>
     <tr>
        {if $form.location.$index.address.country_id}
        <td>
           {$form.location.$index.address.country_id.label}<br />
           {$form.location.$index.address.country_id.html}
        </td>
        {/if}
        {if $form.location.$index.address.state_province_id} 
        <td>
           {$form.location.$index.address.state_province_id.label}<br />
           {$form.location.$index.address.state_province_id.html}
        </td>
        {/if}
     </tr>
  </table>
 <div>
*}

