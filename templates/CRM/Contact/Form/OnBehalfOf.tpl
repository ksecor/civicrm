{* This file provides the HTML for the on-behalf-of form. Can also be used for related contact edit form. *}

<fieldset id="for_organization"><legend>{$fieldSetTitle}</legend>
{if $contact_type eq 'Individual'}
 <div id="name">
  {if $contactEditMode}<fieldset><legend></legend>{/if}
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
  {if $contactEditMode}</fieldset>{/if}
 </div>

{elseif $contact_type eq 'Household'}
<div id="name">
 {if $contactEditMode}<fieldset><legend></legend>{/if}
   	<table class="form-layout">
      <tr>
		<td>{$form.household_name.label}</td>
      </tr>
      <tr>
        <td>{$form.household_name.html|crmReplace:class:big}</td>
      </tr>
    </table>   
 {if $contactEditMode}</fieldset>{/if}
</div>


{elseif $contact_type eq 'Organization'}
<div id="name">
 {if $contactEditMode}<fieldset><legend></legend>{/if}
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
 {if $contactEditMode}</fieldset>{/if}
</div>
{/if}

{* Display the address block *}
{assign var=index value=1}

{if !$contactEditMode}<br/>{/if}

<div id="id_location_{$index}_phone">
  {if $contactEditMode}<fieldset><legend>{ts}Phone and Email{/ts}</legend>{/if}
    <div class="form-item">
		<span class="labels">{$form.location.$index.phone.1.phone.label}</span>
        <span class="fields">{$form.location.$index.phone.1.phone.html}</span>  
    </div>
    <div class="form-item">
		<span class="labels">{$form.location.$index.email.1.email.label}</span>
        <span class="fields">{$form.location.$index.email.1.email.html}</span>
    </div>
  {if $contactEditMode}</fieldset>{/if}
</div>

{if !$contactEditMode}<br/>{/if}

<div id="id_location_{$index}_address">
    {if $contactEditMode}<fieldset><legend>{ts}Address{/ts}</legend>{/if}
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
    {if $contactEditMode}</fieldset>{/if}
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

{* Javascript method to populate the location fields when a different existing related contact is selected *}
{literal}
<script type="text/javascript">
    function loadLocationData( cid ) {
	    var dataUrl = {/literal}"{$locDataURL}"{literal};
        dataUrl = dataUrl + cid;

        var result = dojo.xhrGet({
        url: dataUrl,
        handleAs: "text",
        timeout: 5000, //Time in milliseconds

        // The LOAD function will be called on a successful response.
        load: function(response, ioArgs) {
            var fldVal = response.split(";;");
            for (var i in fldVal) {
                var elem = fldVal[i].split('::');
                if ( elem[0] == 'id_location[1][address][country_state]_0' ) {
                    var countryState = elem[1].split('-');
                    var country = countryState[0];
                    var state   = countryState[1];

                    var selector1 = dijit.byId( elem[0] );
                    var selector2 = dijit.byId( 'id_location[1][address][country_state]_1' );
    
                    selector1.store.fetch({
                        query: {},
                        onComplete: function(items, request) {
                            selector1.setValue(country);
                            selector2.store.fetch({
                                query: {id:state},
                                onComplete: function(items, request) {
                                    selector2.setValue(state);
                                }
                            });
                        }
                    });
                } else if (elem[0]) {
                    document.getElementById( elem[0] ).value = elem[1];
                }
            }
        },

        // The ERROR function will be called in an error case.
        error: function(response, ioArgs) {
            console.error("HTTP status code: ", ioArgs.xhr.status);
        }
     });
    }
</script>
{/literal}
