{* This file provides the HTML for the on-behalf-of form. Can also be used for related contact edit form. *}
<fieldset id="for_organization"><legend>{$fieldSetTitle}</legend>
{if $contact_type eq 'Individual'}

  {if $contactEditMode}<fieldset><legend></legend>{/if}
	<table class="form-layout-compressed">
    <tr>
		<td>{$form.prefix_id.label}</td>
		<td>{$form.first_name.label}</td>
		<td>{$form.middle_name.label}</td>
		<td>{$form.last_name.label}</td>
		<td>{$form.suffix_id.label}</td>
	</tr>
	<tr>
		<td>{$form.prefix_id.html}</td>
		<td>{$form.first_name.html}</td>
		<td>{$form.middle_name.html|crmReplace:class:eight}</td>
		<td>{$form.last_name.html}</td>
		<td>{$form.suffix_id.html}</td>
	</tr>
   	
    </table>
  {if $contactEditMode}</fieldset>{/if}


{elseif $contact_type eq 'Household'}

 {if $contactEditMode}<fieldset><legend></legend>{/if}
   	<table class="form-layout-compressed">
      <tr>
		<td>{$form.household_name.label}</td>
      </tr>
      <tr>
        <td>{$form.household_name.html|crmReplace:class:big}</td>
      </tr>
    </table>   
 {if $contactEditMode}</fieldset>{/if}


{elseif $contact_type eq 'Organization'}

 {if $contactEditMode}<fieldset><legend></legend>{/if}
	<table class="form-layout-compressed">
      {if $relatedOrganizationFound}
      <tr>
		<td>{$form.org_option.html}</td>
      </tr>
      <tr id="select_org">
        <td>{$form.organization_id.html|crmReplace:class:big}</td>
      </tr>
      {/if}  
      <tr id="create_org">
		<td>{$form.organization_name.label}<br/>
            {$form.organization_name.html|crmReplace:class:big}</td>
      </tr>
    </table>
 {if $contactEditMode}</fieldset>{/if}

{/if}

{* Display the address block *}
{assign var=index value=1}

{if $contactEditMode}
  <fieldset><legend>{ts}Phone and Email{/ts}</legend>
    <table class="form-layout-compressed">
		<tr>
            <td width="25%">{$form.phone.$index.phone.label}</td>
            <td>{$form.phone.$index.phone.html}</td>
        </tr>
		<tr>
            <td>{$form.email.$index.email.label}</td>
            <td>{$form.email.$index.email.html}</td>
        </tr>
    </table>
  </fieldset>
{/if}


