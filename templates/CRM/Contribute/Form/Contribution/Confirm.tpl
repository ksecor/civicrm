{if $action eq 1024}
    {include file="CRM/Contribute/Form/Contribution/PreviewHeader.tpl"}
{/if}

<div class="form-item">
    <div id="help">
        <p>
        {ts}Please verify Contribution Amount, Billing Name and Address and Credit Card information carefully. Click <strong>Go Back</strong> if you need to make changes. To complete your contribution, click the <strong>Make Contribution</strong> button below.{/ts}
        </p> 
    </div>
    
       
    {include file="CRM/Contribute/Form/Contribution/MembershipBlock.tpl" context="confirmContribution"}
   
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
           <strong>{$amount|crmMoney}</strong>
           {else}
            <strong>{$minimum_fee|crmMoney}</strong> 
           {/if}
        {/if}    
    </div>
    
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
    
    <div class="messages status">
        <p>
        {ts}Your contribution will not be completed until you click the <strong>Make Contribution</strong> button. Please click the button one time only.{/ts}
        </p>
    </div>
    
    
    <div id="crm-submit-buttons">
        {$form.buttons.html}
    </div>
</div>
