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
         <label>{$form.location_option.label}</label>
       </span>
       <span class="fields">
         {$form.location_option.html}
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

    {assign var=locUsedMsgTxt value="<strong>Note:</strong> This location is used by multiple events. Modifying location information will change values for all events."}
    <div id="locUsedMsg"></div>

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
{if $locEvents}
<script type="text/javascript">    
{literal}
var locUsedMsgTxt = {/literal}"{$locUsedMsgTxt}"{literal};
var locBlockURL   = {/literal}"{crmURL p='civicrm/ajax/locBlock' q="reset=1"}"{literal};
var locBlockId    = {/literal}"{$form.loc_event_id.value.0}"{literal};

if ( {/literal}"{$locUsed}"{literal} ) {
   displayMessage( true );
}

cj(document).ready(function() {
  cj('#loc_event_id').change(function() {
    cj.ajax({
      url: locBlockURL, 
      type: 'POST',
      data: {'lbid': cj(this).val()},
      dataType: 'json',
      success: function(data) {
        var selectLocBlockId = cj('#loc_event_id').val();
        for(i in data) {
          if ( i == 'count_loc_used' ) {
            if ( ((selectLocBlockId == locBlockId) && data['count_loc_used'] > 1) || 
                 ((selectLocBlockId != locBlockId) && data['count_loc_used'] > 0) ) {
              displayMessage( true );
            } else {
              displayMessage( false );
            }
          } else {
            document.getElementById( i ).value = data[i];
          }
        }
      }
    });
    return false;
  });
});

function displayMessage( set ) {
   cj(document).ready(function() {
     if ( set ) {
       cj('#locUsedMsg').html( locUsedMsgTxt ).addClass('status');
     } else {
       cj('#locUsedMsg').html( ' ' ).removeClass('status');
     }
   });
}

function showLocFields( ) {
   var createNew = document.getElementsByName("location_option")[0].checked;
   var useExisting = document.getElementsByName("location_option")[1].checked;
   if ( createNew ) {
     hide('existingLoc');
     displayMessage(false);
   } else if ( useExisting ) {
     show('existingLoc');
   }
}

showLocFields( );
{/literal}
</script>
{/if}
