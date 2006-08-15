{if $action eq 1024}{include file="CRM/Contribute/Form/Contribution/ReceiptPreviewHeader.tpl"}
{/if}
{$receipt_text}

{ts}Please print this receipt for your records.{/ts}

{if $membership_assign}
===========================================================
{ts}Membership Information{/ts}

===========================================================
Membershiptype : {$membership_name}

Membership Start Date : {$mem_start_date}

Membership End Date   : {$mem_end_date}

{/if}
===========================================================
{ts}Contribution Information{/ts}

===========================================================
{if $membership_amount } 
{ts}Contribution Amount{/ts}:{$amount|crmMoney}
{ts}{$membership_name} Membership:{$membership_amount|crmMoney}{/ts}
-------------------------------------------
{ts}Total:{/ts}{$amount+$membership_amount|crmMoney}
{else}
{ts}Amount{/ts}: {$amount|crmMoney}
{/if}
{ts}Date{/ts}: {$receive_date|crmDate}
{ts}Transaction #{/ts}: {$trxn_id}
{if $membership_trx_id}
{ts}Membership Transaction #{/ts}:{$membership_trx_id}
{/if}
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
{ts}Premium Information{/ts}

===========================================================
{$product_name}
{if $option}
Option        : {$option}
{/if}
{if $sku}
SKU           : {$sku}
{/if}
{if $start_date}
Start Date    : {$start_date|crmDate}
{/if}
{if $end_date  }
End Date      : {$end_date|crmDate}
{/if}
{if $contact_email OR $contact_phone}

{ts}For information about this premium, contact:{/ts}

{if $contact_email}
  {$contact_email}
{/if}
{if $contact_phone}
  {$contact_phone}
{/if}
{/if}
{if $is_deductible AND $price}

{ts 1=$price|crmMoney}The value of this premium is %1. This may affect the amount of the
tax deduction you can claim. Consult your tax advisor for more information.{/ts}{/if}
{/if}
