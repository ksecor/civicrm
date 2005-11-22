
{$receipt_text}

Amount : ${$amount}
Trxn ID: {$trxn_id}
Name: {$name}
Billing Address: {$street_address}
City: {$city}
State: {$state_province}
Postal Code: {$postal_code}
Country: {$country}

{if $contributeMode eq 'direct'}
Credit Card Number: {$credit_card_number}
Credit Card Type: {$credit_card_type}
Credit Card Exp Date: {$credit_card_exp_date}
{/if}

