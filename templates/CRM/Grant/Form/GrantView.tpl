{* this template is used for viewing grant *} 
<div class="form-item">
<fieldset>
     <legend>{ts}View Grant{/ts}</legend>
     <dl class="html-adjust">
          <dt class="font-size12pt">{ts}Name{/ts}</dt><dd class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</dd>    
          <dt>{ts}Grant Status{/ts}          </dt> <dd>{$grantStatus}</dd>
          <dt>{ts}Grant Type{/ts}            </dt> <dd>{$grantType}</dd>
          <dt>{ts}Application Received{/ts}  </dt> <dd>{$application_received_date|crmDate}</dd>
          <dt>{ts}Grant Decision{/ts}        </dt> <dd>{$decision_date|crmDate}</dd>
          <dt>{ts}Money Transferred{/ts}     </dt> <dd>{$money_transfer_date|crmDate}</dd>
          <dt>{ts}Grant Report Due{/ts}      </dt> <dd>{$grant_due_date|crmDate}</dd>
          <dt>{ts}Amount Requested{/ts}      </dt> <dd>{$amount_total|crmMoney}</dd>
	      <dt>{ts}Amount Requested <br />
	          (original currency){/ts}   </dt> <dd>{$amount_requested|crmMoney}</dd>
          <dt>{ts}Amount Granted{/ts}        </dt> <dd>{$amount_granted|crmMoney}</dd>
          <dt>{ts}Grant Report Received?{/ts}</dt> <dd>{if $grant_report_received}{ts}Yes{/ts} {else}{ts}No{/ts}{/if} </dd>  
          <dt>{ts}Rationale{/ts}             </dt> <dd>{$rationale}</dd>
          <dt>{ts}Notes{/ts}                 </dt> <dd>{$note}</dd>
{if $attachment}
	  <div class="spacer"></div>
          <dt>{ts}Attachment(s){/ts}         </dt> <dd>{$attachment}</dd>
{/if}
	  <div class="spacer"></div>
           {include file="CRM/Custom/Page/CustomDataView.tpl"} 
     </dl>
    <div class="spacer"></div>  
    <dl class="html-adjust">
         <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>        
    
