{* This file provides the plugin for the Address block *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller*}
{* @var $blockId Contains the current address block id, and assigned in the  CRM/Contact/Form/Location.php file *}

{if $title and $className eq 'CRM_Contact_Form_Contact'}
<h3 class="head"> 
    <span class="ui-icon ui-icon-triangle-1-e"></span><a href="#">{ts}{$title}{/ts}</a>
</h3>

<div id="addressBlock" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
{/if}

{if $blockId gt 1}<div class="spacer"></div>{/if}

 <div id="Address_Block_{$blockId}" {if $className eq 'CRM_Contact_Form_Contact'} class="boxBlock ui-corner-all" {/if}>
  <table class="form-layout-compressed">
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
         {* build address block w/ address sequence. *}
         {foreach item=addressElement from=$addressSequence}
              {include file=CRM/Contact/Form/Edit/Address/$addressElement.tpl}
         {/foreach}
         {include file=CRM/Contact/Form/Edit/Address/geo_code.tpl}
     </table>

     </td></tr>
     {if $className eq 'CRM_Contact_Form_Contact'}
     <tr id="addMoreAddress{$blockId}" >
         <td><a href="#" onclick="buildAdditionalBlocks( 'Address', '{$className}' );return false;">add address</a></td>
     </tr>
     {/if}
  </table>
 </div>

{if $title and $className eq 'CRM_Contact_Form_Contact'}
</div>
{/if}
{literal}
<script type="text/javascript">
{/literal}
{if $blockId eq 1}
{literal}
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
{/literal}
{/if}	
{literal}										  
//to check if same location type is already selected.
function checkLocation( object, noAlert ) {
	selectedText = cj( '#' + object + ' :selected').text();
	cj( 'td#Address-Primary-html select' ).each( function() {
		element = cj(this).attr('id');
		if( cj(this).val() && element != object && selectedText == cj( '#' + element + ' :selected').text() ) {
			if( ! noAlert ) alert( 'Location Type ' + selectedText + ' is already Selected. Please select another Location Type !' );
			cj( '#' + object ).val('');
		}
	});
}
</script>
{/literal}