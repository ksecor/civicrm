{if $returnContent eq 'subject'}
    {ts}Possible Duplicate Contact Record{/ts}
{else if $returnContent eq 'textMessage'}
Listed below is the organization information on behalf of whom a contribution / membership signup was done and was found to be matching with some of your existing records in your database.

Organization Name : {$onBehalfName} 
Organization Email: {$onBehalfEmail} 
Organization Contact Id: {$onBehalfID} 

If you think this may be a duplicate contact which should be merged with an existing record - Go to "CiviCRM >> Administer CiviCRM >> Find and Merge Duplicate Contacts" and use strict rule for Organization to find the potential duplicates and merge them if appropriate. 

{if $receiptMessage}
###########################################################
{ts}Copy of Contribution Receipt{/ts}

###########################################################
{$receiptMessage}

{/if}
{/if}