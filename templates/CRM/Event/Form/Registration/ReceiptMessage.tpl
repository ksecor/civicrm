{if $action eq 1024}{include file="CRM/Contribute/Form/Contribution/ReceiptPreviewHeader.tpl"}
{/if}
{$receipt_text}

{ts}Please print this receipt for your records.{/ts}

===========================================================
{ts}Contribution Information{/ts}

===========================================================
{ts}Amount{/ts}: {$amount|crmMoney} {if $amount_level } - {$amount_level} {/if}

{ts}Date{/ts}: {$receive_date|crmDate}
{*if $is_monetary*}
{ts}Transaction #{/ts}: {$trxn_id}
{*/if*}

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
