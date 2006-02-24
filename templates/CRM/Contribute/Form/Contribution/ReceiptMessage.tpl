{if $action eq 1024}{include file="CRM/Contribute/Form/Contribution/ReceiptPreviewHeader.tpl"}{/if}
{$receipt_text}
{ts}Please print this receipt for your records.{/ts}


===========================================================
{ts}Contribution Information{/ts}

===========================================================
{ts}Amount{/ts}: {$amount|crmMoney}
{ts}Date{/ts}: {$receive_date|crmDate}
{ts}Transaction #{/ts}: {$trxn_id}

===========================================================
{ts}Billing Name and Address{/ts}

===========================================================
{$name}
{$address}

{$email}

{if $contributeMode eq 'direct'}
===========================================================
{ts}Credit or Debit Card Information{/ts}

===========================================================
{$credit_card_type}
{$credit_card_number}
{ts}Expires{/ts}: {$credit_card_exp_date|truncate:7:''|crmDate}
{/if}

{if $selectPremium }
===========================================================
{ts}Premium  Information{/ts}

===========================================================

Name          : {$product_name}
SKU           : {$sku}   
Price         : {$price|crmMoney}
Option        : {$option}
{if $start_date}
Start Date    : {$start_date|crmDate}
{/if}
{if $end_date  }
End Date      : {$end_date|crmDate}    
{/if}
{if $contact_email}
Contact Email : {$contact_email}
{/if}
{if $contact_phone}
Contact Phone : {$contact_phone}
{/if}
{/if}
