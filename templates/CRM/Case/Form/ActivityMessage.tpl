{if $returnContent eq 'subject'}{strip}{ts}Activity Information{/ts} - {$activityTypeName}{/strip}{else if $returnContent eq 'textMessage'}
===========================================================
{ts}Activity Summary{/ts} - {$activityTypeName}
===========================================================
{ts}Your Case Role{/ts} : {$contact.role}

{foreach from=$activity.fields item=field}
{$field.label}{if $field.category}({$field.category}){/if} : {$field.value}
{/foreach}

{foreach from=$activity.customGroups key=customGroupName item=customGroup}
==========================================================
{$customGroupName}
==========================================================
{foreach from=$customGroup item=field}
{$field.label} : {$field.value}
{/foreach}

{/foreach}
{/if}
