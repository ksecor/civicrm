{* This file provides the HTML for the on-behalf-of form. Can also be used for related contact edit form. *}

<fieldset id="for_organization"><legend>{$fieldSetTitle}</legend>
{if $contact_type eq 'Individual'}
 <div id="name">
  <fieldset><legend></legend>
	<table class="form-layout">
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
  </fieldset>
 </div>

{elseif $contact_type eq 'Household'}
<div id="name">
 <fieldset><legend></legend>
   	<table class="form-layout">
      <tr>
		<td>{$form.household_name.label}</td>
      </tr>
      <tr>
        <td>{$form.household_name.html|crmReplace:class:big}</td>
      </tr>
    </table>   
 </fieldset>
</div>


{elseif $contact_type eq 'Organization'}
<div id="name">
 <fieldset><legend></legend>
	<table class="form-layout">
      {if $relatedOrganizationFound}
      <tr>
		<td>{$form.org_option.html}</td>
      </tr>
      <tr id="select_org">
        <td><span class="tundra" dojoType= "dojo.data.ItemFileReadStore" jsId="employerStore" url="{$employerDataURL}">
            {$form.organization_id.html|crmReplace:class:big}</span>
        </td>
      </tr>
      {/if}  
      <tr id="create_org">
		<td>{$form.organization_name.label}<br/>
            {$form.organization_name.html|crmReplace:class:big}</td>
      </tr>
    </table>
 </fieldset>
</div>
{/if}

{* Display the address block *}
{assign var=index value=1}

<div id="id_location_{$index}_phone">
  <fieldset><legend>{ts}Phone and Email{/ts}</legend>  
  	<table class="form-layout">
    <tr>
		<td>{$form.location.$index.phone.1.phone.label}</td>
        <td>{$form.location.$index.phone.1.phone.html}</td>  
    </tr>
    <tr>
		<td>{$form.location.$index.email.1.email.label}</td>
        <td>{$form.location.$index.email.1.email.html}</td>  
    </tr>
    </table>
  </fieldset>
</div>

<div id="id_location_{$index}_address">
    <fieldset><legend>{ts}Address{/ts}</legend>
      {foreach item=addressElement from=$addressSequence}
          {include file=CRM/Contact/Form/Address/$addressElement.tpl}
      {/foreach}

      {* Special block for country & state implemented using new hier-select widget *}  
      {if $addressSequenceCountry}  
        <div id="id_location_{$index}_address_country" class="form-item">
        <span class="labels">{ts}Country{/ts}</span>
        <span class="fields">
            {$form.location.1.address.country_state.html}
            <br class="spacer"/>
            <span class="description font-italic">
                {ts}Type in the first few letters of the country and then select from the drop-down. After selecting a country, the State / Province field provides a choice of states or provinces in that country.{/ts}
            </span>
        </span>
        </div>
      {/if}

      {if $addressSequenceState}  
        <div id="id_location_{$index}_address_state" class="form-item">
        <span class="labels">{ts}State / Province{/ts}</span>
        <span class="tundra fields"><span id="id_location[1][address][country_state]_1"></span>
            <br class="spacer"/>
            <span class="description font-italic">
                {ts}Type in the first few letters of the country and then select from the drop-down. After selecting a country, the State / Province field provides a choice of states or provinces in that country.{/ts}
            </span>
        </span>
        </div>
      {/if}  

      {if $contactEditMode}  
          {include file=CRM/Contact/Form/Address/geo_code.tpl}
      {/if}

      <!-- Spacer div forces fieldset to contain floated elements -->
      <div class="spacer"></div>
    </fieldset>
</div>

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


{* If mid present in the url, take the required action (poping up related existing contact ..etc) *}
{if $membershipContactID}
<script type="text/javascript">
   dojo.addOnLoad( function( ) {ldelim}
   dijit.byId( 'organization_id' ).setValue("{$membershipContactID}");
   {rdelim} );
</script>
{/if}

{* Javascript method to reset the location fields when a different existing related contact is selected *}
{if $membershipContactID}
{literal}
<script type="text/javascript">
    function resetLocation( val ) {
        var membershipContactID = {/literal}{$membershipContactID}{literal};
        var selectedVal = dijit.byId( 'organization_id' ).getValue();
        if ( val == '0' ) {
            selectedVal = "";
        }

        if ( selectedVal != membershipContactID ) {
            var fields = new Array('location_1_phone_1_phone', 
                                   'location_1_address_street_address', 
                                   'location_1_address_supplemental_address_1', 
                                   'location_1_address_supplemental_address_2', 
                                   'location_1_address_city', 
                                   'location_1_address_postal_code', 
                                   'location_1_address_postal_code_suffix',
                                   'location_1_address_geo_code_1',
                                   'location_1_address_geo_code_2');

            for ( var i in fields ) {
                eval('document.getElementById(fields[i]).value = ""');
            }
            dijit.byId("id_location[1][address][country_state]_0").setValue("");
            dijit.byId("id_location[1][address][country_state]_1").setValue("");
            var indEmail = "email-" + {/literal}{$bltID}{literal};
            if ( document.getElementById(indEmail).value != document.getElementById('location_1_email_1_email').value ) {
                document.getElementById('location_1_email_1_email').value = document.getElementById(indEmail).value;
            }
        }
    }
</script>
{/literal}
{/if}
