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

        &nbsp; &nbsp; {$form.location.$index.name.label}
        {$form.location.$index.name.html|crmReplace:class:big} 
        {if $locationExists}
            {foreach from=$locationExists item=ltypeid}   
               {if $ltypeid == $form.location.$index.location_type_id.value[0]}
                    {capture assign=deleteLocation}{crmURL p='civicrm/contact/view/delete/location' q="reset=1&action=delete&ltypeid=$ltypeid&cid=$contactId"}{/capture}
                    &nbsp; &nbsp; {ts 1=$deleteLocation} <a href="%1">Delete</a>{/ts}
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

    {* Display the elements for shared address ( for individual ) *}
    {if $contact_type eq 'Individual' and $index eq 1}
        <div class="form-item">
            {$form.use_household_address.html}{$form.use_household_address.label}<br />
            <span class="description">
                {ts}Check this option if you want to use a shared household address for this individual. You can
                either select an existing household, or create a new one.{/ts}
            </span>
        </div>
        <div id="confirm_shared_option" class="form-item">
            {$form.shared_option.html}
        </div>
        <div id="shared_household" class="form-item">
            <span class="labels">
                {$form.shared_household.label}
            </span>
            <span class="fields">
                {$form.shared_household.html}
            </span>
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

    </fieldset>
</div> {* End of Location block div *}
{/section}

{* -- Javascript for showing/hiding the shared household options -- *}
{literal}
<script type="text/javascript">
    function showHideSharedOptions()
    {
        if (document.getElementsByName("use_household_address")[0].checked) {
            if (document.getElementsByName("shared_option")[0].checked) {
                show("create_household");
                hide("shared_household");
                show("id_location_1_address");
            } else {
                hide("create_household");
                show("shared_household");
                hide("id_location_1_address");
            }
        } else {
            hide("create_household");
            hide("shared_household");
            if (document.getElementsByName("shared_option")[1].checked) {
                show("id_location_1_address");
            }
        }
    }
</script>
{/literal}

{if $contact_type EQ 'Individual'}
   {if $action eq 1}
       {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="shared_option"
         trigger_value       =""
         target_element_id   ="shared_household" 
         target_element_type ="block"
         field_type          ="radio"
         invert              = "1"
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
         target_element_id   ="confirm_shared_option" 
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
   {if $form.errors and $form.use_household_address.value}
       {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="shared_option"
         trigger_value       =""
         target_element_id   ="id_location_1_address" 
         target_element_type ="block"
         field_type          ="radio"
         invert              = "0"
       }
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
   {if $action eq 2}
      {include file="CRM/common/enableDisableByFieldValue.tpl" 
         trigger_field_id    ="use_household_address"
         trigger_value       =""
         target_element_id   ="location_1_address_street_address|location_1_address_supplemental_address_1|location_1_address_supplemental_address_2|location_1_address_city|location_1_address_postal_code|location_1_address_postal_code_suffix|location_1_address_county_id|location_1_address_state_province_id|location_1_address_country_id|location_1_address_geo_code_1|location_1_address_geo_code_2" 
         target_element_type ="block"
         field_type          ="radio"
         invert              = 1
      }
   {/if} 
{/if}
