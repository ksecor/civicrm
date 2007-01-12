{if $action & 1024}
    {include file="CRM/Contribute/Form/Contribution/PreviewHeader.tpl"}
{/if}

<div class="form-item">
    <div id="help">
        <p>{ts}Please verify the information below carefully. Click <strong>Go Back</strong> if you need to make changes.{/ts}
            {if $contributeMode EQ 'notify'}
                {ts}Click the <strong>Continue</strong> button to go to PayPal, where you will select your payment method and complete the contribution.{/ts}
            {elseif ! $is_monetary}
                {ts}To complete this transaction, click the <strong>Continue</strong> button below.{/ts}
            {else}
                {ts}To complete your contribution, click the <strong>Make Contribution</strong> button below.{/ts}
            {/if}
        </p> 
    </div>
    
       
    {include file="CRM/Contribute/Form/Contribution/MembershipBlock.tpl" context="confirmContribution"}
   
    {if $amount GT 0 OR $minimum_fee GT 0}
    <div class="header-dark">
        {ts}Contribution Amount{/ts}
    </div>
    <div class="display-block">
        {if $is_separate_payment }
             {if $amount_block_is_active }   
              {ts}Contribution Amount{/ts}: <strong>{$amount|crmMoney}</strong><br />
              {$membership_name} {ts}Membership{/ts}: <strong>{$minimum_fee|crmMoney}</strong><br />
              <strong> -------------------------------------------</strong><br />
              {ts}Total{/ts}: <strong>{$amount+$minimum_fee|crmMoney}</strong><br />
             {else}
              {$membership_name} {ts}Membership{/ts}: <strong>{$minimum_fee|crmMoney}</strong>
             {/if}         
        {else}
           {if $amount }
            {ts}Total Amount{/ts}: <strong>{$amount|crmMoney} {if $amount_level } - {$amount_level} {/if}</strong>
           {else}
            <strong>{$minimum_fee|crmMoney}</strong> 
           {/if}
        {/if}
        {if $is_recur}
            {if $installments}
                <p><strong>{ts 1=$frequency_interval 2=$frequency_unit 3=$installments}I want to contribute this amount every %1 %2(s) for %3 installments.{/ts}</strong></p>
            {else}
                <p><strong>{ts 1=$frequency_interval 2=$frequency_unit}I want to contribute this amount every %1 %2(s).{/ts}</strong></p>
            {/if}
            <p>{ts}Your initial contribution will be processed once you complete the confirmation step. You will be able to modify or cancel future
                contributions at any time by logging in to your account.{/ts}</p>
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


    
    {if $contributeMode NEQ 'notify' and $is_monetary} {* In 'notify mode, contributor is taken to processor payment forms next *}
    <div class="messages status">
        <p>
        {ts}Your contribution will not be completed until you click the <strong>Make Contribution</strong> button. Please click the button one time only.{/ts}
        </p>
    </div>
    {/if}
    
    <div id="crm-submit-buttons">
        {$form.buttons.html}
    </div>
</div>
