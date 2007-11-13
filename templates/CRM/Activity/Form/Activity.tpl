{* this template is used for adding/editing other (custom) activities. *}
<div class="form-item">
<div id="help">{$activityTypeDescription}</div>
<fieldset>
   <legend>
    {if $action eq 1}
    {$activityTypeName}
    {elseif $action eq 2}{ts}Edit{/ts} {$activityTypeName}
    {elseif $action eq 8}{ts}Delete{/ts} {$activityTypeName}
    {else}
        {ts}View{/ts}{$activityTypeName}
    {/if}
  </legend>
   
  <div class="tundra">
  <dl class="html-adjust">
    {if $action eq 1 or $action eq 2  or $action eq 4 }
      {if $action eq 1 }

    <dl class="html-adjust">     
    <dt>{$form.source_contact.label}</dt>
    
    {if $source_contact_value}
        <script type="text/javascript">
        dojo.addOnLoad( function( ) {ldelim}
        dijit.byId( 'source_contact' ).setValue( "{$source_contact_value}", "{$source_contact_value}" )
        {rdelim} );
        </script>
    {/if}

     <div dojoType="dojo.data.ItemFileReadStore" jsId="contactStore" url="{$dataUrl}">
        <dd>{if $action eq 4} {$source_contact_value} {/if}{$form.source_contact.html}</dd>
     </div>
    
    <dt>{$form.target_contact.label}</dt>
    {if $target_contact_value}
        <script type="text/javascript">
        dojo.addOnLoad( function( ) {ldelim}
        dijit.byId( 'target_contact' ).setValue( "{$target_contact_value}", "{$target_contact_value}" )
    {rdelim} );
    </script>
    {/if}
    <div dojoType="dojo.data.ItemFileReadStore" jsId="contactStore" url="{$dataUrl}" >
        <dd>{if $action eq 4} {$target_contact_value}{/if}{$form.target_contact.html}</dd>
    </div>

    <dt>{$form.assignee_contact.label}</dt>
    {if $regard_contact_value}
        <script type="text/javascript">
        dojo.addOnLoad( function( ) {ldelim}
        dijit.byId( 'assignee_contact' ).setValue( "{$regard_contact_value}", "{$regard_contact_value}" )
    {rdelim} );
    </script>
    {/if}  
    <div dojoType="dojo.data.ItemFileReadStore" jsId="contactStore" url="{$dataUrl}" >
        <dd>{if $action eq 4} {$assignee_contact_value}{/if}{$form.assignee_contact.html}</dd>
    </div>
    {*
    <dt>{$form.case_subject.label}</dt>
    {if $subject_value}
        <script type="text/javascript">
        dojo.addOnLoad( function( ) {ldelim}
        dijit.byId( 'case_subject' ).setValue( '{$subject_value}', '{$subject_value}' )
    {rdelim} );
    </script>
    {/if} 
    <div dojoType="dojo.data.ItemFileReadStore" jsId="caseStore" url="{$caseUrl}" >
        <dd>{if $action eq 4} {$subject_value} {else}{$form.case_subject.html}{/if}</dd>
    </div> 
    
    <dt>{$form.activity_tag1_id.label}</dt><dd>{$form.activity_tag1_id.html}</dd>
	<dt>{$form.activity_tag2_id.label}</dt><dd>{$form.activity_tag2_id.html}</dd>
	<dt>{$form.activity_tag3_id.label}</dt><dd>{$form.activity_tag3_id.html}</dd>
   </dl>
    *}
         <div class="spacer"></div>
        <dl class="html-adjust">
	    <dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
	    <dt>{$form.location.label}</dt><dd>{$form.location.html|crmReplace:class:large}</dd>
	<dt>{$form.activity_date_time.label}</dt><dd>{$form.activity_date_time.html | crmDate } </dd>
        {if $action neq 4}
            <dt>&nbsp;</dt>
            <dd class="description">
               {include file="CRM/common/calendar/desc.tpl" trigger=trigger_otheractivity_1}
            </dd>
            <dt>&nbsp;</dt>
            <dd class="description">
	{include file="CRM/common/calendar/body.tpl" dateVar=activity_date_time startDate=currentYear endDate=endYear offset=3 doTime=1 trigger=trigger_otheractivity_1}
            </dd>
        {/if}
    	<dt>{$form.duration_hours.label}</dt><dd>{$form.duration_hours.html} {ts}Hrs{/ts} &nbsp; {$form.duration_minutes.html} {ts}Min{/ts} &nbsp;</dd>
	    <dt>{$form.status_id.label}</dt><dd>{$form.status_id.html}</dd>
	
        {edit}      {*if $action neq 4*}{*Commented for crm-914*}
            <dt>&nbsp;</dt><dd class="description">{ts}Activity will be moved to Activity History when status is 'Completed'.{/ts}</dd>
        {/edit}     {*/if*}

        <dt>{$form.details.label}</dt><dd>{$form.details.html|crmReplace:class:huge}&nbsp;</dd>
        
        <dt></dt><dd class="description">
	    {if $action eq 4} 
         {include file="CRM/Contact/Page/View/InlineCustomData.tpl"}
        {else}
          {include file="CRM/Contact/Page/View/CustomData.tpl" mainEditForm=1}
        {/if} 
        </dd>
	
      {else}
         <div class="messages status">
          <dl>
           <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
           <dd>    
             {ts}Cannot display Activity History details since activity type for this activity has been deleted.{/ts}
           </dd>
          </dl>
        </div>
      {/if}
	
     {/if}
    
     {if $action eq 8 }
        <div class="status">{ts 1=$delName}Are you sure you want to delete "%1"?{/ts}</div>
     {/if}
	
        <dt></dt><dd>{$form.buttons.html}</dd>
     {if $action eq 4 && ! $history }
     <dl> <dt></dt><dd>&nbsp;&nbsp;</dd>&nbsp;<dd><a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=`$activityID`&action=update&reset=1&id=`$id`&cid=`$contactId`&context=`$context`&subType=`$activityID`&edit=1&caseid=`$caseid`"}" ">{ts}Edit Activity{/ts}</a>{ts} | {/ts}
   <a href="{crmURL p='civicrm/contact/view/activity'
     q="activity_id=`$activityID`&action=delete&reset=1&id=`$id`&cid=`$contactId`&context=`$context`&subType=`$activityID`&caseid=`$caseid`"}" ">{ts}  Delete Activity {/ts}</a>{ts} | {/ts}
        
        {if $subject_value}  
           <a href="{crmURL p='civicrm/contact/view/case'
     q="activity_id=`$activityID`&action=delete&reset=1&id=`$id`&cid=`$contactId`&context=`$context`&subType=`$activityID`&caseid=`$caseid`"}" ">{ts}  Detach Activity from Case {/ts}</a>
        {/if}
        </dd>
    </dl>
    {/if} 
      </dl>
    </div>

    </fieldset>
    </div>
