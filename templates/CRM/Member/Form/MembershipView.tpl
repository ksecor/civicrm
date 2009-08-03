<div class="form-item"> 
<fieldset>
      <legend>{ts}View Membership{/ts}</legend>
    <table class="view-layout">
        <tr><td class="label">{ts}Member{/ts}</td><td class="bold">{$displayName}&nbsp;</td></tr>
        {if $owner_display_name}
            <tr><td class="label">{ts}By Relationship{/ts}</td><td>{$relationship}&nbsp;&nbsp;{$owner_display_name}&nbsp;</td></tr>
        {/if}
        <tr><td class="label">{ts}Membership Type{/ts}</td><td>{$membership_type}&nbsp;</td></tr>
        <tr><td class="label">{ts}Status{/ts}</td><td>{$status}&nbsp;</td></tr>
        <tr><td class="label">{ts}Source{/ts}</td><td>{$source}&nbsp;</td></tr>
        <tr><td class="label">{ts}Join date{/ts}</td><td>{$join_date|crmDate}&nbsp;</td></tr>
        <tr><td class="label">{ts}Start date{/ts}</td><td>{$start_date|crmDate}&nbsp;</td></tr>
        <tr><td class="label">{ts}End date{/ts}</td><td>{$end_date|crmDate}&nbsp;</td></tr>
        <tr><td class="label">{ts}Reminder date{/ts}</td><td>{$reminder_date|crmDate}&nbsp;</td></tr>
    </table>
        {include file="CRM/Custom/Page/CustomDataView.tpl"}
    <table class="form-layout">
       <tr>   
         <td>&nbsp;</td>
            <td>
                {$form.buttons.html}
                {if call_user_func(array('CRM_Core_Permission','check'), 'edit memberships')}
                    &nbsp;|&nbsp;<a href="{crmURL p='civicrm/contact/view/membership' q="reset=1&id=$id&cid=$contact_id&action=update&context=membership"}" accesskey="e">Edit</a>
                {/if}
                {if call_user_func(array('CRM_Core_Permission','check'), 'delete in CiviMember')}
                    &nbsp;|&nbsp;<a href="{crmURL p='civicrm/contact/view/membership' q="reset=1&id=$id&cid=$contact_id&action=delete&context=membership"}">Delete</a>
                {/if}
            </td>
        </tr>
    </table>
	{if $accessContribution and $rows.0.contribution_id}
	    {include file="CRM/Contribute/Form/Selector.tpl" context="Search"}	
	{/if}
</fieldset>  
</div>  
 
