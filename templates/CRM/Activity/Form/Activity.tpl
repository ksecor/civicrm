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
      
        {if $action eq 8} {* Delete action. *}
            <table class="form-layout">
             <tr>
                <td colspan="2">
                    <div class="status">{ts 1=$delName}Are you sure you want to delete '%1'?{/ts}</div>
                </td>
             </tr>
               
        {elseif $action eq 1 or $action eq 2  or $action eq 4 or $context eq 'search' }
            { if $activityTypeDescription }  
                <div id="help">{$activityTypeDescription}</div>
            {/if}

            <table class="form-layout">
             {if $context eq 'standalone' or $context eq 'search' }
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
                <td class="view-value" style="white-space: normal">{$with|escape}</td>
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
                <td class="view-value" style="white-space: normal">{$target_contact_value}</td>
             </tr>
             {/if}
             
             <tr>
             {if $action eq 4}
                <td class="label">{ts}Assigned To {/ts}</td><td class="view-value">{$assignee_contact_value}</td>
             {else}
                <td class="label">{ts}Assigned To {/ts}</td>
                <td class="tundra">                  
                   <div id="assignee_contact_1"></div>
                   {edit}<span class="description">{ts}You can optionally assign this activity to someone. Assigned activities will appear in their Contact Dashboard.{/ts}</span>{/edit}
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

  hide('follow-up');
  show('follow-up_show');
    </script>
    {/literal}
{/if}

{* Build add contact *}
<script type="text/javascript">
    {literal}

    cj(document).ready(function(){
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

            {/literal}
        {/if}
  {literal}        
  });
    
  {/literal}
</script>

{*include add contact js file*}
{include file="CRM/common/addContact.tpl"}

{/if} {* end of snippet if*}	