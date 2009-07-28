{* Links for scheduling/logging meetings and calls and Sending Email *}
{if $cdType eq false }
{if $contact_id }
{assign var = "contactId" value= $contact_id }
{/if}

{* Only display the activity drop-down if there are activity_type entries for this domain. *}
{if $showOtherActivityLink}{$form.other_activity.html}{/if}

{* add hook links if any *}
{if $hookLinks}
   {foreach from=$hookLinks item=link}
{if $link.img}
      <a href="{$link.url}"><img src="{$link.img}" alt="{$link.title}" /></a>&nbsp;
{/if}
      <a href="{$link.url}">{$link.title}</a>&nbsp;&nbsp;
   {/foreach}
{/if}

{/if}