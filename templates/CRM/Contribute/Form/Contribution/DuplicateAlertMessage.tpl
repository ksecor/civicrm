{if $returnContent eq 'subject'}{ts}CiviContribute Alert: Possible Duplicate Contact Record{/ts}{else if $returnContent eq 'textMessage'}
A contribution / membership signup was made on behalf of the organization listed below.
The information provided matched multiple existing database records based on the configured
Duplicate Matching Rules for your site.

Organization Name : {$onBehalfName} 
Organization Email: {$onBehalfEmail} 
Organization Contact Id: {$onBehalfID} 

If you think this may be a duplicate contact which should be merged with an existing record -
Go to "CiviCRM >> Administer CiviCRM >> Find and Merge Duplicate Contacts". Use the strict
rule for Organizations to find the potential duplicates and merge them if appropriate. 

{if $receiptMessage}
###########################################################
{ts}Copy of Contribution Receipt{/ts}

###########################################################
{$receiptMessage}

{/if}
{/if}