{* This file provides the templating for the Location block *}
{* The phone, Email, Instant messenger and the Address blocks have been plugged in from external source files*}

{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var $locationCount contains the max number of locations to be displayed, assigned in the Location.php file*}
{* @var $index contains the current index of the location section *}

 {section name = locationLoop start = 1 loop = $locationCount}
 {assign var=index value=$smarty.section.locationLoop.index}

 <div id="id_location_{$index}_show" class="section-hidden section-hidden-border label">
    {$form.location.$index.show.html}{if $index EQ 1}{ts}Primary Location{/ts}{else}{ts}Additional Location{/ts}{/if}
 </div>
<div id="id_location_{$index}">
	<fieldset>
    <legend>{$form.location.$index.hide.html}
        {if $index EQ 1}{ts}Primary Location{/ts}{else}{ts}Additional Location{/ts}{/if}
    </legend>
    <div class="form-item">
        {* Location type drop-down (e.g. Home, Work...) *}
        {$form.location.$index.location_type_id.html}

        {* Checkbox for "make this the primary location" *}
        {$form.location.$index.is_primary.html}
    
        {* Checkbox for "make this the billing location" *}
        {$form.location.$index.is_billing.html}

        {if $locationExists}
            {foreach from=$locationExists item=ltypeid}   
               {if $ltypeid == $form.location.$index.location_type_id.value[0]}
                    {capture assign=deleteLocation}{crmURL p='civicrm/contact/view/delete/location' q="reset=1&action=delete&ltypeid=$ltypeid&cid=$contactId"}{/capture}
                    &nbsp; &nbsp; <a href="{$deleteLocation}">{ts}Delete{/ts}</a>
               {/if} 
            {/foreach}
        {/if}
        
    </div>

    {* Display the phone block(s) *}
    {include file="CRM/Contact/Form/Phone.tpl"}

    {* Display the email block(s) *}
    {include file="CRM/Contact/Form/Email.tpl"}

    {* Display the instant messenger block(s) *}
    {if $showIM}
    {include file="CRM/Contact/Form/IM.tpl"}
    {/if}

    {* Display the openid block(s) *}
    {if $showOpenID}
    {include file="CRM/Contact/Form/OpenID.tpl"}
    {/if}
    {* Display the elements for shared address ( for individual ) *}
    {if $contact_type eq 'Individual' and $index eq 1}
        <div class="form-item">
            {$form.use_household_address.html}{$form.use_household_address.label}<br />
            <span class="description">
                {ts}Check this option if you want to use a shared household address for this individual. You can either select an existing household, or create a new one.{/ts}
            </span>
        </div>
        <div id="shared_household" class="form-item">
            <div>
                {$form.shared_household.label}
            </div>
            <div class="tundra" dojoType= "dojox.data.QueryReadStore" jsId="addressStore" url="{$dataURL}" doClientPaging="false">
                {$form.shared_household.html}
                {* Conditionally display the address currently selected in the comboBox *}
                <span id="shared_household_address" class="description"></span>
            </div>
            <span id="shared_household_help" class="description">{ts}Enter the first letters of the name of the household to see available households. If the household doesn't exist yet, type in the new household name.{/ts}</span> 
        </div>
      
        {* -- Spacer div contains floated elements -- *}
        <div class="spacer"></div>
    {/if}

    {* Display the address block *}
    <div id="id_location_{$index}_address">
        {include file="CRM/Contact/Form/Address.tpl"} 
    </div>

    {* Display existing shared household address *}
    {if $contact_type eq 'Individual' and $index eq 1 and $action eq 2 and $form.use_household_address.value and $location_1_address_display}
        <div id="id_location_1_address_shared_view">
        <fieldset><legend>{ts}Shared Household Address{/ts}</legend>
            {$HouseholdName}
            {$location_1_address_display}
        </fieldset>
        </div>
    {/if}


    </fieldset>
</div> {* End of Location block div *}
{/section}

{* -- Javascript for showing/hiding the shared household options -- *}
{literal}
<script type="text/javascript">
    
 function showSelectedAddress( val )
 {
    var help = val+'_help';
    var address = val+'_address';
    if ( document.getElementsByName("use_household_address")[0].checked == true ) {
	show('shared_household', 'block');
	hide('id_location_1_address');
    }
    var contactId = dijit.byId(val).getValue();
    if ( isNaN( contactId ) ) {
	document.getElementById(address).innerHTML = {/literal}"({ts}New Contact Record{/ts})"{literal};
	if ( val == 'shared_household' ) {
	    show('shared_household', 'block');
	    show('id_location_1_address', 'block');	   
	}	
	return; 
    }

    if ( val == 'shared_household' ) {		
	var dataUrl = {/literal}"{crmURL p='civicrm/ajax/search' h=0 q='sh=1&id='}"{literal} + contactId;
    } else {
	var dataUrl = {/literal}"{crmURL p='civicrm/ajax/search' h=0 q='sh=2&id='}"{literal} + contactId;
    }

    dojo.xhrGet( { 
	        url: dataUrl, 
		handleAs: "text",
		timeout: 5000, // Time in milliseconds
		
		// The LOAD function will be called on a successful response.
		load: function(response, ioArgs) {
		var selectedAddr = response;
		
		if ( selectedAddr != "" ) {
		    var ind = selectedAddr.indexOf(':::');
		    if ( ind < 0){
			var formattedAddr = '';	
		    } else {
			selectedAddr = selectedAddr.substr(ind+3);
			var formattedAddr = selectedAddr.replace(/:::/g, ", ");
		    }
		    document.getElementById(address).innerHTML = formattedAddr;		

		} else {
		    document.getElementById(address).innerHTML = '';	
		}
		return response; 
	    },
		
		// The ERROR function will be called in an error case.
		error: function(response, ioArgs) { 
		    console.error("HTTP status code: ", ioArgs.xhr.status); 
		    return response; 
		}
	});
 }

