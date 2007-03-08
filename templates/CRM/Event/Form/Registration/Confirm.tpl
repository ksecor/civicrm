<div class="form-item">
    <div id="help">
        <p>{ts}Please verify the information below. Click <strong>Go Back</strong>
            if you need to make changes. Otherwise, click the <strong>Continue</strong> button below
            to complete your registration.{/ts}</p>
    </div>

    {if $eventPage.confirm_text}
        <div id="intro_text">
        <p>{$eventPage.confirm_text}</p>
        </div>
    {/if}
    
    <div class="header-dark">
        {ts}Event Information{/ts}
    </div>
    <div class="display-block">
         {include file="CRM/Event/Form/Registration/EventInfoBlock.tpl"}
    </div>
    {if $paidEvent}
    <div class="header-dark">
        {ts}Registration Fee{/ts}
    </div>
    <div class="display-block">
        {if $amount}
            {ts}Amount :{/ts} 
            <strong>{$amount|crmMoney} {if $amount_level } - {$amount_level} {/if}</strong>
        {/if}
    </div>
    {else}
    <div class="header-dark">
        {ts}Email{/ts}
    </div>
    <div class="display-block">
        {$email}
    </div>
    {/if}

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

    {if $contributeMode ne 'notify' and $paidEvent}
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
    {if $contributeMode NEQ 'notify'} {* In 'notify mode, contributor is taken to processor payment forms next *}
    <div class="messages status">
        <p>
        {ts}Your registration will not be completed until you click the <strong>Continue</strong> button. Please click the button one time only.{/ts}
        </p>
    </div>
    {/if}    
   
    <div id="crm-submit-buttons">
     {$form.buttons.html}
    </div>

    {if $eventPage.confirm_footer_text}
        <div id="footer_text">
            <p>{$eventPage.confirm_footer_text}</p>
        </div>
    {/if}
</div>

