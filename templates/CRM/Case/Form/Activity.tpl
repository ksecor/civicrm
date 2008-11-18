{* this template is used for adding/editing activities for a case. *}
{if $addAssigneeContact }
   {include file="CRM/Contact/Form/AddContact.tpl"}
{else}

{* onload javascript for source contact *}
<script type="text/javascript">
    dojo.addOnLoad( function( ) {ldelim}
    dijit.byId( 'source_contact_id' ).setValue( "{$source_contact}")
    {rdelim} );
</script>


<fieldset>
    <legend>
       {if $action eq 8}
          {ts}Delete{/ts}
       {elseif $action eq 4}
          {ts}View{/ts}
       {elseif $action eq 32768}
          {ts}Detach{/ts}
       {/if}
       {$activityTypeName}
    </legend>

       {if $activityTypeDescription }  
          <div id="help">{$activityTypeDescription}</div>
       {/if}

    <table class="form-layout">
       <tr>
          <td class="label font-size12pt">{ts}Client{/ts}</td>
          <td class="view-value font-size12pt bold">{$client_name|escape}</td>
       </tr>
       <tr>
          <td class="label">{ts}Activity Type{/ts}</td>
          <td class="view-value bold">{$activityTypeName|escape}</td>
       </tr>
       <tr>
          <td class="label">{$form.source_contact_id.label}</td>
          <td class="view-value">
              <div dojoType="dojox.data.QueryReadStore" jsId="contactStore" url="{$dataUrl}" class="tundra" doClientPaging="false">
                  {if $admin}{$form.source_contact_id.html}{/if}
              </div>
          </td>
        </tr>
        <tr>
            <td class="label">{ts}Assigned To {/ts}</td>
            <td class="tundra">                  
                <div id="assignee_contact_1"></div>
                {edit}<span class="description">{ts}You can optionally assign this activity to someone. Assigned activities will appear in their Contact Dashboard.{/ts}</span>{/edit}
            </td>
        </tr>

        {* Include special processing fields if any are defined for this activity type. *}
        {if $caseAction}
            <tr><td colspan="2"><hr />{include file="CRM/Case/Form/Activity/$caseAction.tpl"}<hr /><br/></td></tr>
        {/if}

        <tr>
          <td class="label">{$form.subject.label}</td><td class="view-value">{$form.subject.html}</td>
        </tr>
       <tr>
          <td class="label">{$form.medium_id.label}</td>
          <td class="view-value">{$form.medium_id.html}&nbsp;&nbsp;&nbsp;{$form.location.label} &nbsp;{$form.location.html}</td>
       </tr> 
       <tr>
          <td class="label">{$form.due_date_time.label}</td>
          <td class="view-value">{$form.due_date_time.html | crmDate }</br>
              <span class="description">
              {include file="CRM/common/calendar/desc.tpl" trigger=trigger_activity doTime=1}
              {include file="CRM/common/calendar/body.tpl" dateVar=due_date_time startDate=currentYear 
                             endDate=endYear offset=10 doTime=1 trigger=trigger_activity ampm=1}
              </span>
          </td>
       </tr> 
       <tr>
          <td class="label">{$form.activity_date_time.label}</td>
          <td class="view-value">{$form.activity_date_time.html | crmDate }</br>
              {if $action neq 4}
                  <span class="description">
                      {include file="CRM/common/calendar/desc.tpl" trigger=trigger_activity_1 doTime=1}
                      {include file="CRM/common/calendar/body.tpl" dateVar=activity_date_time startDate=currentYear 
                                     endDate=endYear offset=10 doTime=1 trigger=trigger_activity_1 ampm=1}
                  </span>
              {/if}  
          </td>
       </tr>
       <tr>
          <td colspan="2">{include file="CRM/Custom/Form/CustomData.tpl" noPostCustomButton=1}</td>
       </tr>
       <tr>
          <td class="label">{$form.details.label}</td><td class="view-value">{$form.details.html|crmReplace:class:huge}</td>
       </tr>
       <tr>
          <td colspan="2">{include file="CRM/Form/attachment.tpl"}</td>
       </tr>
    {if $searchRows} {* We've got case role rows to display for "Send Copy To" feature *}
        <tr>
            <td colspan="2">
        <div id="sendcopy_show" class="section-hidden section-hidden-border">
        <a href="#" onclick="hide('sendcopy_show'); show('sendcopy'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open section"/></a><label>{ts}Send a Copy{/ts}</label><br />
        </div>

        <div id="sendcopy" class="section-shown">
         <fieldset><legend><a href="#" onclick="hide('sendcopy'); show('sendcopy_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close section"/></a>{ts}Send a Copy{/ts}</legend>
         <div class="description">
             {ts}Email a complete copy of this activity record to other people involved with the case. Click the top left box to select all.{/ts}
         </div>
          {strip}
          <table>
              <tr class="columnheader">
                  <th>{$form.toggleSelect.html}&nbsp;</th>
                  <th>{ts}Case Role{/ts}</th>
                  <th>{ts}Name{/ts}</th>
                  <th>{ts}Email{/ts}</th>
               </tr>
               {foreach from=$searchRows item=row}
               <tr class="{cycle values="odd-row,even-row"}">
                   <td>{$form.contact_check[$row.id].html}</td>
                   <td>{$row.role}</td>
                   <td>{$row.name}</td>
                   <td>{$row.email}</td>
               </tr>
               {/foreach}
          </table>
          {/strip}
         </fieldset>
        </div>
            </td>
        </tr>
    {/if}
       <tr>
          <td colspan="2">
            <div id="follow-up_show" class="section-hidden section-hidden-border">
             <a href="#" onclick="hide('follow-up_show'); show('follow-up'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open section"/></a><label>{ts}Schedule Follow-up{/ts}</label><br />
            </div>

            <div id="follow-up" class="section-shown">
            <fieldset><legend><a href="#" onclick="hide('follow-up'); show('follow-up_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close section"/></a>{ts}Schedule Follow-up{/ts}</legend>
                <table class="form-layout-compressed">
                    <tr><td class="label">{ts}Schedule Follow-up Activity{/ts}</td>
                        <td>{$form.followup_activity.html}&nbsp;{$form.interval.label}&nbsp;{$form.interval.html}&nbsp;{$form.interval_unit.html}<br />
                            <span class="description">Begin typing to select from a list of available activity types.</span>
                        </td>
                    </tr>
                </table>
            </fieldset>
            </div>
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
          <td>&nbsp;</td><td class="buttons">{$form.buttons.html}</td>
        </tr>
    </table>
</fieldset>
{/if}
 
{* Build add contact *}
{literal}
<script type="text/javascript">
hide('attachments');
show('attachments_show');
hide('sendcopy');
show('sendcopy_show');
hide('follow-up');
show('follow-up_show');
buildContact( 1, 'assignee_contact' );

var activityUrl = {/literal}"{crmURL p='civicrm/ajax/activitytypelist' h=0 q='caseType='}{$caseType}"{literal};

cj("#followup_activity").autocomplete( activityUrl, {
	width: 260,
	selectFirst: false  
});

cj("#followup_activity").result(function(event, data, formatted) {

});		    

var assigneeContactCount = {/literal}"{$assigneeContactCount}"{literal}
if ( assigneeContactCount ) {
    for ( var i = 1; i <= assigneeContactCount; i++ ) {
	buildContact( i, 'assignee_contact' );
    }
}

function buildContact( count, pref )
{
    if ( count > 1 ) {
	    prevCount = count - 1;
        {/literal}{if $action eq 1  OR $action eq 2}{literal}
	        hide( pref + '_' + prevCount + '_show');
        {/literal}{/if}{literal}
    }

    // do not recreate if combo widget is already created
    if ( dijit.byId( pref + '[' + count + ']' ) ) {
	    return;
    }

    var context = {/literal}"{$context}"{literal}
    var dataUrl = {/literal}"{crmURL p=$urlPath h=0 q='snippet=4&count='}"{literal} + count + '&' + pref + '=1&context=' + context;

    {/literal}{if $urlPathVar}
	    dataUrl = dataUrl + '&' + '{$urlPathVar}'
    {/if}{literal}

    var result = dojo.xhrGet({
        url: dataUrl,
        handleAs: "text",
    	sync: true,
        timeout: 5000, //Time in milliseconds
        handle: function(response, ioArgs) {
                    if (response instanceof Error) {
    	        	    if (response.dojoType == "cancel") {
            			//The request was canceled by some other JavaScript code.
            			console.debug("Request canceled.");
            		    } else if (response.dojoType == "timeout") {
            			//The request took over 5 seconds to complete.
            			console.debug("Request timed out.");
            		    } else {
            			//Some other error happened.
            			console.error(response);
            		    }
                    } else {
    		            // on success
            		    dojo.byId( pref + '_' + count).innerHTML = response;
            		    dojo.parser.parse( pref + '_' + count );
            		}
	            }
	});
}
</script>
{/literal}
