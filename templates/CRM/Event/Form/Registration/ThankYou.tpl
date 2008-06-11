<div class="form-item">
    {if $eventPage.thankyou_text} 
        <div id="intro_text">
            <p>
            {$eventPage.thankyou_text}
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
        {if $is_pay_later and $paidEvent}
           <div class="bold">{$pay_later_receipt}</div>
            {if $is_email_confirm}
                <p>{ts 1=$email}A registration confirmation email will be sent to %1 once the transaction is processed successfully.{/ts}</p>
            {/if}
        {elseif $contributeMode EQ 'notify' and $paidEvent}
            <p>{ts}Your registration payment has been submitted to {if $paymentProcessor.payment_processor_type EQ 'Google_Checkout'}Google{else}PayPal{/if} for processing. Please print this page for your records.{/ts}</p>
            {if $is_email_confirm}
                <p>{ts 1=$email}A registration confirmation email will be sent to %1 once the transaction is processed successfully.{/ts}</p>
            {/if}
        {else}
            <p>{ts}Your registration has been processed successfully. Please print this page for your records.{/ts}</p>
            {if $is_email_confirm}
                <p>{ts 1=$email}A registration confirmation email has also been sent to %1{/ts}</p>
            {/if}
        {/if}
    </div>

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
            {include file="CRM/Event/Form/Registration/LineItem.tpl}<br />
        {elseif $amount || $amount == 0}
        {foreach from= $finalAmount item=amount key=level}  
          <strong>{$amount|crmMoney} &nbsp;&nbsp; {$level}</strong><br />	
        {/foreach}
        {if $totalAmount}
	<br /> <strong>{ts}Event Total{/ts}: {$totalAmount|crmMoney}</strong><br />
        {/if}	
        {/if}
        {if $receive_date}
        <strong>{ts}Transaction Date{/ts}: {$receive_date|crmDate}</strong><br />
        {/if}
        {if $contributeMode ne 'notify' AND $trxn_id}
          <strong>{ts}Transaction #{/ts}: {$trxn_id}</strong><br />
        {/if}
    </div>
    {/if}

    <div class="header-dark">
        {ts}Registered Email{/ts}
    </div>
    <div class="display-block">
        {$email}
    </div>

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

    {if $contributeMode ne 'notify' and $paidEvent and ! $is_pay_later and ! $isAmountzero}    
    <div class="header-dark">
        {ts}Billing Name and Address{/ts}
    </div>
    <div class="display-block">
        <strong>{$name}</strong><br />
        {$address|nl2br}
    </div>
    {/if}

    {if $contributeMode eq 'direct' and $paidEvent and ! $is_pay_later and !$isAmountzero}
    <div class="header-dark">
        {ts}Credit or Debit Card Information{/ts}
    </div>
    <div class="display-block">
        {$credit_card_type}<br />
        {$credit_card_number}<br />
        {ts}Expires{/ts}: {$credit_card_exp_date|truncate:7:''|crmDate}
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

    {if $customProfile}
    <div class="header-dark">
         {ts}Information Of Additional Participants{/ts}
    </div>
     {foreach from=$customProfile item=value key=name}
        {foreach from=$value item=val key=field}
           {if $field}
               {if $field eq 'customPre' }
               <div class="bold">
                    {ts}{$customPre_grouptitle}{/ts}
               {else}
                    {ts}{$customPost_grouptitle}{/ts}
               </div>
               {/if}
               {foreach from=$val item=v key=f}
                  <strong>{$f}</strong>:{$v}
               {/foreach}
          {/if}
        {/foreach}
     {/foreach} 
    {/if}

    {if $eventPage.thankyou_footer_text}
        <div id="footer_text">
            <p>{$eventPage.thankyou_footer_text}</p>
        </div>
    {/if}
    {if $event.is_public }
      <div class="action-link">
        {capture assign=icalFile}{crmURL p='civicrm/event/ical' q="reset=1&id=`$event.id`"}{/capture}
        {capture assign=icalFeed}{crmURL p='civicrm/event/ical' q="reset=1&page=1&id=`$event.id`"}{/capture}

        <a href="{$icalFile}">&raquo; {ts}Download iCalendar File{/ts}</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{$icalFeed}" title="{ts}iCalendar Feed{/ts}"><img src="{$config->resourceBase}i/ical_feed.gif" alt="{ts}iCalendar Feed{/ts}" /></a> 
      </div>
    {/if} 
</div>
