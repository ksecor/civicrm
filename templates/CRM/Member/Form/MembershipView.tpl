<div class="form-item"> 
<fieldset>
      <legend>{ts}View Membership{/ts}</legend>
      <dl>  
        <dt class="font-size12pt">{ts}Member{/ts}</dt><dd class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</dd>
        {if $owner_display_name}
            <dt>{ts}By Relationship{/ts}</dt><dd>{$relationship}&nbsp;&nbsp;{$owner_display_name}&nbsp;</dd>
        {/if}
        <dt>{ts}Membership Type{/ts}</dt><dd>{$membership_type}&nbsp;</dd>
        <dt>{ts}Status{/ts}</dt><dd>{$status}&nbsp;</dd>
        <dt>{ts}Source{/ts}</dt><dd>{$source}&nbsp;</dd>
        <dt>{ts}Join date{/ts}</dt><dd>{$join_date|crmDate}&nbsp;</dd>
        <dt>{ts}Start date{/ts}</dt><dd>{$start_date|crmDate}&nbsp;</dd>
        <dt>{ts}End date{/ts}</dt><dd>{$end_date|crmDate}&nbsp;</dd>
        <dt>{ts}Reminder date{/ts}</dt><dd>{$reminder_date|crmDate}&nbsp;</dd>
        {include file="CRM/Custom/Page/CustomDataView.tpl"}
        <dt></dt>
            <dd>
                {$form.buttons.html}
                {if call_user_func(array('CRM_Core_Permission','check'), 'edit memberships')}
                    &nbsp;|&nbsp;<a href="{crmURL p='civicrm/contact/view/membership' q="reset=1&id=$id&cid=$contact_id&action=update&context=membership"}" accesskey="e">Edit</a>
                {/if}
                {if call_user_func(array('CRM_Core_Permission','check'), 'delete in CiviMember')}
                    &nbsp;|&nbsp;<a href="{crmURL p='civicrm/contact/view/membership' q="reset=1&id=$id&cid=$contact_id&action=delete&context=membership"}">Delete</a>
                {/if}
            </dd>
    </dl>
	{if $accessContribution and $rows.0.contribution_id}
	    {include file="CRM/Contribute/Form/Selector.tpl" context="Search"}	
	{/if}
</fieldset>  
</div>  
 
