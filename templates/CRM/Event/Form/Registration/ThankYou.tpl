{if $action & 1024}
    {include file="CRM/Event/Form/Registration/PreviewHeader.tpl"}
{/if}
<div class="form-item">
    {if $event.thankyou_text} 
        <div id="intro_text">
            <p>
            {$event.thankyou_text}
            </p>
        </div>
    {/if}
    
    {* Show link to Tell a Friend (CRM-2153) *}
    {if $friendText}
        <div id="tell-a-friend">
            <a href="{$friendURL}" title="{$friendText}" class="button"><span>&raquo; {$friendText}</span></a>
       </div><br /><br />
    {/if}  

    <div id="help">
        {* PayPal_Standard sets contribution_mode to 'notify'. We don't know if transaction is successful until we receive the IPN (payment notification) *}
        {if $is_pay_later and $paidEvent}
            <div class="bold">{$pay_later_receipt}</div>
            {if $is_email_confirm}
                <p>{ts 1=$email}An email with event details has been sent to %1.{/ts}</p>
            {/if}
        {elseif $contributeMode EQ 'notify' and $paidEvent}
            <p>{ts}Your registration payment has been submitted to {$paymentProcessor.processorName} for processing. Please print this page for your records.{/ts}</p>
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
    <div class="spacer"></div>

    <div class="header-dark">
        {ts}Event Information{/ts}
    </div>
    <div class="display-block">
        {include file="CRM/Event/Form/Registration/EventInfoBlock.tpl" context="ThankYou"}
    </div>

    {if $paidEvent}
    <div class="header-dark">
        {$event.fee_label}
    </div>
    <div class="display-block">
        {if $lineItem}
            {include file="CRM/Event/Form/Registration/LineItem.tpl"}<br />
        {elseif $amount || $amount == 0}
            {foreach from= $finalAmount item=amount key=level}  
                <strong>{$amount.amount|crmMoney} &nbsp;&nbsp; {$amount.label}</strong><br />	
            {/foreach}
            {if $totalAmount}
                <br /><strong>{ts}Event Total{/ts}: {$totalAmount|crmMoney}</strong>
                {if $hookDiscount.message}
                    <em>({$hookDiscount.message})</em>
                {/if}
                <br />
            {/if}	
        {/if}
        {if $receive_date}
            <strong>{ts}Transaction Date{/ts}</strong>: {$receive_date|crmDate}<br />
        {/if}
        {if $contributeMode ne 'notify' AND $trxn_id}
            <strong>{ts}Transaction #{/ts}: {$trxn_id}</strong><br />
        {/if}
    </div>
    {elseif $participantInfo}
        <div class="header-dark">
            {ts}Additional Participant Email(s){/ts}
        </div>
        <div class="display-block">
            {foreach from=$participantInfo  item=mail key=no}  
                <strong>{$mail}</strong><br />	
            {/foreach}
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
        {foreach from=$customPre item=field key=customName}
            {if $field.groupTitle}
                {assign var=groupTitlePre  value=$field.groupTitle} 
            {/if}
        {/foreach}
        <div class="header-dark">
	    {$groupTitlePre}
        </div>  
        {include file="CRM/UF/Form/Block.tpl" fields=$customPre}
    {/if}

    {if $contributeMode ne 'notify' and $paidEvent and ! $is_pay_later and ! $isAmountzero}    
    <div class="header-dark">
        {ts}Billing Name and Address{/ts}
    </div>
    <div class="display-block">
        <strong>{$billingName}</strong><br />
        {$address|nl2br}
    </div>
    {/if}

    {if $contributeMode eq 'direct' and $paidEvent and ! $is_pay_later and !$isAmountzero}
    <div class="header-dark">
        {ts}Credit Card Information{/ts}
    </div>
    <div class="display-block">
        {$credit_card_type}<br />
        {$credit_card_number}<br />
        {ts}Expires{/ts}: {$credit_card_exp_date|truncate:7:''|crmDate}
    </div>
    {/if}

    {if $customPost}
        {foreach from=$customPost item=field key=customName}
            {if $field.groupTitle}
                {assign var=groupTitlePost  value=$field.groupTitle} 
            {/if}
        {/foreach}
        <div class="header-dark">
            {$groupTitlePost}
        </div>  
        {include file="CRM/UF/Form/Block.tpl" fields=$customPost}
    {/if}

    {*display Additional Participant Info*}
    {if $customProfile}
        {foreach from=$customProfile item=value key=customName}
            <div class="header-dark">
                {ts 1=$customName+1}Participant Information - Participant %1{/ts}	
            </div>
            {foreach from=$value item=val key=field}
                {if $field}
                    {if $field eq 'customPre' }
                        <fieldset><legend>{$groupTitlePre}</legend>
                    {else}
                        <fieldset><legend>{$groupTitlePost}</legend>
                    {/if}
                    <table class="form-layout-compressed">	
                    {foreach from=$val item=v key=f}
                        <tr>
                            <td class="label">{$f}</td><td class="view-value">{$v}</td>
                        </tr>
                    {/foreach}
                    </table>
                    </fieldset>
                {/if}
            {/foreach}
            <div class="spacer"></div>  
        {/foreach}
    {/if}

    {if $event.thankyou_footer_text}
        <div id="footer_text">
            <p>{$event.thankyou_footer_text}</p>
        </div>
    {/if}
    
    <div class="action-link">
        <a href="{crmURL p='civicrm/event/info' q="reset=1&id=`$event.id`"}">&raquo; {ts 1=$event.event_title}Back to "%1" event information{/ts}</a>
    </div>

    {if $event.is_public }
        {include file="CRM/Event/Page/iCalLinks.tpl"}
    {/if} 
</div>
