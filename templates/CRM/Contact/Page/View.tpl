{if $mode eq 0}
{include file="CRM/Contact/Page/View/Contact.tpl"}
{elseif $mode eq 1}
{include file="CRM/Contact/Page/View/Note.tpl"}
{elseif $mode eq 2}
{include file="CRM/Contact/Page/View/Group.tpl"}
{elseif $mode eq 4}
{include file="CRM/Contact/Page/View/Relationship.tpl"}
{elseif $mode eq 8}
{include file="CRM/Contact/Page/View/Tags.tpl"}
{/if}

