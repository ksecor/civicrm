{* this template used to build location block *}
{include file="CRM/common/WizardHeader.tpl"}
<fieldset>
    <legend>{ts}Event Location and Contact Information{/ts}</legend>
    <div id="help">
        {ts}Use this form to configure the location and optional contact information for the event. This information will be displayed on the Event Information page. It will also be included in online registration pages and confirmation emails if these features are enabled.{/ts}
    </div>
    {if $locEvents}
    <div id="optionType" class="form-item">
       <span class="labels">
         <label>{$form.option_type.label}</label>
       </span>
       <span class="fields">
         {$form.option_type.html}
       </span>
    </div>   
    <div class="spacer"></div>
    <div id="existingLoc" class="form-item">
       <span class="labels">
         <label>{$form.loc_event_id.label}</label>
       </span>
       <span class="fields">
         {$form.loc_event_id.html|crmReplace:class:huge}
       </span>
    </div> 
    <div class="spacer"></div>
    {/if}	
    <div id="newLocation">
    {* Display the address block *}
    {include file="CRM/Contact/Form/Address.tpl"} 

    {* Display the email block(s) *}  
    {include file="CRM/Contact/Form/Email.tpl" hold=1}

    {* Display the phone block(s) *}
    {include file="CRM/Contact/Form/Phone.tpl"} 
    </div>

    <div id="showLoc" class="form-item">
       <span class="labels">
         {$form.is_show_location.label}
       </span>
       <span class="fields">
         {$form.is_show_location.html}<br /><span class="description">{ts}Uncheck this box if you want to HIDE the event Address on Event Information and Registration pages as well as on email confirmations.{/ts}
       </span>
    </div>
</fieldset>
<dl>
    <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>
</dl>
    
{* Include Javascript to hide and display the appropriate blocks as directed by the php code *} 
{include file="CRM/common/showHide.tpl"}
{if $useExistingEventLocation and $locEvents}
<script type="text/javascript">    
{literal}
function showLocFields( ) {
   var createNew = document.getElementsByName("option_type")[0].checked;
   var useExisting = document.getElementsByName("option_type")[1].checked;
   if ( createNew ) {
      show('newLocation');
      show('showLoc');
      hide('existingLoc');
   } else if ( useExisting ) {
      hide('newLocation');	
      show('existingLoc');
      show('showLoc');
   } else {
      hide('newLocation');	
      hide('existingLoc');
      hide('showLoc');
   }
}
showLocFields( );
{/literal}
</script>
{/if}