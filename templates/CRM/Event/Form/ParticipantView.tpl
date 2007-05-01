<div class="form-item">  
<fieldset>
        {if $history neq 1}
            <legend>{ts}View Participant{/ts}</legend>
        {else}
            <legend>{ts}View Activity History{/ts}</legend>
        {/if}
        <dl>  
        <dt class="font-size12pt">{ts}Name{/ts}</dt><dd class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</dd>
        <dt>{ts}Event{/ts}</dt><dd>{$event}&nbsp;</dd>
        <dt>{ts}Participant Role{/ts}</dt><dd>{$role}&nbsp;</dd>
        {if $history neq 1}
            <dt>{ts}Registration Date{/ts}</dt><dd>{$register_date|truncate:10:''|crmDate}&nbsp;</dd>
        {else}
            <dt>{ts}Modified Date{/ts}</dt><dd>{$modified_date|truncate:10:''|crmDate}&nbsp;</dd>   
        {/if}
        <dt>{ts}Status{/ts}</dt><dd>{$status}&nbsp;</dd>
        {if $source}
            <dt>{ts}Event Source{/ts}</dt><dd>{$source}&nbsp;</dd>
        {/if}
        {if $event_level}
            {if $line_items}
                {assign var="total_price" value=0}
        <dt>{ts}Event Fees{/ts}</dt>
        <dd>
            <table>
                <tr>
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
                    <td>{$line.unit_price}</td>
                    <td>{$line.line_total}</td>
                </tr>
                {/foreach}
                <tr>
                    <td colspan="3"><strong>{ts}Total{/ts}</strong>:</td>
                    <td><strong>{$total_price|string_format:"$%.2f"}</strong></td>
                </tr>
            </table>
        </dd>
            {else}
        <dt>{ts}Event Level{/ts}</dt><dd>{$event_level}&nbsp;</dd>
            {/if}
        {/if}
        {if $history neq 1}
	    {foreach from=$note item="rec"}
		    {if $rec }
			<dt>{ts}Note:{/ts}</dt><dd>{$rec}</dd>	
	   	    {/if}
        {/foreach}
         
            {include file="CRM/Contact/Page/View/InlineCustomData.tpl" mainEditForm=1}  
        {/if}
        <dl>
           <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
    </dl>

</fieldset>  
</div>
