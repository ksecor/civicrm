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
    {include file="CRM/Contact/Form/IM.tpl"}

    {* Display the openid block(s) *}
    {include file="CRM/Contact/Form/OpenID.tpl"}

    {* Display the elements for shared address ( for individual ) *}
    {if $contact_type eq 'Individual' and $index eq 1}
        <div class="form-item">
            {$form.use_household_address.html}{$form.use_household_address.label}<br />
            <span class="description">
                {ts}Check this option if you want to use a shared household address for this individual. You can either select an existing household, or create a new one.{/ts}
            </span>
        </div>
        <div id="confirm_shared_option" class="form-item">
            {$form.shared_option.html}
        </div>
        <div id="shared_household" class="form-item">
            <div  class="tundra" dojoType= "dojox.data.QueryReadStore" jsId="addressStore" url="{$dataURL}" doClientPaging="false" >
            {$form.shared_household.html}
            </div>
            <span class="description">{ts}Enter the first letters of the name of the household to see available households with their addresses.{/ts}</span> 
        </div>
        <div id="create_household" class="form-item">
            <span class="labels">
                {$form.create_household.label}
            </span>
            <span class="fields">
                {$form.create_household.html}
            </span>
        </div>
        {* -- Spacer div contains floated elements -- *}
        <div class="spacer"></div>
    {/if}

    {* Display the address block *}
    <div id="id_location_{$index}_address">
        {include file="CRM/Contact/Form/Address.tpl"} 
    </div>

    {* Display the address block in view-mode *}
    {if $contact_type eq 'Individual' and $index eq 1}
    <div id="Hhaddress" style="display:none"> <fieldset><legend>{ts}Household Address{/ts}</legend><div id="household_address">
       
       </div></fieldset></div>
       {/if}
       {if $contact_type eq 'Individual' and $index eq 1 and $action eq 2 and $form.use_household_address.value}
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
{/literal}{if $contact_type EQ 'Individual' AND $action eq 2 AND !$form.errors}{literal}
    document.getElementsByName("shared_option")[1].checked = true; 
{/literal}{/if}{literal}

    function showHideSharedOptions()
    {
        if (document.getElementsByName("use_household_address")[0].checked) {
            if (document.getElementsByName("shared_option")[0].checked) {
                show("create_household");
                hide("shared_household");
                show("id_location_1_address");
                {/literal}{if $action eq 2 AND $old_mail_to_household_id}{literal}
                    hide("id_location_1_address_shared_view");
                {/literal}
		{/if}{literal}
            } else {
                hide("create_household");
                show("shared_household");
                hide("id_location_1_address");
                {/literal}{if $action eq 2 AND $old_mail_to_household_id}{literal}
                    show("id_location_1_address_shared_view");
                {/literal}{/if}{literal}
            }
        } else {
            {/literal}{if $action eq 2 AND $old_mail_to_household_id}{literal}
                hide("id_location_1_address_shared_view");
            {/literal}{/if}{literal}
            hide("create_household");
            hide("shared_household");
            if (document.getElementsByName("shared_option")[1].checked) {
                show("id_location_1_address");
            }
        }
    }


 function showSharedHouseholdAddress()
 {
     var text = dijit.byId('shared_household').getValue();
     var ind = text.indexOf(':::');
     var Household_addr;
     Household_addr ='';
     text = text.substr(ind);
     var formatted_addr = text.split(":::",8);	
     for (var i = 0; i < 8; i++){      
       
       if (i == 3 ){
	 Household_addr = Household_addr   + formatted_addr[i] + ',';
       }else if (i == 4){
	 Household_addr = Household_addr   + ' ' +  formatted_addr[i] + ' ';
       } else {
	 Household_addr   = Household_addr + formatted_addr[i] + '<br>';	 
       }
          
     }
if ( formatted_addr != "" ) {
     document.getElementById('Hhaddress').style.display='Block';
     document.getElementById('household_address').innerHTML = Household_addr;	
  }
 }




