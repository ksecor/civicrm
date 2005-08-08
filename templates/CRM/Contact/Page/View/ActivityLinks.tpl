{* Links for scheduling/logging meetings and calls and Sending Email *}

{if $contact_id}
{assign var = "contactId" value= $contact_id }
{/if}

<div class= "data-group">
   <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=3&cid=`$contactId`&reset=1"}"><img src="{$config->resourceBase}i/EnvelopeIn.gif" alt="{ts}Send Email{/ts}"></a>&nbsp;
   <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=3&cid=`$contactId`&reset=1"}">{ts}Send an Email{/ts}</a>&nbsp;&nbsp;
   <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=1&action=add&reset=1&cid=`$contactId`"}"><img src="{$config->resourceBase}i/meeting.gif" alt="{ts}Schedule Meeting{/ts}"></a>&nbsp;
   <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=1&action=add&reset=1&cid=`$contactId`"}">{ts}Schedule a Meeting{/ts}</a>&nbsp;&nbsp;
   <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=2&action=add&reset=1&cid=`$contactId`"}"><img src="{$config->resourceBase}i/tel.gif" alt="{ts}Schedule Call{/ts}"></a>&nbsp;
   <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=2&action=add&reset=1&cid=`$contactId`"}">{ts}Schedule a Call{/ts}</a>&nbsp;&nbsp;
   <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=1&action=add&reset=1&cid=`$contactId`&log=1"}"><img src="{$config->resourceBase}i/meeting.gif" alt="{ts}Log a Meeting{/ts}"></a>&nbsp;
   <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=1&action=add&reset=1&cid=`$contactId`&log=1"}">{ts}Log a Meeting{/ts}</a>&nbsp;&nbsp;
   <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=2&action=add&reset=1&cid=`$contactId`&log=1"}"><img src="{$config->resourceBase}i/tel.gif" alt="{ts}Log a Call{/ts}"></a>&nbsp;
   <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=2&action=add&reset=1&cid=`$contactId`&log=1"}">{ts}Log a Call{/ts}</a>&nbsp;&nbsp;
   {* Only display next link if there are activity_type entries for this domain. *}
   {if $showOtherActivityLink}
        &nbsp;&nbsp;
        <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=other&action=add&reset=1&cid=`$contactId`"}"><img src="{$config->resourceBase}i/custom_activity.gif" alt="{ts}Other Activities{/ts}"></a>&nbsp;
        <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=other&action=add&reset=1&cid=`$contactId`"}">{ts}Other Activities{/ts}</a>
   {/if}

</div>
