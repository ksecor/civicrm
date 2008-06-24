{* this template used to build location block *}
{include file="CRM/common/WizardHeader.tpl"}
<fieldset>
    <legend>{ts}Pledge Location Information{/ts}</legend>
    <div id="help">
        {ts}Use this form to define optional geographical location of the pledge.{/ts}
    </div>
     <div id="location" class="form-item">
       <span class="labels">
         {$form.has_location.label}
       </span>
       <span class="fields">
         {$form.has_location.html}
       </span>
    </div>
    <div id="location_show" class="form-item">
       <span class="labels">
           <label>{$form.location.1.name.label}</label>
       </span>  
       <span class="fields">
          {$form.location.1.name.html|crmReplace:class:big}
       </span>
    </div>
    <div class="spacer"></div>
    {* Display the address block *}
    {include file="CRM/Contact/Form/Address.tpl"} 
   
</fieldset>
<dl>
    <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>
</dl>
    
{* Include Javascript to hide and display the appropriate blocks as directed by the php code *} 
{*include file="CRM/common/showHide.tpl"*}    
{include file="CRM/common/showHide.tpl"}
{include file="CRM/common/showHideByFieldValue.tpl" 
trigger_field_id    ="has_location"
trigger_value       ="" 
target_element_id   ="location_show" 
target_element_type ="block"
field_type          ="radio"
invert              = 0
}