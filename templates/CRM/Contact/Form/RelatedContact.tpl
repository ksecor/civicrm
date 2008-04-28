{* This file provides the HTML for the edit Related contact form *}
{* Including the javascript source code from the Contact.js *}
 <script type="text/javascript" src="{$config->resourceBase}js/Contact.js"></script>

<fieldset><legend>{ts}Contact Information{/ts}</legend>
{if $contact_type eq 'Individual'}
 <div id="name">
 <fieldset><legend>{ts}Individual{/ts}</legend>
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
 <fieldset><legend>{ts}Household{/ts}</legend>
   	<table class="form-layout">
    <tr>
		<td>{$form.household_name.label}</td>
    </tr>
    <tr>
        <td>{$form.household_name.html|crmReplace:class:big}</td>
    </tr>
   
 </fieldset>
 </div>


{elseif $contact_type eq 'Organization'}
<div id="name">
 <fieldset><legend>{ts}Organization{/ts}</legend>
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
        {include file="CRM/Contact/Form/Address.tpl"} 
    </div>

{* Include Javascript to hide and display the appropriate blocks as directed by the php code *}
{include file="CRM/common/showHide.tpl"}
 </fieldset>
<div class="crm-submit-buttons">
    {$form.buttons.html}
</div>