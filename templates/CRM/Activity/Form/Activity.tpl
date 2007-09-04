{* this template is used for adding/editing other (custom) activities. *}
<div class="form-item">
<fieldset>
   <legend>
    {if $action eq 1}
    {ts}Schedule an Activity{/ts}
    {elseif $action eq 2}{ts}Edit Scheduled Activity{/ts}
    {elseif $action eq 8}{ts}Delete Activity{/ts}
    {else}
        {if $history eq 1}{ts}View Completed Activity{/ts}{else}{ts}View Scheduled Activity{/ts}{/if}
    {/if}
  </legend>
  <dl class="html-adjust">
    {if $action eq 1 or $action eq 2  or $action eq 4 }
      {if $action eq 1  or $form.activity_type_id.value }
         <dt>{$form.activity_type_id.label}</dt><dd>{$form.activity_type_id.html}{$form.description.html|crmReplace:class:texttolabel}</dd>







   <dl class="html-adjust">     
    <dt>{$form.from_contact.label}</dt>
    
    {if $from_contact_value}
     
        <script type="text/javascript">
        dojo.addOnLoad( function( ) {ldelim}
        dojo.widget.byId( 'from_contact' ).setAllValues( "{$from_contact_value}", "{$from_contact_value}" )
        {rdelim} );
        </script>
   
    {/if}
    <dd>{if $action eq 4} {$from_contact_value} {else}{$form.from_contact.html}{/if}</dd>
    <dt>{$form.to_contact.label}</dt>
    {if $to_contact_value}
    <script type="text/javascript">
    dojo.addOnLoad( function( ) {ldelim}
    dojo.widget.byId( 'to_contact' ).setAllValues( "{$to_contact_value}", "{$to_contact_value}" )
    {rdelim} );
    </script>
    {/if}
    <dd>{if $action eq 4}{$to_contact_value}{else}{$form.to_contact.html}{/if}</dd>
    <dt>{$form.regarding_contact.label}</dt>
    {if $regard_contact_value}
    <script type="text/javascript">
    dojo.addOnLoad( function( ) {ldelim}
    dojo.widget.byId( 'regarding_contact' ).setAllValues( "{$regard_contact_value}", "{$regard_contact_value}" )
    {rdelim} );
    </script>
    {/if}  
    <dd>{if $action eq 4}{$regard_contact_value}{else}{$form.regarding_contact.html}{/if}</dd>
	<dt>{$form.case_subject.label}</dt>
    {if $subject_value}
    <script type="text/javascript">
    dojo.addOnLoad( function( ) {ldelim}
    dojo.widget.byId( 'case_subject' ).setAllValues( '{$subject_value}', '{$subject_value}' )
    {rdelim} );
    </script>
    {/if}  
    <dd>{if $action eq 4}{$subject_value}{else}{$form.case_subject.html}{/if}</dd>
   
    <dt>{$form.activity_tag1_id.label}</dt><dd>{$form.activity_tag1_id.html}</dd>
	<dt>{$form.activity_tag2_id.label}</dt><dd>{$form.activity_tag2_id.html}</dd>
	<dt>{$form.activity_tag3_id.label}</dt><dd>{$form.activity_tag3_id.html}</dd>
   </dl>









        <div class="spacer"></div>
        <dl class="html-adjust">
	    <dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
	    <dt>{$form.location.label}</dt><dd>{$form.location.html|crmReplace:class:large}</dd>
        {if $action eq 4}
            <dt>{$form.scheduled_date_time.label}</dt><dd>{$scheduled_date_time|crmDate}</dd>
        {else}
            <dt>{$form.scheduled_date_time.label}</dt>
            <dd>{$form.scheduled_date_time.html}</dd>
            <dt>&nbsp;</dt>
            <dd class="description">
               {include file="CRM/common/calendar/desc.tpl" trigger=trigger_otheractivity_1}
            </dd>
            <dt>&nbsp;</dt>
            <dd class="description">
{include file="CRM/common/calendar/body.tpl" dateVar=scheduled_date_time startDate=currentYear endDate=endYear offset=3 doTime=1 trigger=trigger_otheractivity_1}
            </dd>
        {/if}
    	<dt>{$form.duration_hours.label}</dt><dd>{$form.duration_hours.html} {ts}Hrs{/ts} &nbsp; {$form.duration_minutes.html} {ts}Min{/ts} &nbsp;</dd>
	    <dt>{$form.status.label}</dt><dd>{$form.status.html}</dd>
	
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
    </fieldset>
    </div>
    

{if $action eq 1  or $action eq 2 or $form.activity_type_id.value } 
    <script type="text/javascript" >
    var activityDesc = document.getElementById("description");
    activityDesc.readOnly = 1;
    {literal}

    function activity_get_description( )
    {
      var activityType = document.getElementById("activity_type_id");
      var activityDesc = document.getElementById("description");
      var desc = new Array();
      desc[0] = "";
      {/literal}
      var index = 1;
      {foreach from= $ActivityTypeDescription item=description key=id}{$ActivityTypeDescription}
        {literal}desc[index]{/literal} = "{$description}"
        {literal}index = index + 1{/literal}
      {/foreach}
      {literal}
      activityDesc.value = desc[activityType.selectedIndex];
     }
    
    function reload(refresh) {
        var activityType = document.getElementById("activity_type_id");
        var context = {/literal}"{$context}"{literal} 
        var caseid = {/literal}"{$caseid}"{literal}
        var url = {/literal}"{$refreshURL}"{literal}
        var post = url + "&subType=" + activityType.value + "&context=" + context + "&caseid=" + caseid
        if( refresh ) {
            window.location= post; 
        }
    }
        
    {/literal}
    </script >
{/if}       

 <script type="text/javascript" >
      activity_get_description( );
 </script>
