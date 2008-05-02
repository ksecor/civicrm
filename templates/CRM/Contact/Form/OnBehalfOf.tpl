{* This file provides the HTML for the on-behalf-of form. Can also be used for related contact edit form. *}

{* Including the javascript source code from the Contact.js *}
<script type="text/javascript" src="{$config->resourceBase}js/Contact.js"></script>

<fieldset><legend>{$fieldSetTitle}</legend>
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
    {include file="CRM/Contact/Form/Address.tpl"} 
</div>

</fieldset>
