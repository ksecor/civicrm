<div class= "data-group">
   <img src="{$config->resourceBase}i/EnvelopeIn.gif" alt="{ts}send email{/ts}">&nbsp;
   <a href="{crmURL p='civicrm/contact/email' q="cid=`$contactId`&reset=1"}">Send An Email</a>&nbsp;&nbsp;
   <img src="{$config->resourceBase}i/meeting.gif" alt="{ts}meeting{/ts}">&nbsp;
   <a href="{crmURL p='civicrm/contact/view/meeting' q="action=add"}">Schedule A Meeting</a>&nbsp;&nbsp;
   <img src="{$config->resourceBase}i/tel.gif" alt="{ts}call{/ts}">&nbsp;
   <a href="{crmURL p='civicrm/contact/view/call' q="action=add"}">Schedule A Call</a>&nbsp;&nbsp;
   <img src="{$config->resourceBase}i/meeting.gif" alt="{ts}meeting{/ts}">&nbsp;
   <a href="{crmURL p='civicrm/contact/view/meeting' q="action=add&log=true"}">Log A Meeting</a>&nbsp;&nbsp;
   <img src="{$config->resourceBase}i/tel.gif" alt="{ts}call{/ts}">&nbsp;
   <a href="{crmURL p='civicrm/contact/view/call' q="action=add&log=true"}">Log A Call</a>
</div>
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/History/Form/Activity.tpl"}	
{/if}

{include file="CRM/History/Selector/Activity.tpl"}