{if !$contactEditMode}<br/>{/if}

    {if $contactEditMode}<fieldset><legend>{ts}Address{/ts}</legend>{/if}
    <table class="form-layout-compressed">
        {if !$contactEditMode}
		<tr>
            <td>{$form.phone.$index.phone.label}</td>
            <td>{$form.phone.$index.phone.html}</td>
        </tr>
		<tr>
            <td>{$form.email.$index.email.label}</td>
            <td>{$form.email.$index.email.html}</td>
        </tr>
        {/if}
        {if $addressSequence.street_address}
        <tr>
            <td width="15%">{$form.address.$index.street_address.label}</td>
            <td>{$form.address.$index.street_address.html}    
                <br class="spacer"/>
                <span class="description font-italic">{ts}Street number, street name, apartment/unit/suite - OR P.O. box{/ts}</span>
            </td>
        </tr>
        {/if}
        {if $addressSequence.supplemental_address_1}
        <tr>
            <td>{$form.address.$index.supplemental_address_1.label}</td>
            <td>{$form.address.$index.supplemental_address_1.html}    
                <br class="spacer"/>
                <span class="description font-italic">{ts} Supplemental address info, e.g. c/o, department name, building name, etc.{/ts}</span>
            </td>
        </tr>
        {/if}
        {if $addressSequence.supplemental_address_2}
        <tr>
            <td>{$form.address.$index.supplemental_address_2.label}</td>
            <td>{$form.address.$index.supplemental_address_2.html}    
            </td>
        </tr>
        {/if}
        {if $addressSequence.city}
        <tr>
            <td>{$form.address.$index.city.label}</td>
            <td>{$form.address.$index.city.html}</td>
        </tr>
        {/if}
        {if $addressSequence.postal_code}
        <tr>
            <td>{$form.address.$index.postal_code.label}</td>
            <td>{$form.address.$index.postal_code.html}
                {if $form.address.$index.postal_code_suffix.html}
                     - {$form.address.$index.postal_code_suffix.html}    
                    <br class="spacer"/>
                    <span class="description font-italic">{ts}Enter optional 'add-on' code after the dash ('plus 4' code for U.S. addresses).{/ts}</span>
                {/if}
            </td>
        </tr>
        {/if}
        {if $addressSequence.country}
        <tr>
            <td>{$form.address.$index.country_id.label}</td>
            <td>{$form.address.$index.country_id.html}</td>
        </tr>
        {/if}
        {if $addressSequence.state_province}
        <tr>
            <td>{$form.address.$index.state_province_id.label}</td>
            <td>{$form.address.$index.state_province_id.html}</td>
        </tr>
        {/if}
        {if $contactEditMode and $form.location.$index.address.geo_code_1.label}
        <tr>
            <td>{$form.address.$index.geo_code_1.label}, {$form.address.$index.geo_code_2.label}</td>
            <td>{$form.address.$index.geo_code_1.html}, {$form.address.$index.geo_code_2.html}    
                <br class="spacer"/>
                <span class="description font-italic">
                    {ts}Latitude and longitude may be automatically populated by enabling a Mapping Provider.{/ts} {docURL page="Mapping and Geocoding"}</span>
            </td>
        </tr>
        {/if}
    </table>

    {if $contactEditMode}</fieldset>{/if}

</fieldset>

{if $form.is_for_organization}
    {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="is_for_organization"
         trigger_value       ="true"
         target_element_id   ="for_organization" 
         target_element_type ="block"
         field_type          ="radio"
         invert              = "false"
    }
{/if}

{if $relatedOrganizationFound}
    {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="org_option"
         trigger_value       ="true"
         target_element_id   ="select_org" 
         target_element_type ="table-row"
         field_type          ="radio"
         invert              = "true"
    }
    {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="org_option"
         trigger_value       ="true"
         target_element_id   ="create_org" 
         target_element_type ="table-row"
         field_type          ="radio"
         invert              = "false"
    }
{/if}

{literal}
<script type="text/javascript">
{/literal}
{* If mid present in the url, take the required action (poping up related existing contact ..etc) *}
{if $membershipContactID}
{literal}
cj(document).ready( function( ) { cj( '#organization_id' ).val("{/literal}{$membershipContactID}{literal}"); });
{/literal}
{/if}
{* Javascript method to populate the location fields when a different existing related contact is selected *}
{literal}
var dataUrl   = "{/literal}{$employerDataURL}{literal}";
cj('#organization_id').autocomplete( dataUrl, { width : 180, selectFirst : false
                            }).result( function(event, data, formatted) {
                                             cj('#onbehalfof_id').val( data[1]);
                                             var locationUrl = {/literal}"{$locDataURL}"{literal}+ data[1]; 
                                             cj.ajax({
                                                	   url         : locationUrl,
                                                       data        : "{}",
                                                       dataType    : "json",
                                                       timeout     : 5000, //Time in milliseconds
                                                       success     : function( data, status ) {
                                                                               for (var ele in data) {
                                                                                    cj( "#"+ele ).val( data[ele] );
                                                                               }
                                                                      },
                                                       error       : function( XMLHttpRequest, textStatus, errorThrown ) {
                                                                                console.error("HTTP error status: ", textStatus);
                                                                      }
                                                     });
                           });
</script>
{/literal}
