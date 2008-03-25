{if $action eq 1024}{include file="CRM/Contribute/Form/Contribution/ReceiptPreviewHeader.tpl"}
{/if}
{$receipt_text}

{ts}Please print this receipt for your records.{/ts}

{if $membership_assign}
===========================================================
{ts}Membership Information{/ts}

===========================================================
{ts}Membership Type{/ts}: {$membership_name}
{ts}Membership Start Date{/ts}: {$mem_start_date|crmDate}
{ts}Membership End Date{/ts}: {$mem_end_date|crmDate}

{/if}
===========================================================
{if !$membershipBlock AND $amount}{ts}Contribution Information{/ts}{else}{ts}Membership Fee{/ts}{/if}

===========================================================
{if $membership_amount } 
{ts 1=$membership_name}%1 Membership{/ts}: {$membership_amount|crmMoney} 
{if $amount}
{if ! $is_separate_payment }
{ts}Contribution Amount{/ts}: {$amount|crmMoney}
{else}
{ts}Additional Contribution{/ts}: {$amount|crmMoney}
{/if}
{/if}
-------------------------------------------
{ts}Total{/ts}: {$amount+$membership_amount|crmMoney}
{else}
{ts}Amount{/ts}: {$amount|crmMoney} {if $amount_level } - {$amount_level} {/if}
{/if}

{ts}Date{/ts}: {$receive_date|crmDate}
{if $is_monetary}
{ts}Transaction #{/ts}: {$trxn_id}
{/if}
{if $membership_trx_id}
{ts}Membership Transaction #{/ts}: {$membership_trx_id}

{/if}
{if $is_recur}
{ts}This is a recurring contribution. You can modify or cancel future contributions by logging in to your account at:{/ts}

{$cancelSubscriptionUrl}
{/if}

{if $honor_block_is_active }
===========================================================
{$honor_type}
===========================================================
{$honor_prefix} {$honor_first_name} {$honor_last_name}
{if $honor_email}
{ts}Honoree Email{/ts}: {$honor_email}
{/if}

{/if}
{if $contributeMode ne 'notify' and $is_monetary}
===========================================================
{ts}Billing Name and Address{/ts}

===========================================================
{$name}
{$address}

{$email}
{/if}

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
Option: {$option}
{/if}
{if $sku}
SKU   : {$sku}
{/if}
{if $start_date}
Start Date: {$start_date|crmDate}
{/if}
{if $end_date}
End Date: {$end_date|crmDate}
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

{ts 1=$price|crmMoney}The value of this premium is %1. This may affect the amount of the tax deduction you can claim. Consult your tax advisor for more information.{/ts}{/if}
{/if}

{if $customPre}
===========================================================
{ts}{$customPre_grouptitle} {/ts}

===========================================================
{foreach from=$customPre item=value key=name}
 {$name} : {$value}
{/foreach}
{/if}


{if $customPost}
===========================================================
{ts}{$customPost_grouptitle}{/ts}

===========================================================
{foreach from=$customPost item=value key=name}
 {$name} : {$value}
{/foreach}
{/if}
