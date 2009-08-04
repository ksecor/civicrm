{* this template is used for adding/editing other (custom) activities. *}
{if $cdType }
   {include file="CRM/Custom/Form/CustomData.tpl"}
{elseif $atypefile }
   {if $activityTypeFile}{include file="CRM/{$crmDir}/Form/Activity/$activityTypeFile.tpl"}{/if}
{else}

{* added onload javascript for source contact*}
{literal}
<script type="text/javascript">
var target_contact = assignee_contact = '';

{/literal}
{foreach from=$target_contact key=id item=name}
     {literal} target_contact += '{"name":"'+{/literal}"{$name}"{literal}+'","id":"'+{/literal}"{$id}"{literal}+'"},';{/literal}
{/foreach}
{literal} eval( 'target_contact = [' + target_contact + ']'); {/literal}

{if $assigneeContactCount}
{foreach from=$assignee_contact key=id item=name}
     {literal} assignee_contact += '{"name":"'+{/literal}"{$name}"{literal}+'","id":"'+{/literal}"{$id}"{literal}+'"},';{/literal}
{/foreach}
{literal} eval( 'assignee_contact = [' + assignee_contact + ']'); {/literal}
{/if}
{literal}

cj(document).ready( function( ) {
{/literal}
{if $source_contact and $admin and $action neq 4} 
{literal} cj( '#source_contact_id' ).val( "{/literal}{$source_contact}{literal}");{/literal}
{/if}
{literal}

eval( 'tokenClass = { tokenList: "token-input-list-facebook", token: "token-input-token-facebook", tokenDelete: "token-input-delete-token-facebook", selectedToken: "token-input-selected-token-facebook", highlightedToken: "token-input-highlighted-token-facebook", dropdown: "token-input-dropdown-facebook", dropdownItem: "token-input-dropdown-item-facebook", dropdownItem2: "token-input-dropdown-item2-facebook", selectedDropdownItem: "token-input-selected-dropdown-item-facebook", inputToken: "token-input-input-token-facebook" } ');

var sourceDataUrl = "{/literal}{$dataUrl}{literal}";
var hintText = "{/literal}{ts}Type in a partial or complete name or email{/ts}{literal}";
cj( "#target_contact_id"  ).tokenInput( sourceDataUrl, { prePopulate: target_contact,   classes: tokenClass, hintText: hintText });
cj( "#assignee_contact_id").tokenInput( sourceDataUrl, { prePopulate: assignee_contact, classes: tokenClass, hintText: hintText });

cj('#source_contact_id').autocomplete( sourceDataUrl, { width : 180, selectFirst : false, hintText: hintText
                            }).result( function(event, data, formatted) { cj( "#source_contact_qid" ).val( data[1] );
                            }).bind( 'click', function( ) { cj( "#source_contact_qid" ).val(''); });
});
</script>
{/literal}

    <fieldset>
    <legend>
       {if $single eq false}
          {ts}New Activity{/ts}
       {elseif $action eq 1}
          {ts}New{/ts} 
       {elseif $action eq 2}
          {ts}Edit{/ts} 
       {elseif $action eq 8}
          {ts}Delete{/ts}
       {elseif $action eq 4}
          {ts}View{/ts}
       {elseif $action eq 32768}
          {ts}Detach{/ts}
       {/if}
       {$activityTypeName}
    </legend>
      
        {if $action eq 8} {* Delete action. *}
            <table class="form-layout">
             <tr>
                <td colspan="2">
                    <div class="status">{ts 1=$delName}Are you sure you want to delete '%1'?{/ts}</div>
                </td>
             </tr>
               
        {elseif $action eq 1 or $action eq 2  or $action eq 4 or $context eq 'search' or $context eq 'smog'}
            { if $activityTypeDescription }  
                <div id="help">{$activityTypeDescription}</div>
            {/if}

            <table class="{if $action eq 4}view-layout{else}form-layout{/if}">
             {if $context eq 'standalone' or $context eq 'search' or $context eq 'smog'}
                <tr>
                   <td class="label">{$form.activity_type_id.label}</td><td class="view-value">{$form.activity_type_id.html}</td>
                </tr>
             {/if}
             <tr>
                <td class="label">{$form.source_contact_id.label}</td>
                <td class="view-value">
                    {if $admin and $action neq 4}{$form.source_contact_id.html} {else} {$source_contact_value} {/if}
                </td>
             </tr>
             
             {if $single eq false}
             <tr>
                <td class="label">{ts}With Contact(s){/ts}</td>
                <td class="view-value" style="white-space: normal">{$with|escape}</td>
             </tr>
             {elseif $action neq 4}
             <tr>
                <td class="label">{ts}With Contact{/ts}</td>
                <td>{$form.target_contact_id.html}</td>
             </tr>
		     {else}
             <tr>
                <td class="label">{ts}With Contact{/ts}</td>
                <td class="view-value" style="white-space: normal">{$target_contact_value}</td>
             </tr>
             {/if}
             
             <tr>
             {if $action eq 4}
                <td class="label">{ts}Assigned To {/ts}</td><td class="view-value">{$assignee_contact_value}</td>
             {else}
                <td class="label">{ts}Assigned To {/ts}</td>
                <td>{$form.assignee_contact_id.html}                
                   {edit}<span class="description">{ts}You can optionally assign this activity to someone. Assigned activities will appear in their Activities listing at CiviCRM Home.{/ts}<br />{ts}A copy of this activity will be emailed to each Assignee.{/ts}</span>
                   {/edit}
                </td>
             {/if}
             </tr>
             <tr>
                <td class="label">{$form.subject.label}</td><td class="view-value">{$form.subject.html}</td>
             </tr>
             <tr>
                <td class="label">{$form.location.label}</td><td class="view-value">{$form.location.html}</td>
             </tr> 
             <tr>
                <td class="label">{$form.activity_date_time.label}</td>
                <td class="view-value">{$form.activity_date_time.html | crmDate }</br>
                    {if $action neq 4}
                      <span class="description">
                      {include file="CRM/common/calendar/desc.tpl" trigger=trigger_activity doTime=1}
                      {include file="CRM/common/calendar/body.tpl" dateVar=activity_date_time startDate=currentYear 
                                      endDate=endYear offset=10 doTime=1 trigger=trigger_activity ampm=1}
                      </span>
                   {/if}  
                </td>
             </tr>
             <tr>
                <td class="label">{$form.duration.label}</td>
                <td class="view-value">
                    {$form.duration.html}
                    <span class="description">{ts}Total time spent on this activity (in minutes).{/ts}
                </td>
             </tr> 
             <tr>
                <td class="label">{$form.status_id.label}</td><td class="view-value">{$form.status_id.html}</td>
             </tr> 
             <tr>
                <td class="label">{$form.details.label}</td><td class="view-value">{$form.details.html|crmReplace:class:huge}</td>
             </tr> 
	     <tr>
                <td class="label">{$form.priority_id.label}</td><td class="view-value">{$form.priority_id.html}</td>
             </tr>
             <tr>
                <td colspan="2">
	            {if $action eq 4} 
                    {include file="CRM/Custom/Page/CustomDataView.tpl"}
                {else}
                    <div id="customData"></div>
                {/if} 
                </td>
             </tr> 

             <tr>
                <td colspan="2">
                    {include file="CRM/Form/attachment.tpl"}
                </td>
             </tr>

             {if $action neq 4} {* Don't include "Schedule Follow-up" section in View mode. *}
                 <tr>
                    <td colspan="2">
                     <div id="follow-up_show" class="section-hidden section-hidden-border">
                      <a href="#" onclick="hide('follow-up_show'); show('follow-up'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open section"/></a><label>{ts}Schedule Follow-up{/ts}</label><br />
                     </div>
                          
                     <div id="follow-up" class="section-shown">
                       <fieldset><legend><a href="#" onclick="hide('follow-up'); show('follow-up_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close section"/></a>{ts}Schedule Follow-up{/ts}</legend>
                        <table class="form-layout-compressed">
                           <tr><td class="label">{ts}Schedule Follow-up Activity{/ts}</td>
                               <td>{$form.followup_activity_type_id.html}&nbsp;{$form.interval.label}&nbsp;{$form.interval.html}&nbsp;{$form.interval_unit.html}                          </td>
                           </tr>
                           <tr>
                              <td class="label">{$form.followup_activity_subject.label}</td>
                              <td>{$form.followup_activity_subject.html}</td>
                           </tr>
                        </table>
                       </fieldset>
                     </div>
                    </td>
                 </tr>
             {/if}
        {/if} {* End Delete vs. Add / Edit action *}
        <tr>
            <td>&nbsp;</td><td>{$form.buttons.html}</td>
        </tr> 
        </table>   
      </fieldset> 

{if $form.case_select}
<div id="fileOnCaseDialog">
{$form.case_select.label}<br /><br />
{$form.case_select.html}<br /><br />
{$form.case_subject.label}<br /><br />
{$form.case_subject.html}
</div>

{literal}
<script type="text/javascript">

cj("#fileOnCaseDialog").hide( );
function fileOnCase() {
    cj("#fileOnCaseDialog").show( );

	cj("#fileOnCaseDialog").dialog({
		title: "File on case",
		modal: true,
		bgiframe: true,
		width: 400,
		height: 300,
		width: 400,
		height: 300,
		overlay: { 
			opacity: 0.5, 
			background: "black"
		},

        beforeclose: function(event, ui) {
            cj(this).dialog("destroy");
        },

		open:function() {
		},

		buttons: { 
			"Ok": function() { 	    
				var v1 = cj("#case_select").val();
				if ( ! v1 ) {
					alert('Please select a case from the list.');
					return false;
				}
				var v2 = cj("#case_subject").val();
				
				var destUrl = {/literal}"{crmURL p='civicrm/contact/view/case' q='reset=1&action=view&id=' h=0 }"{literal}; 
 				var activityID = {/literal}"{$entityID}"{literal};
 				var underscore_pos = v1.indexOf('_');
 				if (underscore_pos < 1) {
 					alert('A problem occurred during case selection.');
 					return false;
 				}
 				var case_id = v1.substring(0, underscore_pos);
 				var contact_id = v1.substring(underscore_pos+1);
 				
				cj(this).dialog("close"); 
				cj(this).dialog("destroy");

				var postUrl = {/literal}"{crmURL p='civicrm/ajax/activity/convert' h=0 }"{literal};
                cj.post( postUrl, { activityID: activityID, caseID: v1, newSubject: v2 },
                    function( data ) {
                    		if (data.error_msg == "") {
                            	window.location.href = destUrl + case_id + '&cid=' + contact_id;
                            } else {
                            	alert("Unable to file on case.\n\n" + data.error_msg);
                            	return false;
                            } 
                        }, 'json' 
                    );
			},

			"Cancel": function() { 
				cj(this).dialog("close"); 
				cj(this).dialog("destroy"); 
			} 
		} 

	});
}

</script>
{/literal}
{/if}

{if $action eq 1 or $action eq 2 or $context eq 'search' or $context eq 'smog'}
   {*include custom data js file*}
   {include file="CRM/common/customData.tpl"}
    {literal}
    <script type="text/javascript">
   	cj(document).ready(function() {
		{/literal}
		buildCustomData( '{$customDataType}' );
		{if $customDataSubType}
			buildCustomData( '{$customDataType}', {$customDataSubType} );
		{/if}
		{literal}
	});

  hide('follow-up');
  show('follow-up_show');
    </script>
    {/literal}
{/if}

{/if} {* end of snippet if*}	
