{* this template is used for adding/editing other (custom) activities. *}
{if $cdType }
   {include file="CRM/Custom/Form/CustomData.tpl"}
{elseif $addAssigneeContact or $addTargetContact }
   {include file="CRM/Contact/Form/AddContact.tpl"}
{else}

{* added onload javascript for source contact*}
{if $source_contact and $admin and $action neq 4}
   <script type="text/javascript">
       dojo.addOnLoad( function( ) {ldelim}
       dijit.byId( 'source_contact_id' ).setValue( "{$source_contact}")
       {rdelim} );
   </script>
{/if}

{* added onload javascript for case subject*}
{if $caseId and $context neq 'standalone' and $action neq 4}
   <script type="text/javascript">
       dojo.addOnLoad( function( ) {ldelim}
       dijit.byId( 'case_id' ).setValue( "{$caseId}" )
       {rdelim} );
   </script>
{/if}

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
      
        { if $activityTypeDescription }  
          <div id="help">{$activityTypeDescription}</div>
        {/if}
      
        {edit}
        {if $caseEnabled AND ! $hasCases}
            {capture assign=newCaseUrl}{crmURL p='civicrm/contact/view/case' q="reset=1&action=add&cid=$contactId&context=case"}{/capture}
            <div class="status messages">{ts}There are no Cases for this contact.{/ts}{if $permission EQ 'edit'} {ts 1=$newCaseUrl}If you want to associate this activity with a case, you will need to <a href='%1'>create one first</a>.{/ts}{/if}</div>
        {/if}
        {/edit}
        
         <table class="form-layout">
           {if $action eq 1 or $action eq 2  or $action eq 4 or $context eq 'search' }
             {if $context eq ('standalone' or 'case' or 'search') }
                <tr>
                   <td class="label">{$form.activity_type_id.label}</td><td class="view-value">{$form.activity_type_id.html}</td>
                </tr>
             {/if}
             <tr>
                <td class="label">{$form.source_contact_id.label}</td>
                <td class="view-value">
                   <div dojoType="dojox.data.QueryReadStore" jsId="contactStore" url="{$dataUrl}" class="tundra" doClientPaging="false">
                       {if $admin and $action neq 4}{$form.source_contact_id.html} {else} {$source_contact_value} {/if}
                   </div>
                </td>
             </tr>
             {if $single eq false}
             <tr>
                <td class="label">{ts}With Contact(s){/ts}</td>
                <td class="view-value">{$with|escape}</td>
             </tr>
             {elseif $action neq 4}
             <tr>
		 <td class="label">{ts}With Contact{/ts}<div dojoType="dojox.data.QueryReadStore" jsId="contactStore" url="{$dataUrl}" class="tundra" doClientPaging="false"></div></td>
                <td class="tundra">
		          <span id="target_contact_1"></span></td></tr>
		     {else}
             <tr>
	    		<td class="label">{ts}With Contact{/ts}</td>
                <td class="view-value">{$target_contact_value}</td>
             </tr>
             {/if}
             <tr>
            {if $action neq 4}
		 <td class="label">{ts}Assigned To {/ts}<div dojoType="dojox.data.QueryReadStore" jsId="contactStore" url="{$dataUrl}" class="tundra" doClientPaging="false"></div></td>
                <td class="tundra">                  
                   <span id="assignee_contact_1"></span>
                   <br />{edit}<span class="description">{ts}You can optionally assign this activity to someone. Assigned activities will appear in their Contact Dashboard.{/ts}</span>{/edit}
                </td>
            {else}
                <td class="label">{ts}Assigned To {/ts}</td><td class="view-value">{$assignee_contact_value}</td>
            {/if}
             </tr>

             {if $context neq 'standalone' AND $hasCases}
                <tr>
                  <td class="label">{$form.case_id.label}</td>
                  <td class="view-value">
                     <div dojoType="dojox.data.QueryReadStore" jsId="caseStore" url="{$caseUrl}" class="tundra">
                         {$form.case_id.html}
                     </div>
                     {edit}<span class="description">{ts}If you are managing case(s) for this contact, you can optionally associate this activity with an existing case.{/ts}</span>{/edit}
                  </td>
                </tr>
             {/if}

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
	           {if $action eq 4} 
                      {if $currentAttachmentURL}
                         <td class="label">{ts}Current Attachments{/ts}</td>
                         <td class="view-value">{$currentAttachmentURL}</td>
                      {else}  
                          <td colspan=2>&nbsp;</td>
                      {/if}
                   {else}
                <td colspan="2">
                      {include file="CRM/Form/attachment.tpl"}
                </td>
                   {/if} 
             </tr>
             <tr>
                <td colspan="2">
	           {if $action eq 4} 
                       {include file="CRM/Contact/Page/View/InlineCustomData.tpl"}
                   {else}
                      <div id="customData"></div>
                   {/if} 
                </td>
             </tr> 
             <tr>
                <td colspan="2">&nbsp;</td>
             </tr> 
           {elseif $action eq 8}
             <tr>
                <td colspan="2">
                    <div class="status">{ts 1=$delName}Are you sure you want to delete '%1'?{/ts}</div>
                </td>
             </tr>  
           {elseif $action eq 32768}
             <tr>
                <td colspan="2">
                    <div class="status">{ts 1=$delName}Are you sure you want to detach '%1' from this case?{/ts}</div>
                </td>
             </tr>  
           {/if}
             <tr>
                <td>&nbsp;</td><td>{$form.buttons.html}</td>
             </tr> 
         </table>   
      </fieldset> 

{if $action eq 1 or $action eq 2 or $context eq 'search'}
   {*include custom data js file*}
   {include file="CRM/common/customData.tpl"}
{/if}

{* Build add contact *}
{literal}
<script type="text/javascript">
{/literal}
{if $action eq 1 or $context eq 'search'}
{literal}
   buildContact( 1, 'assignee_contact' );
{/literal}   
{/if}
{if $action eq 1 }
{literal}
   buildContact( 1, 'target_contact' );
{/literal}   
{/if}
{literal}

var assigneeContactCount = {/literal}"{$assigneeContactCount}"{literal}

if ( assigneeContactCount ) {
    for ( var i = 1; i <= assigneeContactCount; i++ ) {
	buildContact( i, 'assignee_contact' );
    }
}

var targetContactCount = {/literal}"{$targetContactCount}"{literal}

if ( targetContactCount ) {
    for ( var i = 1; i <= targetContactCount; i++ ) {
	buildContact( i, 'target_contact' );
    }
}

function buildContact( count, pref )
{
    if ( count > 1 ) {
	prevCount = count - 1;
    {/literal}
    {if $action eq 1  OR $action eq 2}
    {literal}
	hide( pref + '_' + prevCount + '_show');
    {/literal} 
    {/if}
    {literal}
    }
    var context = {/literal}"{$context}"{literal}
    var dataUrl = {/literal}"{crmURL p=$contactUrlPath h=0 q='snippet=4&count='}"{literal} + count + '&' + pref + '=1&context=' + context;

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

{/if} {* end of snippet if*}	