function setDefaultAddress()
{
  var country   = {/literal}"{$country}"{literal};
  var state     = {/literal}"{$state}"{literal};

  if ( document.getElementsByName("use_household_address")[0].checked == false ) {
 
    {/literal}{if $action eq 2 AND $old_mail_to_household_id}{literal}
       var street    = {/literal}"{$form.location.1.address.street_address.value}"{literal};
       var suppl1    = {/literal}"{$form.location.1.address.supplemental_address_1.value}"{literal};
       var suppl2    = {/literal}"{$form.location.1.address.supplemental_address_2.value}"{literal};
       var city      = {/literal}"{$form.location.1.address.city.value}"{literal};
       var postCode  = {/literal}"{$form.location.1.address.postal_code.value}"{literal};
       var postCodeSuffix   = {/literal}"{$form.location.1.address.postal_code_suffix.value}"{literal};
       var geoCode1  = {/literal}"{$form.location.1.address.geo_code_1.value}"{literal};
       var geoCode2  = {/literal}"{$form.location.1.address.geo_code_2.value}"{literal};

       
       document.getElementById('location_1_address_street_address').value = street;
       document.getElementById('location_1_address_supplemental_address_1').value = suppl1;
       document.getElementById('location_1_address_supplemental_address_2').value = suppl2;
       document.getElementById('location_1_address_city').value = city;
       document.getElementById('location_1_address_postal_code').value = postCode;
       document.getElementById('location_1_address_postal_code_suffix').value = postCodeSuffix;
       document.getElementById('location_1_address_geo_code_1').value = geoCode1;
       document.getElementById('location_1_address_geo_code_2').value = geoCode2;
       dijit.byId( 'location_1_address_country_id' ).setDisplayedValue( country );
       dijit.byId( 'location_1_address_state_province_id' ).setDisplayedValue( state );
     {/literal}{/if}{literal}
   } else {
        {/literal}{if $action eq 1}{literal}
                 document.getElementsByName("shared_option")[1].checked = true; 
        {/literal}{/if}{literal}
   }  
}
function setAddressFields () 
{ var country   = {/literal}"{$country}"{literal};
  if (document.getElementsByName("shared_option")[0].checked) {
    if ( document.getElementsByName("use_household_address")[0].checked == true ) {

       document.getElementById('location_1_address_street_address').value = '';
       document.getElementById('location_1_address_supplemental_address_1').value = '';
       document.getElementById('location_1_address_supplemental_address_2').value = '';
       document.getElementById('location_1_address_city').value = '';
       document.getElementById('location_1_address_postal_code').value = '';
       document.getElementById('location_1_address_postal_code_suffix').value = '';
       document.getElementById('location_1_address_geo_code_1').value = '';
       document.getElementById('location_1_address_geo_code_2').value = '';
       dijit.byId( 'location_1_address_country_id' ).setDisplayedValue( country );
       dijit.byId( 'location_1_address_state_province_id' ).setDisplayedValue( '- type first letter(s) -' ); 
     }
  }
}

{/literal}{if $action eq 2 AND $old_mail_to_household_id}{literal}	 
dojo.connect( dijit.byId('shared_household'), 'onload', 'setHouse')
function setHouse ( ) 
{
 
      	 var houseHoldName  = {/literal}"{$HouseholdName}" {literal};
       	 dijit.byId('shared_household').setDisplayedValue( houseHoldName );
      
}
dojo.connect( dijit.byId('shared_household'), 'onsubmit', 'getHouse')
function getHouse ( ) 
{
   document.Edit.HhName.value = dijit.byId('shared_household').getDisplayedValue( );

} 
{/literal}
{/if}
{literal}

</script>
{/literal}
{if $contact_type EQ 'Individual'}
   {if $form.use_household_address.value}
       {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="shared_option"
         trigger_value       =""
         target_element_id   ="shared_household" 
         target_element_type ="block"
         field_type          ="radio"
         invert              = "1"
       }
       {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="shared_option"
         trigger_value       =""
         target_element_id   ="create_household" 
         target_element_type ="block"
         field_type          ="radio"
         invert              = "0"
       }
       {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="shared_option"
         trigger_value       =""
         target_element_id   ="id_location_1_address" 
         target_element_type ="block"
         field_type          ="radio"
         invert              = "0"
       }
   {else}
       {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="use_household_address"
         trigger_value       =""
         target_element_id   ="confirm_shared_option" 
         target_element_type ="block"
         field_type          ="radio"
         invert              = "0"
       }
       {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="use_household_address"
         trigger_value       =""
         target_element_id   ="create_household" 
         target_element_type ="block"
         field_type          ="radio"
         invert              = "0"
       }
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
       {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="shared_option"
         trigger_value       =""
         target_element_id   ="id_location_1_address" 
         target_element_type ="block"
         field_type          ="radio"
         invert              = "0"
       }
       {if $action eq 2} 
           {include file="CRM/common/showHideByFieldValue.tpl" 
             trigger_field_id    ="shared_option"
             trigger_value       =""
             target_element_id   ="id_location_1_address_shared_view" 
             target_element_type ="block"
             field_type          ="radio"
             invert              = "1"
           }
       {/if} 
       {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="shared_option"
         trigger_value       =""
         target_element_id   ="shared_household" 
         target_element_type ="block"
         field_type          ="radio"
         invert              = "1"
       }
       {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="shared_option"
         trigger_value       =""
         target_element_id   ="create_household" 
         target_element_type ="block"
         field_type          ="radio"
         invert              = "0"
       }
   {/if}
{/if}
