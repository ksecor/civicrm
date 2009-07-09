{* this template used to build location block *}
<div class="crm-submit-buttons">
    {$form.buttons.html}
</div>
<fieldset>
    <div id="help">
        {ts}Use this form to configure the location and optional contact information for the event. This information will be displayed on the Event Information page. It will also be included in online registration pages and confirmation emails if these features are enabled.{/ts}
    </div>
    {if $locEvents}
    	<table class="form-layout-compressed">
			<tr id="optionType">
				<td class="labels">
					{$form.location_option.label}
				</td>
				{foreach from=$form.location_option key=key item =item}
					{if $key|is_numeric}
						<td class="fields"><strong>{$item.html}</strong></td>
				    {/if}
                {/foreach} 
				</td>
			 </tr>
			<tr id="existingLoc">
				<td class="labels">
					{$form.loc_event_id.label}
				</td>
				<td class="fields" colspan=2>
					{$form.loc_event_id.html|crmReplace:class:huge}
				</td>
			</tr>
			<tr>
				<td id="locUsedMsg" colspan="2">
				{assign var=locUsedMsgTxt value="<strong>Note:</strong> This location is used by multiple events. Modifying location information will change values for all events."}
				</td>
			</tr>
			
		</table>
    {/if}	

    

    <div id="newLocation">
	<fieldset><legend>Address</legend>
		{* Display the address block *}
		{include file="CRM/Contact/Form/Edit/Address.tpl" blockId=1 title='' defaultLocation=1} 
	</fieldset>
	<table class="form-layout-compressed">
    {* Display the email block(s) *}  
    {include file="CRM/Contact/Form/Edit/Email.tpl"  blockId=1 hold=1 defaultLocation=1}

    {* Display the phone block(s) *}
    {include file="CRM/Contact/Form/Edit/Phone.tpl" blockId=1 defaultLocation=1} 
    </table>
	 <table class="form-layout-compressed">
	 <tr>
		<td colspan="2">{$form.is_show_location.label}</td>
		<td colspan="2">
			{$form.is_show_location.html}<br />
			<span class="description">{ts}Uncheck this box if you want to HIDE the event Address on Event Information and Registration pages as well as on email confirmations.{/ts}
		</td>
	</tr>
	</table>
</fieldset>
<div class="crm-submit-buttons">
    {$form.buttons.html}
</div>
    
{* Include Javascript to hide and display the appropriate blocks as directed by the php code *} 
{*include file="CRM/common/showHide.tpl"*}
{if $locEvents}
<script type="text/javascript">    
{literal}
var locUsedMsgTxt = {/literal}"{$locUsedMsgTxt}"{literal};
var locBlockURL   = {/literal}"{crmURL p='civicrm/ajax/locBlock' q='reset=1' h=0}"{literal};
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
