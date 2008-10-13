{if $action & 1024}
    {include file="CRM/Contribute/Form/Contribution/PreviewHeader.tpl"}
{/if}

<div class="form-item">
    <div id="help">
        <p>{ts}Please verify the information below carefully. Click <strong>Go Back</strong> if you need to make changes.{/ts}
            {if $contributeMode EQ 'notify' and ! $is_pay_later}
               {if $paymentProcessor.payment_processor_type EQ 'Google_Checkout'} 
                  {ts}Click the <strong>Google Checkout</strong> button to checkout to Google, where you will select your payment method and complete the contribution.{/ts}
               {else} 
                {ts}Click the <strong>Continue</strong> button to go to PayPal, where you will select your payment method and complete the contribution.{/ts}
               {/if} 
            {elseif ! $is_monetary or $amount LE 0.0 or $is_pay_later}
                {ts}To complete this transaction, click the <strong>Continue</strong> button below.{/ts}
            {else}
                {ts}To complete your contribution, click the <strong>Make Contribution</strong> button below.{/ts}
            {/if}
        </p> 
    </div>
    {if $is_pay_later}
        <div class="bold">{$pay_later_receipt}</div>
    {/if}
    
    {include file="CRM/Contribute/Form/Contribution/MembershipBlock.tpl" context="confirmContribution"}

    {if $amount GT 0 OR $minimum_fee GT 0}
    <div class="header-dark">
        {if !$membershipBlock AND $amount }{ts}Contribution Amount{/ts}{else}{ts}Membership Fee{/ts} {/if}
    </div>
    <div class="display-block">
        {if $is_separate_payment }
            {if $amount AND $minimum_fee}
                  {$membership_name} {ts}Membership{/ts}: <strong>{$minimum_fee|crmMoney}</strong><br />
                  {ts}Additional Contribution{/ts}: <strong>{$amount|crmMoney}</strong><br />
                  <strong> -------------------------------------------</strong><br />
                  {ts}Total{/ts}: <strong>{$amount+$minimum_fee|crmMoney}</strong><br />
            {elseif $amount }
                {ts}Amount{/ts}: <strong>{$amount|crmMoney} {if $amount_level } - {$amount_level} {/if}</strong>
            {else}
                  {$membership_name} {ts}Membership{/ts}: <strong>{$minimum_fee|crmMoney}</strong>
            {/if}
        {else}
           {if $amount }
                {ts}Total Amount{/ts}: <strong>{$amount|crmMoney} {if $amount_level } - {$amount_level} {/if}</strong>
           {else}
                {$membership_name} {ts}Membership{/ts}: <strong>{$minimum_fee|crmMoney}</strong>
           {/if}
        {/if}
        {if $is_recur}
            {if $installments}
                <p><strong>{ts 1=$frequency_interval 2=$frequency_unit 3=$installments}I want to contribute this amount every %1 %2(s) for %3 installments.{/ts}</strong></p>
            {else}
                <p><strong>{ts 1=$frequency_interval 2=$frequency_unit}I want to contribute this amount every %1 %2(s).{/ts}</strong></p>
            {/if}
            <p>{ts}Your initial contribution will be processed once you complete the confirmation step. You will be able to modify or cancel future contributions at any time by logging in to your account.{/ts}</p>
        {/if}
        {if $is_pledge }
            {if $pledge_frequency_interval GT 1}
                <p><strong>{ts 1=$pledge_frequency_interval 2=$pledge_frequency_unit 3=$pledge_installments}I pledge to contribute this amount every %1 %2s for %3 installments.{/ts}</strong></p>
            {else}
                <p><strong>{ts 1=$pledge_frequency_interval 2=$pledge_frequency_unit 3=$pledge_installments}I pledge to contribute this amount every %2 for %3 installments.{/ts}</strong></p>
            {/if}
            {if $is_pay_later}
                <p>{ts 1=$receiptFromEmail}Click &quot;Make Contribution&quot; below to register your pledge. You will be able to modify or cancel future pledge payments at any time by logging in to your account or contacting us at %1.{/ts}</p>
            {else}
                <p>{ts 1=$receiptFromEmail}Your initial pledge payment will be processed when you click &quot;Make Contribution&quot; below. You will be able to modify or cancel future pledge payments at any time by logging in to your account or contacting us at %1.{/ts}</p>
            {/if}
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
    {if $pcpBlock}
    <div class="header-dark">
        {ts}Personal Campaign Page{/ts}
    </div>
    <div class="display-block">
	<strong>Display In Roll</strong> : {if $pcp_display_in_roll}{ts}Yes{/ts}<br />{else}{ts}No{/ts}<br />{/if}
	{if $pcp_roll_nickname}<strong>Nick Name</strong> : {$pcp_roll_nickname}<br />{/if}
        {if $pcp_personal_note}<strong>Note</strong> : {$pcp_personal_note|truncate}<br />{/if}
    </div>
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
    {/if}
    {if $email}
    <div class="header-dark">
        {ts}Your Email{/ts}
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
        {ts}Expires{/ts}: {$credit_card_exp_date|truncate:7:''|crmDate}<br />
    </div>
    {/if}
    
    {include file="CRM/Contribute/Form/Contribution/PremiumBlock.tpl" context="confirmContribution"}
    
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
  
    {if $contributeMode NEQ 'notify' and $is_monetary and $amount GT 0} {* In 'notify mode, contributor is taken to processor payment forms next *}
    <div class="messages status">
        <p>
        {if $is_pay_later}
            {ts}Your contribution will not be completed until you click the <strong>Continue</strong> button. Please click the button one time only.{/ts}
        {else}
            {ts}Your contribution will not be completed until you click the <strong>Make Contribution</strong> button. Please click the button one time only.{/ts}
        {/if}
        </p>
    </div>
    {/if}
    
    {if $paymentProcessor.payment_processor_type EQ 'Google_Checkout' and $is_monetary and $amount GT 0 and ! $is_pay_later}
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
</div>
