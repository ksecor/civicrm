{* View existing event registration record. *}
<div class="form-item">  
<fieldset>
        <legend>{ts}View Participant{/ts}</legend>
        <dl> 
	 <dt class="font-size12pt">{ts}Name{/ts}</dt><dd class="font-size12pt"><strong><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$contact_id"}">{$displayName}</a></strong>&nbsp;</dd>
	<dt>{ts}Event{/ts}</dt><dd><a href="{crmURL p='civicrm/admin/event' q="action=update&reset=1&id=$event_id"}">{$event}</a>&nbsp;</dd>
        <dt>{ts}Participant Role{/ts}</dt><dd>{$role}&nbsp;</dd>
        <dt>{ts}Registration Date and Time{/ts}</dt><dd>{$register_date|crmDate}&nbsp;</dd>
        <dt>{ts}Status{/ts}</dt><dd>{$status}&nbsp;
	{if $participant_status_id eq 5}{if $participant_is_pay_later}: {ts}Pay Later{/ts}{else}: {ts}Incomplete Transaction{/ts}{/if}{/if}</dd>
        {if $source}
            <dt>{ts}Event Source{/ts}</dt><dd>{$source}&nbsp;</dd>
        {/if}
        {if $fee_level}
	    {if $line_items}
	        {include file="CRM/Event/Form/LineItems.tpl"} 
	    {else}
                <dt>{ts}Event Level{/ts}</dt><dd>{$fee_level}&nbsp;{if $fee_amount}- {$fee_amount|crmMoney:$fee_currency}{/if}</dd>
            {/if}
        {/if}
        {foreach from=$note item="rec"}
	    {if $rec }
		<dt>{ts}Note:{/ts}</dt><dd>{$rec}</dd>
	    {/if}
        {/foreach}
         
        {include file="CRM/Custom/Page/CustomDataView.tpl"}  
        <dl>
           <dt></dt><dd>{$form.buttons.html} <a href="{crmURL p='civicrm/contact/view/participant' q="reset=1&id=$id&cid=$contact_id&action=update&context=participant"}" accesskey="e">Edit</a>&nbsp;|&nbsp;<a href="{crmURL p='civicrm/contact/view/participant' q="reset=1&id=$id&cid=$contact_id&action=delete&context=participant"}">Delete</a></dd>
        </dl>
    </dl>
	{if $accessContribution and $rows.0.contribution_id}
           {include file="CRM/Contribute/Form/Selector.tpl" context="Search"} 
        {/if}
</fieldset>  
</div>
