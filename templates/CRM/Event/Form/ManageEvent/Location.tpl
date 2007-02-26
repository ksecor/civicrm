{* this template used to build location block *}
{include file="CRM/common/WizardHeader.tpl"}
 {section name = locationLoop start = 1 loop = $locationCount} 
<div id="id_location_1_show" class="section-hidden section-hidden-border label">
    {$form.location.1.show.html}{ts}Primary Location{/ts}
 </div>
<div id="id_location_1">
	<fieldset>
<legend>{$form.location.1.hide.html}
        {ts}Primary Location{/ts}
    </legend>
<div class="form-item">
 {$form.location.1.location_type_id.html}
 &nbsp; &nbsp; {$form.location.1.name.label}
 {$form.location.1.name.html|crmReplace:class:big}
</div>
    {* Display the address block *}
    {include file="CRM/Contact/Form/Address.tpl"} 

    {* Display the email block(s) *}  
    {include file="CRM/Contact/Form/Email.tpl" hold=0}

    {* Display the phone block(s) *}
    {include file="CRM/Contact/Form/Phone.tpl"}       
 </fieldset>
{/section}
</div>
    
 <dl>
         <dt></dt><dd>{$form.buttons.html}</dd>
     </dl>

    
{* Include Javascript to hide and display the appropriate blocks as directed by the php code *} 
{include file="CRM/common/showHide.tpl"}    
