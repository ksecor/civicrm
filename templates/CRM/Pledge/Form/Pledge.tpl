{* this template is used for adding/editing/deleting pledge *} 
{* if $cdType *}
  {* include file="CRM/Custom/Form/CustomData.tpl" *}
{* else *}
<div class="form-item">
{if !$email}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        <p>{ts}You will not be able to send an acknowledgment for this pledge because there is no email address recorded for this contact. If you want a acknowledgment to be sent when this pledge is recorded, click Cancel and then click Edit from the Summary tab to add an email address before recording the pledge.{/ts}</p>
    </dd>
  </dl>
</div>
{/if}
<fieldset><legend>{if $action eq 1 or $action eq 1024}{ts}New Pledge{/ts}{elseif $action eq 8}{ts}Delete Pledge{/ts}{else}{ts}Edit Pledge{/ts}{/if}</legend> 
   {if $action eq 8} 
      <div class="messages status"> 
        <dl> 
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt> 
          <dd> 
          {ts}WARNING: Deleting this pledge will result in the loss of the associated financial transactions (if any).{/ts} {ts}Do you want to continue?{/ts} 
          </dd> 
       </dl> 
      </div> 
   {else}
      <table class="form-layout-compressed">
        <tr>
            <td class="font-size12pt right"><strong>{ts}Pledger{/ts}</strong></td><td class="font-size12pt"><strong>{$displayName}</strong></td>
        </tr>
        <tr><td class="label">{$form.amount.label}</td><td>{$form.amount.html|crmMoney}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Actual amount given by pledger.{/ts}</td></tr>
	<tr><td class="label">{$form.frequency_unit.label}</td><td>{$form.frequency_unit.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Frequency Unit of this Pledge(day, week, month etc.){/ts}</td></tr>
	<tr><td class="label">{$form.frequency_interval.label}</td><td>{$form.frequency_interval.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Frequency Interval of this Pledge(integer - e.g. every "3" months){/ts}</td></tr>
	<tr><td class="label">{$form.frequency_day.label}</td><td>{$form.frequency_day.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}The day of week, month or year that payment is scheduled.{/ts}</td></tr>
	<tr><td class="label">{$form.installments.label}</td><td>{$form.installments.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Number of Installments.{/ts}</td></tr>
	<tr><td class="label">{$form.create_date.label}</td><td>{$form.create_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=create_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_2}<br />
            <span class="description">{ts}The date this pledge was received.{/ts}</span></td></tr>
	<tr><td class="label">{$form.start_date.label}</td><td>{$form.start_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=start_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_2}<br />
             {if $email}
            <span class="description">{ts}The date this pledge was started.{/ts}</span></td></tr>
	<tr><td class="label">{$form.is_acknowledge.label}</td><td>{$form.is_acknowledge.html}</td></tr>
            <tr><td class="label">&nbsp;</td><td class="description">{ts}Automatically email a acknowledgment for this contribution to {$email}?{/ts}</td></tr>
             {/if}
	<tr id="acknowledgeDate"><td class="label">{$form.acknowledge_date.label}</td><td>{$form.acknowledge_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=acknowledge_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_2}<br />
            <span class="description">{ts}Date that a acknowledgment was sent.{/ts}</span></td></tr>
	<tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}
	{if $status_id eq 2}{if $is_pay_later }: {ts}Pay Later{/ts} {else}: {ts}Incomplete Transaction{/ts}{/if}{/if}</td></tr>
        {* Cancellation fields are hidden unless contribution status is set to Cancelled *}
        <tr id="cancelDate"><td class="label">{$form.cancel_date.label}</td><td>{$form.cancel_date.html}
                   {if $hideCalendar neq true}
                     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_4}
                     {include file="CRM/common/calendar/body.tpl" dateVar=cancel_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_4}
                   {/if}
        </td></tr>
        {/if}
      </table>
      <div id="customData"></div>
    {*include custom data js file*}
    {* include file="CRM/common/customData.tpl" *}

    {literal}
    <script type="text/javascript">

     function verify( ) {
       var element = document.getElementsByName("is_acknowledge");
        if ( element[0].checked ) {
         var ok = confirm( "Click OK to save this Pledge record AND send a acknowledgment to the pledger now." );    
          if (!ok ) {
            return false;
          }
        }
     }
     function status() {
       document.getElementById("cancel_date[M]").value = "";
       document.getElementById("cancel_date[d]").value = "";
       document.getElementById("cancel_date[Y]").value = "";
     }
    </script>
    {/literal}
    <dl>    
       <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>   
    </dl> 
</fieldset>
</div> 

{if $email}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_acknowledge"
    trigger_value       =""
    target_element_id   ="acknowledgeDate" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 1
}
{/if}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="status_id"
    trigger_value       = '3'
    target_element_id   ="cancelDate" 
    target_element_type ="table-row"
    field_type          ="select"
    invert              = 0
}
{* /if *}
{* closing of main custom data if*}
