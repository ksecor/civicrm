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
            <tr><td colspan="2"><hr /></td></tr>
            {include file="CRM/Case/Form/Activity/$caseAction.tpl"}
            <tr><td colspan="2"><hr /></td></tr>
        {/if}

        <tr>
          <td class="label">{$form.subject.label}</td><td class="view-value">{$form.subject.html}</td>
        </tr>
       <tr>
          <td class="label">{$form.medium_id.label}</td><td class="view-value">{$form.medium_id.html}</td>
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
          <td class="label">{$form.duration_hours.label}</td>
          <td class="view-value">
              {if $action eq 4}
                 {if $form.duration_hours.value}{$form.duration_hours.html} {ts}Hrs{/ts}&nbsp;&nbsp;{/if}
                 {if $form.duration_minutes.value}{$form.duration_minutes.html} {ts}Mins{/ts}{/if}
                 {else}
                     {$form.duration_hours.html} {ts}Hrs{/ts}&nbsp;&nbsp;{$form.duration_minutes.html} {ts}Mins{/ts}
                 {/if}
          </td>
       </tr> 
       <tr>
          <td class="label">{$form.status_id.label}</td><td class="view-value">{$form.status_id.html}</td>
       </tr> 
       <tr>
          <td class="label">{$form.details.label}</td><td class="view-value">{$form.details.html|crmReplace:class:huge}</td>
       </tr>
       <tr>
          <td colspan="2">{include file="CRM/Form/attachment.tpl"}</td>
       </tr>
    </table>
    {if $searchRows} {* we've got rows to display *}
       <fieldset><legend>{ts}Send Copy To{/ts}</legend>
          <div class="description">
              {ts}Select the contact(s) whom you would like to mail a copy of this activity.{/ts}
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
    {/if}
    {include file="CRM/Custom/Form/CustomData.tpl"}
</fieldset>
{/if}
 
{* Build add contact *}
{literal}
<script type="text/javascript">
hide('attachments');
show('attachments_show');
buildContact( 1, 'assignee_contact' );

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
