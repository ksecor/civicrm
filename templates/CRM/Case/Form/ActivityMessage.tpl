{ts}Hello {$contact.name}{/ts}<br />{ts}Being a {$contact.role} related to case {$caseSub}; this is a copy of activity.{/ts}<br />
===========================================================
{ts}Activity Information{/ts}

===========================================================
{ts}Activity Type{/ts}:{$activityTypeName}

{ts}Subject{/ts}:{$activityTypeName}
{ts}Medium{/ts}:{$activity.medium}
{ts}Activity Date{/ts}:{$activity.activity_date_time|crmDate}
{ts}Due Date{/ts}:{$activity.due_date_time|crmDate}
{ts}Activity Status{/ts}:{$activity.status}

{if $customGroup}
{foreach from=$customGroup item=value key=customName} 
==========================================================
{$customName}
==========================================================
{foreach from=$value item=v key=n}
 {$n} : {$v}
{/foreach}
{/foreach}
{/if}




