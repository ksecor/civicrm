{if $title and $className eq 'CRM_Contact_Form_Contact'}
<h3 class="head"> 
    <span class="ui-icon ui-icon-triangle-1-e"></span><a href="#">{ts}{$title}{/ts}</a>
</h3>

<div id="addressBlock" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
{/if}
<!-Add->
{if $blockId gt 1}<div class="spacer"></div>{/if}
 <div id="Address_Block_{$blockId}" style="background-color: #F7F7F7;border:1px solid #CCCCCC;"	class="ui-corner-all">
  <table class="form-layout-compressed">
	{if !$defaultLocation}
     <tr>
	 {if $className eq 'CRM_Contact_Form_Contact'}
        <td id='Address-Primary-html' colspan="2">
           {$form.address.$blockId.location_type_id.label}
           {$form.address.$blockId.location_type_id.html}
           {$form.address.$blockId.is_primary.html}
           {$form.address.$blockId.is_billing.html}
        </td>
	 {/if}
        {if $blockId gt 1}
            <td>
                <a href="#" title="{ts}Delete Address Block{/ts}" onClick="removeBlock( 'Address', '{$blockId}' ); return false;">{ts}delete{/ts}</a>
            </td>
        {/if}
     </tr>
	 {/if}
     {if $form.use_household_address} 
     <tr>
        <td>
            {$form.use_household_address.html}{$form.use_household_address.label}{help id="id-usehousehold"}<br />
            <div id="share_household" style="display:none">
                {$form.shared_household.label}<br />
                {$form.shared_household.html|crmReplace:class:huge}&nbsp;&nbsp;<span id="show_address"></span>
				{if $mailToHouseholdID}<div id="shared_address">{$sharedHouseholdAddress}</div>{/if}
            </div>
        </td>
     </tr>
     {/if}
    <tr><td>
    <table id="address" style="display:block" class="form-layout-compressed">
     {if $form.address.$blockId.name}
     <tr>
        <td colspan="2">
           {$form.address.$blockId.name.label}<br />
           {$form.address.$blockId.name.html}<br />
           <span class="description font-italic">{ts}Name of this address block like "My House, Work Place,.." which can be used in address book {/ts}</span>
        </td>
     </tr>
     {/if}
     {if $form.address.$blockId.street_address}
     <tr>
        <td colspan="2">
           {$form.address.$blockId.street_address.label}<br />
           {$form.address.$blockId.street_address.html}<br />
           <span class="description font-italic">Street number, street name, apartment/unit/suite - OR P.O. box</span>
        </td>
     </tr>
     {/if}
     {if $form.address.$blockId.supplemental_address_1}
     <tr>
        <td colspan="2">
           {$form.address.$blockId.supplemental_address_1.label}<br />
           {$form.address.$blockId.supplemental_address_1.html} <br >
            <span class="description font-italic">Supplemental address info, e.g. c/o, department name, building name, etc.</span>
        </td>
     </tr>
     {/if}
    {if $form.address.$blockId.supplemental_address_2}
    <tr>
        <td colspan="2">
           {$form.address.$blockId.supplemental_address_2.label}<br />
           {$form.address.$blockId.supplemental_address_2.html} <br >
            <span class="description font-italic">Supplemental address info, e.g. c/o, department name, building name, etc.</span>
        </td>
     </tr>
     {/if}

     <tr>
        {if $form.address.$blockId.city}
        <td>
           {$form.address.$blockId.city.label}<br />
           {$form.address.$blockId.city.html}
        </td>
        {/if}
        {if $form.address.$blockId.postal_code}
        <td>
           {$form.address.$blockId.postal_code.label}<br />
           {$form.address.$blockId.postal_code.html}
           {$form.address.$blockId.postal_code_suffix.html}<br />
           <span class="description font-italic">Enter optional 'add-on' code after the dash ('plus 4' code for U.S. addresses).</span>
        </td>
        {/if}
     </tr>
     {if $form.address.$blockId.county_id}
     <tr>
        <td colspan="2">
           {$form.address.$blockId.county_id.label}<br />
           {$form.address.$blockId.county_id.html}<br />
        </td>
     </tr>
     {/if}
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
	  {if $form.address.$blockId.geo_code_1 && $form.address.$blockId.geo_code_2}
      <tr>
        <td colspan="2">
            {$form.address.$blockId.geo_code_1.label},&nbsp;{$form.address.$blockId.geo_code_2.label}<br />
            {$form.address.$blockId.geo_code_1.html},&nbsp;{$form.address.$blockId.geo_code_2.html}<br />
            <span class="description font-italic">
                Latitude and longitude may be automatically populated by enabling a Mapping Provider. (<a href='http://wiki.civicrm.org/confluence/display/CRMDOC/Mapping+and+Geocoding' target='_blank'>learn more...</a>)
            </span>
        </td>
      </tr>
	  {/if}
    </table>
</td></tr>
      {if $className eq 'CRM_Contact_Form_Contact'}
      <tr id="addMoreAddress{$blockId}" >
          <td><a href="#" onclick="buildAdditionalBlocks( 'Address', '{$className}' );return false;">add address</a></td>
      </tr>
      {/if}
  </table>
 </div>
<!-Add->
{if $title and $className eq 'CRM_Contact_Form_Contact'}
</div>
{/if}
{literal}
<script type="text/javascript">
cj(document).ready( function() { 
    //shared household default setting
	if ( cj('#use_household_address').is(':checked') ) {
    	cj('table#address').hide(); 
        cj('#share_household').show(); 
    }
{/literal}
{if $mailToHouseholdID}
{literal}
		var dataUrl = "{/literal}{crmURL p='civicrm/ajax/search' h=0 q="hh=1&id=$mailToHouseholdID"}{literal}";
		cj.ajax({ 
            url     : dataUrl,   
            async   : false,
            success : function(html){ 
                        //fixme for showing address in div
                        htmlText = html.split( '|' , 2);
                        cj('input#shared_household').val(htmlText[0]);
                    }
                });
{/literal}
{/if}
{literal}
	//event handler for use_household_address check box
	cj('#use_household_address').click( function() { 
		cj('#share_household').toggle( );
        if( ! cj('#use_household_address').is(':checked')) {
            cj('table#address').show( );
        } else {
           cj('table#address').toggle( );
        }
	});	
});

var dataUrl = "{/literal}{$housholdDataURL}{literal}";
cj('#shared_household').autocomplete( dataUrl, { width : 320, selectFirst : false 
                                              }).result( function(event, data, formatted) { 
                                                    if( isNaN( data[1] ) ){
                                                        cj( "span#show_address" ).html( 'New Household Record'); 
                                                        cj( "#shared_household_id" ).val( data[0] );
                                                        cj( 'table#address' ).toggle( ); 
                                                    } else {
                                                        cj( 'table#address' ).hide( ); 
                                                        cj( "span#show_address" ).html( data[0] ); 
                                                        cj( "#shared_household_id" ).val( data[1] );
                                                    }
                                              });
</script>
{/literal}