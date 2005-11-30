{if $action eq 1024}{include file="CRM/Contribute/Form/Contribution/ReceiptPreviewHeader.tpl"}{/if}
{$receipt_text}
{ts}Please print this receipt for your records.{/ts}

===========================================================
{ts}Contribution Information{/ts}
===========================================================
        {ts}Amount{/ts}: ${$amount|string_format:"%01.2f"}
        {ts}Date{/ts}: {$receive_date}
        {ts}Transaction #{/ts}: {$trxn_id}

===========================================================
{ts}Billing Name and Address{/ts}
===========================================================
        {$name}
        {$street_address}
        {$city} {$state_province}, {$postal_code} &nbsp; {$country}

        {$email}

{if $contributeMode eq 'direct'}
===========================================================
{ts}Credit or Debit Card Information{/ts}
===========================================================
        {$credit_card_type}
        {$credit_card_number}
        {ts}Expires{/ts}: {$credit_card_exp_date}
{/if}


