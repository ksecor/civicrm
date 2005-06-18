{if $mode eq 0}
{include file="CRM/Contact/Page/View/Contact.tpl"}
{elseif $mode eq 1}
{include file="CRM/Contact/Page/View/Note.tpl"}
{elseif $mode eq 2}
{include file="CRM/Contact/Page/View/GroupContact.tpl"}
{elseif $mode eq 4}
{include file="CRM/Contact/Page/View/Relationship.tpl"}
{elseif $mode eq 8}
{include file="CRM/Contact/Page/View/Tag.tpl"}
{elseif $mode eq 16}
{include file="CRM/Contact/Page/View/CustomData.tpl"}
{elseif $mode eq 32}
{include file="CRM/Contact/Page/View/Activity.tpl"}
{elseif $mode eq 64}
{include file="CRM/Contact/Page/View/Meeting.tpl"}
{elseif $mode eq 128}
{include file="CRM/Contact/Page/View/Call.tpl"}
{/if}



