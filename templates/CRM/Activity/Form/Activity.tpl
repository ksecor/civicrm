{* this template is used for adding/editing other (custom) activities. *}
{if $cdType }
   {include file="CRM/Custom/Form/CustomData.tpl"}
{elseif $atypefile }
   {if $activityTypeFile}{include file="CRM/{$crmDir}/Form/Activity/$activityTypeFile.tpl"}{/if}
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
             {if $context eq 'standalone' or $context eq 'case' or $context eq 'search' }
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
                <td class="label">{ts}With Contact{/ts}</td>
                <td class="tundra">
		    <div id="target_contact_1"></div>
                </td>
             </tr>
		     {else}
             <tr>
    		<td class="label">{ts}With Contact{/ts}</td>
                <td class="view-value">{$target_contact_value}</td>
             </tr>
             {/if}
             <tr>
             {if $action neq 4}
                <td class="label">{ts}Assigned To {/ts}</td>
                <td class="tundra">                  
                   <div id="assignee_contact_1"></div>
                   {edit}<span class="description">{ts}You can optionally assign this activity to someone. Assigned activities will appear in their Contact Dashboard.{/ts}</span>{/edit}
                </td>
             {else}
                <td class="label">{ts}Assigned To {/ts}</td><td class="view-value">{$assignee_contact_value}</td>
             {/if}
             </tr>
             <tr>
                <td class="label">{$form.subject.label}</td><td class="view-value">{$form.subject.html}</td>
             </tr> 

             {* Include special processing fields if any are defined for this activity type. *}
             {if $activityTypeFile}
                {include file="CRM/{$crmDir}/Form/Activity/$activityTypeFile.tpl"}
             {else}
                {* if user going to select the activity type, provide space for dynamically injecting the form fields.*}
                <tr>
                   <td colspan="2"><span id="atypefields"></span></td>
                </tr>
             {/if}

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
                <td colspan="2">
	            {if $action eq 4} 
                    {include file="CRM/Custom/Page/CustomDataView.tpl"}
                {else}
                    <div id="customData"></div>
                {/if} 
                </td>
             </tr> 
             <tr>
             {if $action eq 4} 
                <td colspan=2>&nbsp;</td>
             {else}
                <td colspan="2">
                    {include file="CRM/Form/attachment.tpl"}
                </td>
            {/if} 
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
    </script>
    {/literal}
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

{if $action eq 1 or $action eq 2}
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

    // do not recreate if combo widget is already created
    if ( dijit.byId( pref + '[' + count + ']' ) ) {
	return;
    }

    var context = {/literal}"{$context}"{literal}
    var dataUrl = {/literal}"{crmURL p=$urlPath h=0 q='snippet=4&count='}"{literal} + count + '&' + pref + '=1&context=' + context;

{/literal}
{if $urlPathVar}
	dataUrl = dataUrl + '&' + '{$urlPathVar}'
{/if}
{literal}

    fetchurl(dataUrl, pref + '_' + count, pref + '_' + count);
}

function injectActTypeFileFields( type ) {
	var dataUrl = {/literal}"{crmURL p=$urlPath h=0 q='snippet=4&atype='}"{literal} + type; 
    dataUrl = dataUrl + '&atypefile=1';
    fetchurl(dataUrl, 'atypefields', false);
}

function fetchurl(dataUrl, fieldname, parsefield) {
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
    		        document.getElementById( fieldname ).innerHTML = response;
                    if ( parsefield ) {
	    	            dojo.parser.parse( parsefield );
                    }
        		}
        	    }
	});
}
</script>

{/literal}

{/if} {* closing add contact widget condition*}

{/if} {* end of snippet if*}	