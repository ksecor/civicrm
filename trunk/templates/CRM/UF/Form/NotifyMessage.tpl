{if $grouptitle} 

Submitted For: {$displayName}
Date: {$currentDate}
Contact Summary: {$contactLink} 

===========================================================
{ts}{$grouptitle} {/ts}

===========================================================
{foreach from=$values item=value key=valueName}
 {$valueName} : {$value}
{/foreach}

{/if}