function setDefaultAddress()
{
    var country   = {/literal}"{$form.location.1.address.country_id.value.0}"{literal};
    var state     = {/literal}"{$form.location.1.address.state_province_id.value.0}"{literal};
    
   
    {/literal}{if $action eq 2}
    {foreach from=$form.location.1.address  key=k item=v}
    {literal}      
    document.getElementById('location_1_address_{/literal}{$k}{literal}').value ={/literal}"{$v.value}";
    {/foreach}
    {if $form.location.1.address.county_id}
    {literal}
    document.getElementById('location_1_address_county_id').value = 
	{/literal}"{$form.location.1.address.county_id.value.0}"{literal};
    {/literal}
    {/if}
    {literal} 
    if ( country ) {
	document.getElementById('location_1_address_country_id').value = country;
    }
    if ( state && country) {
	document.getElementById('location_1_address_state_province_id').value = state;
    } else if ( state ) {
	document.getElementById('location_1_address_state_province_id').value = 
	    {/literal}"{$form.location.1.address.state_province_id.value.0}"{literal};
    }
    
    {/literal}{/if}{literal}
}


function showHideHouseAddress( )
{
    if ( document.getElementsByName("use_household_address")[0].checked == true ) { 
	show('shared_household', 'block');
	{/literal}{if !$form.errors}{literal}
	hide('id_location_1_address');
	{/literal}{/if}
	{if $location_1_address_display}{literal}
	show('id_location_1_address_shared_view', 'block');
	{/literal}{/if}{literal}
    } else {
	show('id_location_1_address', 'block');
	hide('shared_household');
	{/literal}{if $location_1_address_display}{literal}
	hide('id_location_1_address_shared_view');
	{/literal}{/if}{literal}
    }
}

function showHideAddress( )
{
    if ( document.getElementsByName("use_household_address")[0].checked == true ) { 
	show('shared_household', 'block');
	{/literal}{if !$form.errors}{literal}
	hide('id_location_1_address');
	{/literal}{/if}{literal}
    } else {
	show('id_location_1_address', 'block');
	hide('shared_household');
    }
}

{/literal}
{if $contact_type eq 'Individual' and $defaultSharedHousehold}
{literal}
dojo.addOnLoad( function( )
{ 
    var sharedHHId = "{/literal}{$defaultSharedHousehold}{literal}";
    dijit.byId('shared_household').setValue( sharedHHId );
    
} );	 
{/literal}
{/if}
{literal}

</script>
{/literal}
{if $contact_type EQ 'Individual'}
{if $form.use_household_address.value}
{include file="CRM/common/showHideByFieldValue.tpl" 
     trigger_field_id    ="use_household_address"
     trigger_value       =""
     target_element_id   ="shared_household" 
     target_element_type ="block"
     field_type          ="radio"
     invert              = "1"
     }
{include file="CRM/common/showHideByFieldValue.tpl" 
     trigger_field_id    ="use_household_address"
     trigger_value       =""
     target_element_id   ="id_location_1_address" 
     target_element_type ="block"
     field_type          ="radio"
     invert              = "1"
     }

{else}
{include file="CRM/common/showHideByFieldValue.tpl" 
     trigger_field_id    ="use_household_address"
     trigger_value       =""
     target_element_id   ="shared_household" 
     target_element_type ="block"
     field_type          ="radio"
     invert              = "0"
     }
{/if} 
{if $form.errors and $form.use_household_address.value}
{if $isshareHouseholdNew}
{include file="CRM/common/showHideByFieldValue.tpl" 
     trigger_field_id    ="use_household_address"
     trigger_value       =""
     target_element_id   ="id_location_1_address" 
     target_element_type ="block"
     field_type          ="radio"
     invert              = "0"
     }
{/if}
{if $action eq 2 and $location_1_address_display}
{include file="CRM/common/showHideByFieldValue.tpl" 
     trigger_field_id    ="use_household_address"
     trigger_value       =""
     target_element_id   ="id_location_1_address_shared_view" 
     target_element_type ="block"
     field_type          ="radio"
     invert              = "0"
     }
{/if} 
{include file="CRM/common/showHideByFieldValue.tpl" 
     trigger_field_id    ="use_household_address"
     trigger_value       =""
     target_element_id   ="shared_household" 
     target_element_type ="block"
     field_type          ="radio"
     invert              = "0"
     }
{/if}
{/if}
