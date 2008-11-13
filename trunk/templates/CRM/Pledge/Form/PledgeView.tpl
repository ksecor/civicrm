<div class="form-item">  
<fieldset>
      <legend>{ts}View Pledge{/ts}</legend>
      <dl>  
        <dt class="font-size12pt">{ts}Pledge By{/ts}</dt><dd class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</dd>
        <dt>{ts}Total Amount{/ts}</dt><dd class="bold">{$amount|crmMoney}&nbsp;</dd>
        <dt>{ts}To be paid in{/ts}</dt><dd>{$installments}&nbsp;&nbsp;{ts}installments of{/ts} {$eachPaymentAmount|crmMoney}&nbsp;&nbsp;{ts}every{/ts}&nbsp;&nbsp;{$frequency_interval}&nbsp;{$frequencyUnit}</dd>
 	<dt>{ts}Payments are due on the{/ts}</dt><dd>{$frequency_day}&nbsp;day of the period</dd>

      	{if $start_date}     
       		<dt>{ts}Pledge Made{/ts}</dt><dd>{$start_date|truncate:10:''|crmDate}</dd>
        	<dt>{ts}Payment Start{/ts}</dt><dd>{$create_date|truncate:10:''|crmDate}</dd>
	{/if}
        {if $end_date}    
        	<dt>{ts}End Date{/ts}</dt><dd>{$end_date|truncate:10:''|crmDate}</dd>
	{/if}
        {if $cancel_date}
            <dt>{ts}Cancelled Date{/ts}</dt><dd>{$cancel_date|truncate:10:''|crmDate}</dd>
        {/if}
            <dt>{ts}Contribution Type{/ts}</dt><dd>{$contribution_type}&nbsp;
        {if $is_test}
          {ts}(test){/ts}
        {/if}
        </dd>
        {if $acknowledge_date}	
            <dt>{ts}Received{/ts}</dt><dd>{$acknowledge_date|truncate:10:''|crmDate}&nbsp;</dd>
	{/if}
        {if $contribution_page}
            <dt>{ts}Self-service Payments Page{/ts}</dt><dd>{$contribution_page}</dd>
        {/if}   
        <dt>{ts}Pledge Status{/ts}</dt><dd{if $status_id eq 3} class="font-red bold"{/if}>{$pledge_status} </dd>
	
       
        {if $honor_contact_id}
            <dt>{ts}{$honor_type}{/ts}</dt><dd>{$honor_display}&nbsp;</dd>
        {/if}
        <dt>{ts}Initial Reminder Day{/ts}</dt><dd>{$initial_reminder_day}&nbsp;days prior to schedule date </dd>
        <dt>{ts}Maximum Reminders Send{/ts}</dt><dd>{$max_reminders}&nbsp;</dd>
        <dt>{ts}Send additional reminders{/ts}</dt><dd>{$additional_reminder_day}&nbsp;days after the last one sent</dd>	
    </dl>
    
    {include file="CRM/Contact/Page/View/InlineCustomData.tpl" mainEditForm=1}

    <dl>
        <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>  
</div>  
 
