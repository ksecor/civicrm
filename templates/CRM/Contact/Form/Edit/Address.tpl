{assign var="index" value=$blockCount}
{if $title}
<h3 class="head"> 
    <span class="ui-icon ui-icon-triangle-1-e"></span><a href="#">{ts}{$title}{/ts}</a>
</h3>

<div id="addressBlock">
{/if}
<!-Add->
 <div id="Address_Block_{$index}">
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
        <td>
            {$form.use_household_address.html}{$form.use_household_address.label}<br /><br />
            <div id="share_household" style="display:none">
                {$form.shared_household.label}<br />
                {$form.shared_household.html|crmReplace:class:huge}&nbsp;&nbsp;<span id="show_address"></span>
				{if $mailToHouseholdID}<div id="shared_address"></div>{/if}
            </div>
        </td>
     </tr>
     {/if}
    <tr><td>
    <table id="address" style="display:block" class="form-layout-compressed">
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
      </tr>
    </table>
</td></tr>
  {/if}
  </tr>
     {if $addMoreAddress}
      <tr id="addMoreAddress" >
          <td><a href="#" onclick="buildAdditionalBlocks( 'Address', '{$contactType}' );return false;">add address</a></td>
      </tr>
    {/if}
  </table>
 </div>
<!-Add->
{if $title}
</div>
{/if}
{literal}
<script type="text/javascript">
cj(document).ready( function() { 
    //shared household default setting
	var householdId = "{/literal}{$mailToHouseholdID}{literal}";
	if ( cj('#use_household_address').is(':checked') || householdId) {
		var dataUrl = "{/literal}{crmURL p='civicrm/ajax/search' h=0 q="hh=1&id=" }{literal}" + householdId ;
		cj.ajax({ 
            url     : dataUrl,   
            async   : false,
            success : function(html){ 
                        //fixme for showing address in div
                        htmlText = html.split( '|' , 2);
                        cj('input#shared_household').val(htmlText[0]);
                        cj('input#shared_household_id').val(htmlText[1]);
                        htmlDiv = htmlText[0].replace( /::/gi, '<br />');
                        cj('div#shared_address').html('<br />'+htmlDiv);
                      }
                });
		cj('#share_household, table#address').toggle(); 
	}
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
                                                        cj( "#shared_household_id" ).val( '' );
                                                        cj( 'table#address' ).toggle( ); 
                                                    } else {
                                                        cj( 'table#address' ).hide( ); 
                                                        cj( "span#show_address" ).html( data[0] ); 
                                                        cj( "#shared_household_id" ).val( data[1] );
                                                    }
                                              });
</script>
{/literal}