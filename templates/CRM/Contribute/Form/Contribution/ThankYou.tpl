{if $action & 1024}
    {include file="CRM/Contribute/Form/Contribution/PreviewHeader.tpl"}
{/if}

<div class="form-item">
    {if $thankyou_text}
    <div id="thankyou_text">
        <p>
        {$thankyou_text}
        </p>
    </div>
    {/if}
    
    {* Show link to Tell a Friend (CRM-2153) *}
    {if $friendText}
        <div class="action-link" id="tell-a-friend">
            <a href="{$friendURL}" title="{$friendText}">&raquo; {$friendText}</a>
       </div>
    {/if}  

    <div id="help">
        {* PayPal_Standard sets contribution_mode to 'notify'. We don't know if transaction is successful until we receive the IPN (payment notification) *}
        {if $is_pay_later}
           <div class="bold">{$pay_later_receipt}</div>
            {if $is_email_receipt}
                <div>{ts 1=$email}An email confirmation with these payment instructions has been sent to %1{/ts}{if $onBehalfEmail AND ($onBehalfEmail neq $email)}{ts 1=$onBehalfEmail} and to %1{/ts}{/if}.</div>
            {/if}
        {elseif $contributeMode EQ 'notify'}
            <div>{ts}Your contribution has been submitted to {if $paymentProcessor.payment_processor_type EQ 'Google_Checkout'}Google{else}PayPal{/if} for processing. Please print this page for your records.{/ts}</div>
            {if $is_email_receipt}
                <div>{ts 1=$email}An email receipt will be sent to %1{/ts}{if $onBehalfEmail AND ($onBehalfEmail neq $email)}{ts 1=$onBehalfEmail} and to %1{/ts}{/if} {ts}once the transaction is processed successfully.{/ts}</div>
            {/if}
        {else}
            <div>{ts}Your transaction has been processed successfully. Please print this page for your records.{/ts}</div>
            {if $is_email_receipt}
                <div>{ts 1=$email}An email receipt has also been sent to %1{/ts}{if $onBehalfEmail AND ($onBehalfEmail neq $email)}{ts 1=$onBehalfEmail} and to %1{/ts}{/if}</div>
            {/if}
        {/if}
    </div>
    <div class="spacer"></div>
    
    {include file="CRM/Contribute/Form/Contribution/MembershipBlock.tpl" context="thankContribution"}

    {if $amount GT 0 OR $minimum_fee GT 0}
    <div class="header-dark">
        {if !$membershipBlock AND $amount}{ts}Contribution Information{/ts}{else}{ts}Membership Fee{/ts}{/if}
    </div>
    <div class="display-block">
        {if $membership_amount } 
              {$membership_name} {ts}Membership{/ts}: <strong>{$membership_amount|crmMoney}</strong><br />
              {if $amount}
                 {if ! $is_separate_payment }
		    {ts}Contribution Amount{/ts}: <strong>{$amount|crmMoney}</strong><br />
	         {else}
		    {ts}Additional Contribution{/ts}: <strong>{$amount|crmMoney}</strong><br />
  	         {/if}
              {/if} 		
              <strong> -------------------------------------------</strong><br />
              {ts}Total{/ts}: <strong>{$amount+$membership_amount|crmMoney}</strong><br />
        {else}
              {ts}Amount{/ts}: <strong>{$amount|crmMoney} {if $amount_level } - {$amount_level} {/if}</strong><br />
         {/if}
          {ts}Date{/ts}: <strong>{$receive_date|crmDate}</strong><br />
        {if $contributeMode ne 'notify' and $is_monetary and ! $is_pay_later and $trxn_id}
          {ts}Transaction #{/ts}: {$trxn_id}<br />
        {/if}
        {if $membership_trx_id}
           {ts}Membership Transaction #{/ts}: {$membership_trx_id}
        {/if}
        
        {* Recurring contribution information *}
        {if $is_recur}
            {if $installments}
                <p><strong>{ts 1=$frequency_interval 2=$frequency_unit 3=$installments}This recurring contribution will be automatically processed every %1 %2(s) for a total %3 installments (including this initial contribution).{/ts}</strong></p>
            {else}
                <p><strong>{ts 1=$frequency_interval 2=$frequency_unit}This recurring contribution will be automatically processed every %1 %2(s).{/ts}</strong></p>
            {/if}
            <p>
            {ts 1=$cancelSubscriptionUrl}You can modify or cancel future contributions at any time by <a href='%1'>logging in to your account</a>.{/ts}
            {if $is_email_receipt}
                {ts}You will receive an email receipt for each recurring contribution. The receipts will also include a link you can use if you decide to modify or cancel your future contributions.{/ts}
            {/if}
            </p>
        {/if}
        {if $is_pledge AND $is_pledge_frequency_interval}
            {if $pledge_frequency_interval GT 1}
                <p><strong>{ts 1=$pledge_frequency_interval 2=$pledge_frequency_unit 3=$pledge_installments}I pledge to contribute this amount every %1 %2s for %3 installments.{/ts}</strong></p>
            {else}
                <p><strong>{ts 1=$pledge_frequency_interval 2=$pledge_frequency_unit 3=$pledge_installments}I pledge to contribute this amount every %2 for %3 installments.{/ts}</strong></p>
            {/if}
            <p>
                {ts 1=$receiptFromEmail}Your initial pledge payment has been processed. You will be able to modify or cancel future pledge payments at any time by logging in to your account or contacting us at %1.{/ts}
                {if $max_reminders}
                    {ts 1=$intial_reminder_day}We will send you a payment reminder %1 days prior to each scheduled payment date. The reminder will include a link to a page where you can make your payment online.{/ts}
                {/if}
            </p>
        {/if}
    </div>
    {/if}
    
    {include file="CRM/Contribute/Form/Contribution/Honor.tpl"}
    {if $customPre}
         {foreach from=$customPre item=field key=cname}
              {if $field.groupTitle}
                {assign var=groupTitlePre  value=$field.groupTitle} 
              {/if}
         {/foreach}
        <div class="header-dark">
          {ts}{$groupTitlePre}{/ts}
         </div>  
         {include file="CRM/UF/Form/Block.tpl" fields=$customPre}
    {/if}

    {if $onBehalfName}
    <div class="header-dark">
        {ts}On Behalf Of{/ts}
    </div>
    <div class="display-block">
        <strong>{$onBehalfName}</strong><br />
        {$onBehalfAddress|nl2br}
    </div>
    <div class="display-block">
        {$onBehalfEmail}
    </div>
    {/if}

    {if $contributeMode ne 'notify' and ! $is_pay_later and $is_monetary and $amount GT 0}    
    <div class="header-dark">
        {ts}Billing Name and Address{/ts}
    </div>
    <div class="display-block">
        <strong>{$billingName}</strong><br />
        {$address|nl2br}
    </div>
    <div class="display-block">
        {$email}
    </div>
    {/if}

    {if $contributeMode eq 'direct' and ! $is_pay_later and $is_monetary and $amount GT 0}
    <div class="header-dark">
        {ts}Credit or Debit Card Information{/ts}
    </div>
    <div class="display-block">
        {$credit_card_type}<br />
        {$credit_card_number}<br />
        {ts}Expires{/ts}: {$credit_card_exp_date|truncate:7:''|crmDate}
    </div>
    {/if}

    {include file="CRM/Contribute/Form/Contribution/PremiumBlock.tpl" context="thankContribution"}

    {if $customPost}
         {foreach from=$customPost item=field key=cname}
              {if $field.groupTitle}
                {assign var=groupTitlePost  value=$field.groupTitle} 
              {/if}
         {/foreach}
        <div class="header-dark">
          {ts}{$groupTitlePost}{/ts}
         </div>  
         {include file="CRM/UF/Form/Block.tpl" fields=$customPost}
    {/if}

    <div id="thankyou_footer">
        <p>
        {$thankyou_footer}
        </p>
    </div>
	
</div>
