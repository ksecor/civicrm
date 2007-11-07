{if $action eq 1024}{include file="CRM/Contribute/Form/Contribution/ReceiptPreviewHeader.tpl"}
{/if}

{ts}Thanks for your support.{/ts}


{ts}Please print this receipt for your records.{/ts}


===========================================================
{ts}Contribution Information{/ts}

===========================================================
{ts}Contribution Type{/ts}: {$formValues.contributionType_name}
{ts}Total Amount{/ts}: {$formValues.total_amount|crmMoney}
{if $receive_date}
{ts}Received Date{/ts}: {$receive_date|truncate:10:''|crmDate}
{/if}
{if $formValues.paidBy}
{ts}Paid By{/ts}: {$formValues.paidBy}
{/if}
{if $formValues.trxn_id}
{ts}Transaction ID{/ts}: {$formValues.trxn_id}
{/if}
{if $showCustom}

===========================================================
{ts}Additional Information{/ts}

===========================================================
{foreach from=$customField item=value key=name}
 {$name}: {$value}
{/foreach}
{/if}
{if $formValues.honor_firstname }

===========================================================
{ts}In Honor Of{/ts}

===========================================================
{$formValues.honor_prefix} {$formValues.honor_firstname} {$formValues.honor_lastname}
{if $formValues.honor_email}
{ts}Honoree Email{/ts}: {$formValues.honor_email}
{/if}
{/if}

{if $formValues.product_name }
===========================================================
{ts}Premium Information{/ts}

===========================================================
{$formValues.product_name}
{ts}Sent{/ts}: {$fulfilled_date|crmDate}

{/if}

{if $formValues.receipt_text }
===========================================================
{ts}Receipt Text{/ts}

===========================================================
{$formValues.receipt_text}
{/if}