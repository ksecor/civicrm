{* View existing event registration record. *}
<div class="form-item">  
<fieldset>
        <legend>{ts}View Participant{/ts}</legend>
        <dl>  
        <dt class="font-size12pt">{ts}Name{/ts}</dt><dd class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</dd>
        <dt>{ts}Event{/ts}</dt><dd>{$event}&nbsp;</dd>
        <dt>{ts}Participant Role{/ts}</dt><dd>{$role}&nbsp;</dd>
        <dt>{ts}Registration Date and Time{/ts}</dt><dd>{$register_date|crmDate}&nbsp;</dd>
        <dt>{ts}Status{/ts}</dt><dd>{$status}&nbsp;</dd>
        {if $source}
            <dt>{ts}Event Source{/ts}</dt><dd>{$source}&nbsp;</dd>
        {/if}
        {if $event_level}
            {if $line_items}
                {assign var="total_price" value=0}
                <dt>{ts}Event Fees{/ts}</dt>
                <dd>
                    <table class="report">
                        <tr class="columnheader">
                            <th>{ts}Item{/ts}</th>
                            <th>{ts}Qty{/ts}</th>
                            <th>{ts}Unit Price{/ts}</th>
                            <th>{ts}Total{/ts}</th>
                        </tr>
                        {foreach from=$line_items item=line}
                            {assign var="total_price" value=`$total_price+$line.line_total`}
                        <tr>
                            <td>{$line.label}</td>
                            <td>{$line.qty}</td>
                            <td class="right">{$line.unit_price|crmMoney}</td>
                            <td class="right">{$line.line_total|crmMoney}</td>
                        </tr>
                        {/foreach}
                        <tr>
                            <td colspan="3"><strong>{ts}Total{/ts}</strong>:</td>
                            <td class="right"><strong>{$total_price|crmMoney}</strong></td>
                        </tr>
                    </table>
                </dd>
            {else}
                <dt>{ts}Event Level{/ts}</dt><dd>{$event_level}&nbsp;</dd>
            {/if}
        {/if}
        {foreach from=$note item="rec"}
	    {if $rec }
		<dt>{ts}Note:{/ts}</dt><dd>{$rec}</dd>	
	    {/if}
        {/foreach}
         
        {include file="CRM/Contact/Page/View/InlineCustomData.tpl" mainEditForm=1}  
        <dl>
           <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
    </dl>
	{if $contribution.contribution_id}
           {include file="CRM/Event/Form/AssociatedPayment.tpl"}
        {/if}
</fieldset>  
</div>
