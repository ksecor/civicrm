{if $action eq 1024}{include file="CRM/Contribute/Form/Contribution/ReceiptPreviewHeader.tpl"}
{/if}
{if $receipt_text}
{$receipt_text}
{/if}
{if $is_pay_later}

===========================================================
{$pay_later_receipt}
===========================================================
{else}

{ts}Please print this receipt for your records.{/ts}
{/if}

{if $membership_assign}
===========================================================
{ts}Membership Information{/ts}

===========================================================
{ts}Membership Type{/ts}: {$membership_name}
{if $mem_start_date}{ts}Membership Start Date{/ts}: {$mem_start_date|crmDate}
{/if}
{if $mem_start_date}{ts}Membership End Date{/ts}: {$mem_end_date|crmDate}
{/if}

{/if}
{if $amount}
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
{elseif $membership_amount}
===========================================================
{ts}Membership Fee{/ts}

===========================================================
{ts 1=$membership_name}%1 Membership{/ts}: {$membership_amount|crmMoney}
{/if}
{if $receive_date}

{ts}Date{/ts}: {$receive_date|crmDate}
{/if}
{if $is_monetary and $trxn_id}
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
{if $pcpBlock}
===========================================================
{ts}Personal Campaign Page{/ts}

===========================================================
{ts}Display In Roll{/ts} : {if $pcp_display_in_roll}{ts}Yes{/ts}{else}{ts}No{/ts}{/if}

{if $pcp_roll_nickname}{ts}Nick Name{/ts} : {$pcp_roll_nickname}{/if}

{if $pcp_personal_note}{ts}Note{/ts} : {$pcp_personal_note}{/if}

{/if}
{if $onBehalfName}
===========================================================
{ts}On Behalf Of{/ts}

===========================================================
{$onBehalfName}
{$onBehalfAddress}

{$onBehalfEmail}

{/if}
{if $contributeMode ne 'notify' and $is_monetary}
{if $is_pay_later}
===========================================================
{ts}Registered Email{/ts}

===========================================================
{$email}
{elseif $amount GT 0 OR $membership_amount GT 0 }
===========================================================
{ts}Billing Name and Address{/ts}

===========================================================
{$billingName}
{$address}

{$email}
{/if} {* End ! is_pay_later condition. *}
{/if}
{if $contributeMode eq 'direct' AND !$is_pay_later AND ( $amount GT 0 OR $membership_amount GT 0 ) }

===========================================================
{ts}Credit Card Information{/ts}

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
{foreach from=$customPre item=customValue key=customName}
 {$customName} : {$customValue}
{/foreach}
{/if}


{if $customPost}
===========================================================
{ts}{$customPost_grouptitle}{/ts}

===========================================================
{foreach from=$customPost item=customValue key=customName}
 {$customName} : {$customValue}
{/foreach}
{/if}
