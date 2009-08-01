{* View existing event registration record. *}
<div class="form-item">  
<fieldset>
        <legend>{ts}View Participant{/ts}</legend>
        <dl> 
        <dt class="font-size12pt">{ts}Name{/ts}</dt><dd class="font-size12pt"><strong><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$contact_id"}">{$displayName}</a></strong>&nbsp;</dd>
        <dt>{ts}Event{/ts}</dt><dd><a href="{crmURL p='civicrm/admin/event' q="action=update&reset=1&id=$event_id"}">{$event}</a>&nbsp;</dd>
        <dt>{ts}Participant Role{/ts}</dt><dd>{$role}&nbsp;</dd>
        <dt>{ts}Registration Date and Time{/ts}</dt><dd>{$register_date|crmDate}&nbsp;</dd>
        <dt>{ts}Status{/ts}</dt><dd>{$status}&nbsp;</dd>
        {if $source}
            <dt>{ts}Event Source{/ts}</dt><dd>{$source}&nbsp;</dd>
        {/if}
        {if $fee_level}
            {if $line_items}
                <dt>{ts}Event Fees{/ts}</dt>
                <dd>{include file="CRM/Event/Form/LineItems.tpl"}</dd> 
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
        {if $accessContribution and $rows.0.contribution_id}
            {include file="CRM/Contribute/Form/Selector.tpl" context="Search"} 
        {/if}
        <dl>
           <dt></dt>
                <dd>
                    {$form.buttons.html}
                    {if call_user_func(array('CRM_Core_Permission','check'), 'edit event participants')}
                        &nbsp;|&nbsp;<a href="{crmURL p='civicrm/contact/view/participant' q="reset=1&id=$id&cid=$contact_id&action=update&context=participant"}" accesskey="e">Edit</a>
                    {/if}
                    {if call_user_func(array('CRM_Core_Permission','check'), 'delete in CiviEvent')}
                        &nbsp;|&nbsp;<a href="{crmURL p='civicrm/contact/view/participant' q="reset=1&id=$id&cid=$contact_id&action=delete&context=participant"}">Delete</a>
                    {/if}
                </dd>
        </dl>
    </dl>
</fieldset>  
</div>
