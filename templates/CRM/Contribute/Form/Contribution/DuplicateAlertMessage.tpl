{if $returnContent eq 'subject'}
    {ts}Possible Duplicate Contact Record{/ts}
{else if $returnContent eq 'textMessage'}
The contribution listed below was submitted on behalf of an organization, and the organization information matches existing records in your database. Please review the following record:

Contact Record: {$dupeContactUrl}

If you think this may be a duplicate contact which should be merged with an existing record - use the following link to identify the potential duplicates and merge them if appropriate:

Find Duplicates: {$dupeFindUrl}

{if $receiptMessage}
###########################################################
{ts}Copy of Contribution Receipt{/ts}

###########################################################
{$receiptMessage}
{/if}

{/if}