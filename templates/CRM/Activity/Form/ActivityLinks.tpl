{* Links for scheduling/logging meetings and calls and Sending Email *}
{if $cdType eq false }
{if $contact_id }
{assign var = "contactId" value= $contact_id }
{/if}
<div class='spacer'></div>
<div class= "section-hidden section-hidden-border">
{if $emailSetting and not $privacy.do_not_email and not $is_deceased}
   <a href="{crmURL p='civicrm/contact/view/activity' q="atype=3&action=add&reset=1&cid=`$contactId`"}"><img src="{$config->resourceBase}i/EnvelopeIn.gif" alt="{ts}Send Email{/ts}"/></a>&nbsp;
   <a href="{crmURL p='civicrm/contact/view/activity' q="atype=3&action=add&reset=1&cid=`$contactId`"}">{ts}Send an Email{/ts}</a>&nbsp;&nbsp;
{/if}
   <a href="{crmURL p='civicrm/contact/view/activity' q="atype=1&action=add&reset=1&cid=`$contactId`&context=activity"}"><img src="{$config->resourceBase}i/meeting.gif" alt="{ts}Meeting{/ts}"/></a>&nbsp;
   <a href="{crmURL p='civicrm/contact/view/activity' q="atype=1&action=add&reset=1&cid=`$contactId`&context=activity"}">{ts}Meeting{/ts}</a>&nbsp;&nbsp;
   <a href="{crmURL p='civicrm/contact/view/activity' q="atype=2&action=add&reset=1&cid=`$contactId`&context=activity"}"><img src="{$config->resourceBase}i/tel.gif" alt="{ts}Phone Call{/ts}"/></a>&nbsp;
   <a href="{crmURL p='civicrm/contact/view/activity' q="atype=2&action=add&reset=1&cid=`$contactId`&context=activity"}">{ts}Phone Call{/ts}</a>&nbsp;&nbsp;
   {* Only display next link if there are activity_type entries for this domain. *}
   {if $showOtherActivityLink}
        &nbsp;&nbsp;
        <img src="{$config->resourceBase}i/custom_activity.gif" alt="{ts}Other Activities{/ts}"/>&nbsp;{$form.other_activity.label}&nbsp;&nbsp;{$form.other_activity.html}
   {/if}

{* add hook links if any *}
{if $hookLinks}
   {foreach from=$hookLinks item=link}
{if $link.img}
      <a href="{$link.url}"><img src="{$link.img}" alt="{$link.title}" /></a>&nbsp;
{/if}
      <a href="{$link.url}">{$link.title}</a>&nbsp;&nbsp;
   {/foreach}
{/if}

</div>
{/if}