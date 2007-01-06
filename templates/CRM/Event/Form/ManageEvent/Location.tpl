{* this template used to build location block *}
{include file="CRM/common/WizardHeader.tpl"}

<div class="form-item">
       {include file="CRM/Contact/Form/Location.tpl"}
     <dl>
         <dt></dt><dd>{$form.buttons.html}</dd>
     </dl>
</div>
    
{* Include Javascript to hide and display the appropriate blocks as directed by the php code *} 
{include file="CRM/common/showHide.tpl"}    
