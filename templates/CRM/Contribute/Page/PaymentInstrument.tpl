{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Contribute/Form/PaymentInstrument.tpl"}
{else}
    <div id="help">
        <p>{ts}You may choose to record the Payment Instrument used for each Contribution. The common payment methods are installed by default and cannot be modified (e.g. Check, Cash, Credit Card...). If your site requires additional payment methods, you can add them here.{/ts}</p>
    </div>
{/if}

{if $rows}
<div id="payment_instrument_type">
<p></p>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Name{/ts}</th>
            <th>{ts}Description{/ts}</th>
            <th>{ts}Reserved?{/ts}</th>
            <th>{ts}Enabled?{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.name}</td>	
	        <td>{$row.description}</td>
	        <td>{if $row.is_reserved eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}" id="newPaymentInstrument">&raquo; {ts}New Payment Instrument{/ts}</a>
        </div>
        {/if}
    </div>
</div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/paymentInstrument' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no custom Payment Instruments entered. You can <a href='%1'>add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
