<div class="form-item">  
<fieldset>
      <legend>{ts}View Pledge{/ts}</legend>
      <dl>  
        <dt class="font-size12pt">{ts}From{/ts}</dt><dd class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</dd>
        <dt>{ts}Contribution Type{/ts}</dt><dd>{$contribution_type}&nbsp;
        {if $is_test}
          {ts}(test){/ts}
        {/if}
        </dd>
        <dt>{ts}Total Amount{/ts}</dt><dd class="bold">{$amount|crmMoney}&nbsp;</dd>
        <dt>{ts}Frequency{/ts}</dt><dd>{$frequency_interval} {$frequency_unit|capitalize}(s)&nbsp;</dd>
	   {if $acknowledge_date}	
        	<dt>{ts}Received{/ts}</dt><dd>{if $acknowledge_date}{$acknowledge_date|truncate:10:''|crmDate}{else}({ts}pending{/ts}){/if}&nbsp;</dd>
	{/if}
        <dt>{ts}Pledge Status{/ts}</dt><dd{if $contribution_status_id eq 3} class="font-red bold"{/if}>{$contribution_status} </dd>
	{if $start_date}     
       		<dt>{ts}Start Date{/ts}</dt><dd>{$start_date|truncate:10:''|crmDate}</dd
	{/if}
	 {if $end_date}    
        	<dt>{ts}End Date{/ts}</dt><dd>{$end_date|truncate:10:''|crmDate}</dd>
	 {/if}

        {if $cancel_date}
            <dt>{ts}Cancelled Date{/ts}</dt><dd>{$cancel_date|truncate:10:''|crmDate}</dd>
        {/if}

        <dt>{ts}Paid By{/ts}</dt><dd>{$payment_instrument}&nbsp;</dd>

        {foreach from=$note item="rec"}
		    {if $rec }
			<dt>{ts}Note:{/ts}</dt><dd>{$rec}</dd>	
	   	    {/if}
        {/foreach}
       
        {if $honor_type_id}
            <dt>{ts}{$honor_type_id}{/ts}</dt><dd>{$honor_contact_id}&nbsp;</dd>
        {/if}

    </dl>
    
    {include file="CRM/Contact/Page/View/InlineCustomData.tpl" mainEditForm=1}

    <dl>
        <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>  
</div>  
 
