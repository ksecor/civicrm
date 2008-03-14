{* this template is used for adding/editing/deleting contribution *} 
<div class="form-item"> 
<fieldset><legend>{if $action eq 1}{ts}New Contribution{/ts}{elseif $action eq 8}{ts}Delete Contribution{/ts}{else}{ts}Edit Contribution{/ts}{/if}</legend> 
   
   {if $action eq 8} 
      <div class="messages status"> 
        <dl> 
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt> 
          <dd> 
          {ts}WARNING: Deleting this contribution will result in the loss of the associated financial transactions (if any).{/ts} {ts}Do you want to continue?{/ts} 
          </dd> 
       </dl> 
      </div> 
   {else} 
      <table class="form-layout-compressed">
        <tr>
            <td class="font-size12pt right"><strong>{ts}Contributor{/ts}</strong></td><td class="font-size12pt"><strong>{$displayName}</strong></td>
        </tr>
        <tr><td class="label">{$form.contribution_type_id.label}</td><td>{$form.contribution_type_id.html}&nbsp;
        {if $is_test}
        {ts}(test){/ts}
        {/if}
        </td></tr> 
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Select the appropriate contribution type for this transaction.{/ts}</td></tr>
        <tr><td class="label">{$form.total_amount.label}</td><td>{$form.total_amount.html|crmMoney}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Actual amount given by contributor.{/ts}</td></tr>
        <tr><td class="label">{$form.receive_date.label}</td><td>{$form.receive_date.html}
        {if $hideCalender neq true}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=receive_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_1}
        {/if}    
        </td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}The date this contribution was received.{/ts}</td></tr>

        <tr><td class="label">{$form.payment_instrument_id.label}</td><td>{$form.payment_instrument_id.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Leave blank for non-monetary contributions.{/ts}</td></tr>
        <tr><td class="label">{$form.source.label}</td><td>{$form.source.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Optional identifier for the contribution source (campaign name, event, mailer, etc.).{/ts}</td></tr>
        {if $email}
            <tr><td class="label">{$form.is_email_receipt.label}</td><td>{$form.is_email_receipt.html}</td></tr>
            <tr><td class="label">&nbsp;</td><td class="description">{ts}Automatically email a receipt for this contribution to {$email}?{/ts}</td></tr>
        {/if}
        <tr id="receiptDate"><td class="label">{$form.receipt_date.label}</td><td>{$form.receipt_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=receipt_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_2}<br />
            <span class="description">{ts}Date that a receipt was sent to the contributor.{/ts}</span></td></tr>
        <tr><td class="label">{$form.contribution_status_id.label}</td><td>{$form.contribution_status_id.html}</td></tr>
        {* Cancellation fields are hidden unless contribution status is set to Cancelled *}
        <tr id="cancelInfo"> 
           <td>&nbsp;</td> 
           <td><fieldset><legend>{ts}Cancellation Information{/ts}</legend>
                <table class="form-layout-compressed">
                  <tr id="cancelDate"><td class="label">{$form.cancel_date.label}</td><td>{$form.cancel_date.html}
                   {if $hideCalendar neq true}
                     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_4}
                     {include file="CRM/common/calendar/body.tpl" dateVar=cancel_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_4}
                   {/if}
                   </td></tr>
                  <tr id="cancelDescription"><td class="label">&nbsp;</td><td class="description">{ts}Enter the cancellation date, or you can skip this field and the cancellation date will be automatically set to TODAY.{/ts}</td></tr>
                  <tr id="cancelReason"><td class="label" style="vertical-align: top;">{$form.cancel_reason.label}</td><td>{$form.cancel_reason.html|crmReplace:class:huge}</td></tr>
               </table>
               </fieldset>
           </td>
        </tr>
      </table>
      {include file="CRM/Contact/Page/View/CustomData.tpl" mainEditForm=1}
    {literal}
    <script type="text/javascript">
    function reload(refresh) {
        var contributionType = document.getElementById("contribution_type_id");
        var url = {/literal}"{$refreshURL}"{literal};
        var post = url + "&subType=" + contributionType.value;
        if ( refresh ) {
            window.location= post; 
        }
     }

     function verify( ) {
       var element = document.getElementsByName("is_email_receipt");
        if ( element[0].checked ) {
         var ok = confirm( "Click OK to save this contribution record AND send a receipt to the contributor now." );    
          if (!ok ) {
            return false;
          }
        }
     }
     function status() {
       document.getElementById("cancel_date[M]").value = "";
       document.getElementById("cancel_date[d]").value = "";
       document.getElementById("cancel_date[Y]").value = "";
       document.getElementById("cancel_reason").value = "";
     }

    </script>
    {/literal}

<div id="adiitionalInfo">
    {include file="CRM/Contribute/Form/AdditionalInfo.tpl"}
</div>
{/if}
    <dl>    
       <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>   
    </dl> 
</fieldset> 
</div> 

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_email_receipt"
    trigger_value       =""
    target_element_id   ="receiptDate" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 1
}

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="contribution_status_id"
    trigger_value       = '3'
    target_element_id   ="cancelInfo" 
    target_element_type ="table-row"
    field_type          ="select"
    invert              = 0
}

