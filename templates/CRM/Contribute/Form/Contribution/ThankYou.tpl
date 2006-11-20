{if $action & 1024}
    {include file="CRM/Contribute/Form/Contribution/PreviewHeader.tpl"}
{/if}

<div class="form-item">
    <div id="thankyou_text">
        <p>
        {$thankyou_text}
        </p>
    </div>
    <div id="help">
        {* PayPal_Standard sets contribution_mode to 'notify'. We don't know if transaction is successful until we receive the IPN (payment notification) *}
        {if $contributeMode EQ 'notify'}
            <p>
            {ts}Your contribution has been submitted to PayPal for processing. Please print this page for your records.{/ts}
            {if $is_email_receipt}
                {ts 1=$email} An email receipt will be sent to %1 once the transaction is processed successfully.{/ts}</p>
            {/if}
            </p>
        {else}
            <p>{ts}Your contribution has been processed successfully. Please print this page for your records.{/ts}</p>
            {if $is_email_receipt}
                <p>{ts 1=$email}An email receipt for this contribution has also been sent to %1{/ts}</p>
            {/if}
        {/if}
    </div>
    
    {include file="CRM/Contribute/Form/Contribution/MembershipBlock.tpl" context="thankContribution"}
    <div class="header-dark">
        {ts}Contribution Information{/ts}
    </div>
    <div class="display-block">
        {if $membership_amount } 
              {ts}Contribution Amount{/ts}: <strong>{$amount|crmMoney}</strong><br />
              {$membership_name} {ts}Membership{/ts}: <strong>{$membership_amount|crmMoney}</strong><br />
              <strong> -------------------------------------------</strong><br />
              {ts}Total{/ts}: <strong>{$amount+$membership_amount|crmMoney}</strong><br />
        {else}
            {ts}Amount{/ts}: <strong>{$amount|crmMoney}</strong><br />
        {/if}
          {ts}Date{/ts}: <strong>{$receive_date|crmDate}</strong><br />
        {if $contributeMode ne 'notify' and $is_monetary}
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
            {ts 1=$cancelSubscriptionUrl}You can modify or cancel future contributions at any time by <a href="%1">logging in to your account</a>.{/ts}
            {if $is_email_receipt}
                {ts}You will receive an email receipt for each recurring contribution. The receipts will also
                include a link you can use if you decide to modify or cancel your future contributions.{/ts}
            {/if}
            </p>
        {/if}
    </div>

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

    {if $contributeMode ne 'notify' and $is_monetary}    
    <div class="header-dark">
        {ts}Billing Name and Address{/ts}
    </div>
    <div class="display-block">
        <strong>{$name}</strong><br />
        {$address|nl2br}
    </div>
    <div class="display-block">
        {$email}
    </div>
    {/if}

    {if $contributeMode eq 'direct'}
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
