{if $action eq 1024}{include file="CRM/Contribute/Form/Contribution/ReceiptPreviewHeader.tpl"}
{/if}
{if $formValues.receipt_text_signup}
{$formValues.receipt_text_signup}
{elseif $formValues.receipt_text_renewal}
{$formValues.receipt_text_renewal}
{else}{ts}Thanks for your support.{/ts}{/if}

{ts}Please print this receipt for your records.{/ts}

===========================================================
{ts}Membership Information{/ts}

===========================================================
{ts}Membership Type{/ts}: {$membership_name}
{ts}Membership Start Date{/ts}: {$mem_start_date|crmDate}
{ts}Membership End Date{/ts}: {$mem_end_date|crmDate}

===========================================================
{ts}Membership Fee{/ts}

===========================================================
{ts}Amount{/ts}: {$formValues.total_amount|crmMoney}
{if $receive_date}
{ts}Received Date{/ts}: {$receive_date|truncate:10:''|crmDate}
{/if}
{if $formValues.paidBy}
{ts}Paid By{/ts}: {$formValues.paidBy}
{/if}

{if $customValues}
===========================================================
{ts}Membership Options{/ts}

===========================================================
{foreach from=$customValues item=value key=name}
 {$name} : {$value}
{/foreach}
{/if}