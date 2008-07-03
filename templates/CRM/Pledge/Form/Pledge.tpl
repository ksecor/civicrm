{* this template is used for adding/editing/deleting pledge *} 
{* if $cdType *}
  {* include file="CRM/Custom/Form/CustomData.tpl" *}
{* else *}
{if $showAdditionalInfo and $formType }
  {include file="CRM/Contribute/Form/AdditionalInfo/$formType.tpl"}
{else}
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
            <td class="font-size12pt right"><strong>{ts}Pledge From{/ts}</strong></td><td class="font-size12pt"><strong>{$displayName}</strong></td>
        </tr>
        <tr><td class="label">{$form.amount.label}</td><td>{$form.amount.html|crmMoney}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Actual amount given by pledger.{/ts}</td></tr>
	<tr><td class="label">{$form.installments.label}</td><td>{$form.installments.html}&nbsp;&nbsp;{$form.frequency_unit.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Number of Installments.{/ts}</td></tr>
	<tr><td class="label">{$form.frequency_day.label}</td><td>{$form.frequency_day.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}This applies to weekly, monthly and yearly payments.{/ts}</td></tr>
	<tr><td class="label">{$form.payment_amount.label}</td><td>{$form.payment_amount.html|crmMoney}</td></tr>
        <tr><td class="label">{$form.create_date.label}</td><td>{$form.create_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=create_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_2}<br />
            <span class="description">{ts}Date when pledge was made by the contributor.{/ts}</span></td></tr>
        <tr><td class="label">{$form.start_date.label}</td><td>{$form.start_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=start_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_2}<br />
            <span class="description">{ts}Date of first pledge payment.{/ts}</span></td></tr>
        {if $email}
            <tr><td class="label">{$form.is_acknowledge.label}</td><td>{$form.is_acknowledge.html}</td></tr>
            <tr><td class="label">&nbsp;</td><td class="description">{ts}Automatically email an acknowledgment of this pledge to {$email}?{/ts}</td></tr>
        {/if}
        <tr id="acknowledgeDate"><td class="label">{$form.acknowledge_date.label}</td><td>{$form.acknowledge_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=acknowledge_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_2}<br />
            <span class="description">{ts}Date when an acknowledgment of the pledge was sent.{/ts}</span></td></tr>
	<tr><td class="label">{$form.contribution_type_id.label}</td><td>{$form.contribution_type_id.html}</td></tr>
	<tr><td class="label">&nbsp;</td><td class="description">{ts}Sets the default contribution type for payments against this pledge.{/ts}</td></tr>
        <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}If payments are received on time, Pledges remain in Pending status until all scheduled payment are completed. Overdue pledges are ones with payment(s) past due.{/ts}</td></tr>
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
         var ok = confirm( "Click OK to save this Pledge record AND send an acknowledgment to {/literal}{$email}{literal} now." );    
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
     function calculatedPaymentAmount( ) {
       var amount = document.getElementById("amount").value;
       var installments = document.getElementById("installments").value;
       document.getElementById("payment_amount").value = (amount/installments);
     }
    </script>
    {/literal}

{* dojo pane *}
<div class="form-item" id="additionalInformation">
   {* Honoree Information / Payment Reminders*}
   <div class="tundra">
      {foreach from=$allPanes key=paneName item=paneValue}
        {if $paneValue.open eq 'true'}
           <div id="{$paneValue.id}" href="{$paneValue.url}" dojoType="civicrm.TitlePane"  title="{$paneName}" open="{$paneValue.open}" width="200" executeScript="true"></div>
        {else}
           <div id="{$paneValue.id}" dojoType="civicrm.TitlePane"  title="{$paneName}" open="{$paneValue.open}" href ="{$paneValue.url}" executeScript="true"></div>
        {/if}
      {/foreach}
   </div>
</div>

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
{/if}
{* closing of main dojo pane if*}
