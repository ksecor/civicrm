{assign var="index" value=$addressBlockCount}
{if $title}
<h3 class="head"> 
    <span class="ui-icon ui-icon-triangle-1-e"></span><a href="#">{ts}{$title}{/ts}</a>
</h3>

<div id="addressBlock">
{/if}
<!-Add->
 <div id="Address_Block_{$addressBlockCount}">
  <table class="form-layout-compressed">
     <tr>
        <td colspan="2">
           {$form.address.$index.location_type_id.label}
           {$form.address.$index.location_type_id.html}
           {$form.address.$index.is_primary.html}
           {$form.address.$index.is_billing.html}
        </td>
     </tr>
     {if $form.use_household_address} 
     <tr>
        <td>{$form.use_household_address.html}{$form.use_household_address.label}<img src="../../i/quiz.png" / alt="help"> &nbsp;&nbsp;
        </td>
        <td></td>
     </tr>
     {/if}
     {if $form.address.$index.street_address}
     <tr>
        <td colspan="2">
           {$form.address.$index.street_address.label}<br />
           {$form.address.$index.street_address.html}<br />
           <span class="description font-italic">Street number, street name, apartment/unit/suite - OR P.O. box</span>
        </td>
     </tr>
     {/if}
     {if $form.address.$index.supplemental_address_1}
     <tr>
        <td colspan="2">
           {$form.address.$index.supplemental_address_1.label}<br />
           {$form.address.$index.supplemental_address_1.html} <br >
            <span class="description font-italic">Supplemental address info, e.g. c/o, department name, building name, etc.</span>
        </td>
     </tr>
     {/if}

     <tr>
        {if $form.address.$index.city}
        <td>
           {$form.address.$index.city.label}<br />
           {$form.address.$index.city.html}
        </td>
        {/if}
        {if $form.address.$index.postal_code}
        <td>
           {$form.address.$index.postal_code.label}<br />
           {$form.address.$index.postal_code.html}
           {$form.address.$index.postal_code_suffix.html}<br />
           <span class="description font-italic">Enter optional 'add-on' code after the dash ('plus 4' code for U.S. addresses).</span>
        </td>
        {/if}
     </tr>
     
     <tr>
        {if $form.address.$index.country_id}
        <td>
           {$form.address.$index.country_id.label}<br />
           {$form.address.$index.country_id.html}
        </td>
        {/if}
        {if $form.address.$index.state_province_id} 
        <td>
           {$form.address.$index.state_province_id.label}<br />
           {$form.address.$index.state_province_id.html}
        </td>
        {/if}
     </tr>
  </table>
{if $addMoreAddress}
<div id = "addMoreAddress" >
    <br />&nbsp;&nbsp;<a href="#" onclick="buildAdditionalBlocks( 'Address', '{$addressBlockCount+1}', '{$contactType}' );return false;" style="font-size: 10px;">add address</a><br /><br />
</div>
{/if}
 </div>
<!-Add->
{if $title}
</div>
{/if}
