{* this template is used for adding/editing/deleting pledge *} 
{if $cdType}
  {include file="CRM/Custom/Form/CustomData.tpl"}
{elseif $showAdditionalInfo and $formType }
  {include file="CRM/Contribute/Form/AdditionalInfo/$formType.tpl"}
{else}
{if !$email and $action neq 8}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        <p>{ts}You will not be able to send an acknowledgment for this pledge because there is no email address recorded for this contact. If you want a acknowledgment to be sent when this pledge is recorded, click Cancel and then click Edit from the Summary tab to add an email address before recording the pledge.{/ts}</p>
    </dd>
  </dl>
</div>
{/if}
<div class="form-item">
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
            <td class="font-size12pt right"><strong>{ts}Pledge by{/ts}</strong></td><td class="font-size12pt"><strong>{$displayName}</strong></td>
        </tr>
        <tr><td class="font-size12pt right">{$form.amount.label}</td><td class="font-size12pt">{$form.amount.html|crmMoney}</td></tr>
        <tr><td class="label">{$form.installments.label}</td><td>{$form.installments.html}&nbsp;&nbsp;{$form.frequency_unit.html} {ts}installments of{/ts} {if $action eq 1}{$form.eachPaymentAmount.html|crmMoney}{elseif $action eq 2}{$eachPaymentAmount|crmMoney}{/if} {ts}each{/ts}</td></tr>
        <tr><td class="label nowrap">{$form.frequency_day.label}</td><td>{$form.frequency_day.html} {ts}day of the period{/ts}<br />
            <span class="description">{ts}This applies to weekly, monthly and yearly payments.{/ts}</td></tr>
        {if $form.create_date}	
        <tr><td class="label">{$form.create_date.label}</td><td>{$form.create_date.html}
            {if $hideCalender neq true}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_pledge_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=create_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_pledge_1}
            {/if}<br />
        {/if}
        {if $create_date}
            <tr><td class="label">Pledge Made</td><td class="view-value">{$create_date|truncate:10:''|crmDate}
        {/if}<br />
            <span class="description">{ts}Date when pledge was made by the contributor.{/ts}</span></td></tr>
       
        {if $form.start_date}	
            <tr><td class="label">{$form.start_date.label}</td><td>{$form.start_date.html}
            {if $hideCalender neq true}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_pledge_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=start_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_pledge_2}
            {/if}<br />
        {/if}
        {if $start_date}
            <tr><td class="label">Payments Start</td><td class="view-value">{$start_date|truncate:10:''|crmDate}
        {/if}<br />
            <span class="description">{ts}Date of first pledge payment.{/ts}</span></td></tr>
       
        {if $email}
        {if $form.is_acknowledge }
            <tr><td class="label">{$form.is_acknowledge.label}</td><td>{$form.is_acknowledge.html}<br />
            <span class="description">{ts}Automatically email an acknowledgment of this pledge to {$email}?{/ts}</span></td></tr>
        {/if}
        {/if}
        <tr id="acknowledgeDate"><td class="label">{$form.acknowledge_date.label}</td><td>{$form.acknowledge_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_pledge_3}
            {include file="CRM/common/calendar/body.tpl" dateVar=acknowledge_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_pledge_3}<br />
            <span class="description">{ts}Date when an acknowledgment of the pledge was sent.{/ts}</span></td></tr>
            <tr><td class="label">{$form.contribution_type_id.label}</td><td>{$form.contribution_type_id.html}<br />
            <span class="description">{ts}Sets the default contribution type for payments against this pledge.{/ts}</span></td></tr>
	    <tr><td class="label">{$form.contribution_page_id.label}</td><td>{$form.contribution_page_id.html}<br />
            <span class="description">{ts}Select an Online Contribution page that the user can access to make self-service pledge payments. (Only Online Contribution pages configured to include the Pledge option are listed.){/ts}</span></td></tr>
        
	    <tr><td class="label">{ts}Pledge Status{/ts}</td><td class="view-value">{$status}<br />
            <span class="description">{ts}Pledges are "Pending" until the first payment is received. Once a payment is received, status is "In Progress" until all scheduled payments are completed. Overdue pledges are ones with payment(s) past due.{/ts}</span></td></tr>
       </table>
   
      <div id="customData"></div>
       {*include custom data js file*}
       {include file="CRM/common/customData.tpl"}

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
   
     function calculatedPaymentAmount( ) {
       var amount = document.getElementById("amount").value;
       var installments = document.getElementById("installments").value;
       if ( installments != '' && installments != NaN) { 
          document.getElementById("eachPaymentAmount").value = (amount/installments);
       }   
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
{/if} {* not delete mode if*}      
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

{/if}
{* closing of main custom data if}