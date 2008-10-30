{if $returnContent eq 'subject'}
    {strip}{ts}Activity Information{/ts} - {$activityTypeName}{/strip}
{else if $returnContent eq 'textMessage'}
{ts}Hello {$contact.name}{/ts}<br />{ts}Being a {$contact.role} related to case {$caseSubject}, you receive this copy of activity.{/ts}<br />
===========================================================
{ts}Activity Information{/ts}

===========================================================
{ts}EditURL{/ts}:{$activity.editURL}

{foreach from=$activity.fields item=field}
{$field.label}{if $field.category}({$field.category}){/if}: {$field.value}

{/foreach}

{foreach from=$activity.customGroups key=customGroupName item=customGroup}
==========================================================
{$customGroupName}
==========================================================
{foreach from=$customGroup item=field}
{$field.label}: {$field.value}
{/foreach}

{/foreach}
{/if}
