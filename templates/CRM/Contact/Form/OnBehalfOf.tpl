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
      <tr>
		<td>{$form.organization_name.label}</td>
      </tr>
      <tr>
        <td>{$form.organization_name.html|crmReplace:class:big}</td>
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
    {*include file="CRM/Contact/Form/Address.tpl"*} 
    <fieldset><legend>{if $legend}{$legend}{else}{ts}Address{/ts}{/if}</legend>
      {if $introText}
          <div class="description">{$introText}</div>
      {/if}
      {foreach item=addressElement from=$addressSequence}
        <span id="id_location_{$index}_address_{$addressElement}">
            {include file=CRM/Contact/Form/Address/$addressElement.tpl}
        </span>
      {/foreach}

      {* Special block for country & state implemented using new hier-select widget *}  
      {if $addressSequenceCountry}  
        <span id="id_location_{$index}_address_country">
        <div class="form-item">
        <span class="labels">{ts}Country{/ts}</span>
        <span class="fields">
            {$form.location.1.address.country_state.html}
            <br class="spacer"/>
            <span class="description font-italic">
                {ts}Type in the first few letters of the country and then select from the drop-down. After selecting a country, the State / Province field provides a choice of states or provinces in that country.{/ts}
            </span>
        </span>
        </div>
        </span>
      {/if}

      {if $addressSequenceState}  
        <span id="id_location_{$index}_address_state">
        <div class="form-item">
        <span class="labels">{ts}State / Province{/ts}</span>
        <span class="tundra fields"><span id="id_location[1][address][country_state]_1"></span>
            <br class="spacer"/>
            <span class="description font-italic">
                {ts}Type in the first few letters of the country and then select from the drop-down. After selecting a country, the State / Province field provides a choice of states or provinces in that country.{/ts}
            </span>
        </span>
        </div>
        </span>
      {/if}  

      {include file=CRM/Contact/Form/Address/geo_code.tpl}

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
