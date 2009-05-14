{* Template for sending notfication email to recurring contribution. *}
{ts}Dear{/ts} {$displayName}

{if $recur_txnType eq 'subscr_signup'}

{ts}Thanks for Recurring Subscription sign-up.{/ts}


{ts 1=$recur_frequency_interval 2=$recur_frequency_unit 3=$recur_installments }This recurring contribution will be automatically processed every %1 %2(s) for a total %3 installment.{/ts}


{ts}Start Date{/ts} :  {$recur_start_date|crmDate}


{ts 1=$receipt_from_name 2=$receipt_from_email}You have pledged to make this recurring donation. You will be charged periodically (per frequency listed above), and you will receive an email receipt from %1 following each charge. These recurring donations will continue until you explicitly cancel the donation. You may change or cancel your recurring donation at anytime by logged in your account. If you have questions about recurring donations please contact us at %2.{/ts}

{else if $recur_txnType eq 'subscr_eot'}

{ts}Recurring Contribution Subscription's end-of-term{/ts}


{ts 1=$recur_installments}You have been successfully completed %1 recurring contribution.{/ts}


==================================================
{ts 1=$recur_installments}Interval of Subscription for %1 installments{/ts}

==================================================
{ts}Start Date{/ts} : {$recur_start_date|crmDate} 

{ts}End Date{/ts} : {$recur_end_date|crmDate}

{/if}