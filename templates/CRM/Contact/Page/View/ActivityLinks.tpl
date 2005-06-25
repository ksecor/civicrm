{* Links for scheduling/logging meetings and calls and Sending Email *}

{if $contact_id}
{assign var = "contactId" value= $contact_id }
{/if}

<div class= "data-group">
   <img src="{$config->resourceBase}i/EnvelopeIn.gif" alt="{ts}send email{/ts}">&nbsp;
   <a href="{crmURL p='civicrm/contact/email' q="cid=`$contactId`&reset=1"}">{ts}Send an Email{/ts}</a>&nbsp;&nbsp;
   <img src="{$config->resourceBase}i/meeting.gif" alt="{ts}meeting{/ts}">&nbsp;
   <a href="{crmURL p='civicrm/contact/view/meeting' q="action=add&pid=0&id=0&log=0"}">{ts}Schedule a Meeting{/ts}</a>&nbsp;&nbsp;
   <img src="{$config->resourceBase}i/tel.gif" alt="{ts}call{/ts}">&nbsp;
   <a href="{crmURL p='civicrm/contact/view/call' q="action=add&pid=0&id=0&log=0"}">{ts}Schedule a Call{/ts}</a>&nbsp;&nbsp;
   <img src="{$config->resourceBase}i/meeting.gif" alt="{ts}meeting{/ts}">&nbsp;
   <a href="{crmURL p='civicrm/contact/view/meeting' q="action=add&&pid=0&id=0&log=1"}">{ts}Log a Meeting{/ts}</a>&nbsp;&nbsp;
   <img src="{$config->resourceBase}i/tel.gif" alt="{ts}call{/ts}">&nbsp;
   <a href="{crmURL p='civicrm/contact/view/call' q="action=add&&pid=0&id=0&log=1"}">{ts}Log a Call{/ts}</a>
</div>
