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
  <dl>
    {if $action eq 1 or $action eq 2  or $action eq 4 }
      {if $action eq 1  or $form.activity_type_id.value }
        {if $action eq 1}
          <dt>{ts}With Contact{/ts}</dt><dd>{$displayName}&nbsp;</dd>
        {else}
  	      <dt>{ts}With Contact{/ts}</dt><dd>{$targetName}&nbsp;</dd>
    	  <dt>{ts}Created By{/ts}</dt><dd>{$sourceName}&nbsp;</dd>
        {/if}
    	<dt>{$form.activity_type_id.label}</dt><dd>{$form.activity_type_id.html}{$form.description.html|crmReplace:class:texttolabel}</dd>
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
      </dl>
    </fieldset>
    </div>

{if $action eq 1  or $form.activity_type_id.value }
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
      {foreach from= $ActivityTypeDescription item=description key=id}
        {literal}desc[index]{/literal} = "{$description}"
        {literal}index = index + 1{/literal}
      {/foreach}
      {literal}
      activityDesc.value = desc[activityType.selectedIndex];
    }
    {/literal}
    </script>
{/if}

{if $action eq 2 }
    <script type="text/javascript" >
       activity_get_description( );
    </script>
{/if}
