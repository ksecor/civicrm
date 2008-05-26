{if $returnContent eq 'subject'}
    {ts}Duplicate Matches Found{/ts}

{else if $returnContent eq 'textMessage'}
{ts 1=$dupeID}
There is a possible duplicate contact situation for contribution / membership signup.

One recently observed for a new organization contact with contact-id=%1.
{/ts}
{/if}