{if $action & 1024}
    {include file="CRM/Event/Form/Registration/PreviewHeader.tpl"}
{/if}

{include file="CRM/common/TrackingFields.tpl"}

<div class="form-item">
    <div id="help">
        {ts}Please verify the information below. Click <strong>Go Back</strong> if you need to make changes.{/ts}
    {if $contributeMode EQ 'notify' and $paymentProcessor.payment_processor_type EQ 'Google_Checkout' and !$is_pay_later and ! $isAmountzero } 
        {ts}Click the <strong>Google Checkout</strong> button to checkout to Google, where you will select your payment method and complete the registration.{/ts}
    {else}
	{ts}Otherwise, click the <strong>Continue</strong> button below to complete your registration.{/ts}
    {/if}
    </div>

    {if $event.confirm_text}
        <div id="intro_text">
        <p>{$event.confirm_text}</p>
        </div>
    {/if}
    {if $is_pay_later}
        <div class="bold">{$pay_later_receipt}</div>
    {/if}
    
    <div class="header-dark">
        {ts}Event Information{/ts}
    </div>
    <div class="display-block">
         {include file="CRM/Event/Form/Registration/EventInfoBlock.tpl"}
    </div>
    {if $paidEvent} 
    <div class="header-dark">
        {$event.fee_label}
    </div>
    <div class="display-block">
        {if $lineItem}
            {include file="CRM/Event/Form/Registration/LineItem.tpl}
        {elseif $amount || $amount == 0}
            {foreach from= $amount item=amount key=level}  
              <strong>{$amount.amount|crmMoney} &nbsp;&nbsp; {$amount.label}</strong><br />	
            {/foreach}
            {if $totalAmount}
                <br /><strong>{ts}Total Amount{/ts}:&nbsp;&nbsp;{$totalAmount|crmMoney}</strong>
            {/if}	 		
            {if $hookDiscount.message}
                <em>({$hookDiscount.message})</em>
            {/if}
        {/if}
    </div>
    {/if}
	
    <div class="header-dark">
    	{ts}Registered Email{/ts}
    </div>
    <div class="display-block">
        {$email}
    </div>
    {if $event.participant_role neq 'Attendee' and $defaultRole}
        <div class="header-dark">
            {ts}Participant Role{/ts}
        </div>
        <div class="display-block">
            {$event.participant_role}
        </div>
    {/if}


    {if $customPre}
         {foreach from=$customPre item=field key=cname}
              {if $field.groupTitle}
                {assign var=groupTitlePre  value=$field.groupTitle} 
              {/if}
         {/foreach}
        <div class="header-dark">
          {$groupTitlePre}
         </div>  
         {include file="CRM/UF/Form/Block.tpl" fields=$customPre}
    {/if}
    {if $customPost}
         {foreach from=$customPost item=field key=cname}
              {if $field.groupTitle}
                {assign var=groupTitlePost  value=$field.groupTitle} 
              {/if}
         {/foreach}
        <div class="header-dark">
          {$groupTitlePost}
         </div>  
         {include file="CRM/UF/Form/Block.tpl" fields=$customPost}
    {/if}

    {*display Additional Participant Profile Information*}
    {if $addParticipantProfile}
        {foreach from=$addParticipantProfile item=participant key=participantNo}
            <div class="header-dark">
                {ts 1=$participantNo+1}Participant Information - Participant %1{/ts}	
            </div>
            {if $participant.customPre}
                <fieldset><legend>{$participant.customPreGroupTitle}</legend>
                     <table class="form-layout-compressed">
                        {foreach from=$participant.customPre item=value key=field}
                        <tr>
                            <td class="label">{$field}</td><td class="view-value">{$value}</td>
                        </tr>
                        {/foreach}
                     </table>
                </fieldset>
            {/if}

            {if $participant.customPost}
                <fieldset><legend>{$participant.customPostGroupTitle}</legend>
                     <table class="form-layout-compressed">
                        {foreach from=$participant.customPost item=value key=field}
                        <tr>
                            <td class="label">{$field}</td><td class="view-value">{$value}</td>
                        </tr>
                        {/foreach}
                     </table>
                </fieldset>
            {/if}
        <div class="spacer"></div>
        {/foreach}
    {/if}

    {if $contributeMode ne 'notify' and
        ! $is_pay_later             and
        $paidEvent                  and
	! $isAmountzero 
	}
    <div class="header-dark">
        {ts}Billing Name and Address{/ts}
    </div>
    <div class="display-block">
        <strong>{$billingName}</strong><br />
        {$address|nl2br}
    </div>
    {/if}
    
    {if $contributeMode eq 'direct' and
        ! $is_pay_later and !$isAmountzero}
    <div class="header-dark">
        {ts}Credit Card Information{/ts}
    </div>
    <div class="display-block">
        {$credit_card_type}<br />
        {$credit_card_number}<br />
        {ts}Expires{/ts}: {$credit_card_exp_date|truncate:7:''|crmDate}<br />
    </div>
    {/if}
    
    {if $contributeMode NEQ 'notify'} {* In 'notify mode, contributor is taken to processor payment forms next *}
    <div class="messages status">
        <p>
        {ts}Your registration will not be completed until you click the <strong>Continue</strong> button. Please click the button one time only.{/ts}
        </p>
    </div>
    {/if}    
   
    {if $paymentProcessor.payment_processor_type EQ 'Google_Checkout' and $paidEvent and !$is_pay_later and ! $isAmountzero}
        <fieldset><legend>{ts}Checkout with Google{/ts}</legend>
         <table class="form-layout-compressed">
          <tr><td class="description">{ts}Click the Google Checkout button to continue.{/ts}</td></tr>
          <tr><td>{$form._qf_Confirm_next_checkout.html} <span style="font-size:11px; font-family: Arial, Verdana;">Checkout securely.  Pay without sharing your financial information. </span></td></tr>
         </table>
        </fieldset>    
    {/if}

    <div id="crm-submit-buttons">
     {$form.buttons.html}
    </div>

    {if $event.confirm_footer_text}
        <div id="footer_text">
            <p>{$event.confirm_footer_text}</p>
        </div>
    {/if}
</div>
{include file="CRM/common/showHide.tpl"}
