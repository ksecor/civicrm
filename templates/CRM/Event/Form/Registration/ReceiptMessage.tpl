{if $action eq 1024}{include file="CRM/Event/Form/Registration/ReceiptPreviewHeader.tpl"}
{/if}
{if $eventPage.confirm_email_text}{$eventPage.confirm_email_text}
{/if}
{ts}Please print this confirmation for your records.{/ts}


===========================================================
{ts}Event Information and Location{/ts}

===========================================================
{$event.event_title}
{$event.event_start_date|crmDate}{if $event.event_end_date}-{if $event.event_end_date|date_format:"%Y%m%d" == $event.event_start_date|date_format:"%Y%m%d"}{$event.event_end_date|date_format:"%I:%M %p"}{else}{$event.event_end_date|crmDate}{/if}{/if}
{if $isShowLocation}
{if $location.1.name}

{$location.1.name}
{/if}
{$location.1.address.display}
{/if}{*End of isShowLocation condition*}

{if $location.1.phone.1.phone || $location.1.email.1.email}

{ts}Event Contacts:{/ts}
{foreach from=$location.1.phone item=phone}
{if $phone.phone}{if $phone.phone_type}

  {$phone.phone_type_display}:{/if} {$phone.phone}{/if}
{/foreach}
{foreach from=$location.1.email item=eventEmail}
{if $eventEmail.email}

  {ts}Email:{/ts} {$eventEmail.email}{/if}{/foreach}
{/if}


===========================================================
{ts}Registered Email{/ts}

===========================================================
{$email}
{if $event.is_monetary} {* This section for Paid events only.*}

===========================================================
{$event.fee_label}
===========================================================
{if $lineItem}
{capture assign="ts_item}{ts}Item{/ts}{/capture}
{capture assign="ts_qty}{ts}Qty{/ts}{/capture}
{capture assign="ts_each}{ts}Each{/ts}{/capture}
{capture assign="ts_total}{ts}Total{/ts}{/capture}
{$ts_item|string_format:"%-30s"} {$ts_qty|string_format:"%5s"} {$ts_each|string_format:"%10s"} {$ts_total|string_format:"%10s"}
----------------------------------------------------------
{foreach from=$lineItem item=line}
{$line.label|truncate:30:"..."|string_format:"%-30s"} {$line.qty|string_format:"%5s"} {$line.unit_price|crmMoney|string_format:"%10s"} {$line.line_total|crmMoney|string_format:"%10s"}
{/foreach}

{/if}
{ts}Total Amount{/ts}     : {$amount|crmMoney} {if $amount_level && !$line_item} - {$amount_level} {/if}

{ts}Transaction Date{/ts} : {$receive_date|crmDate}
{ts}Transaction #{/ts}    : {$trxn_id}
{if $contributeMode ne 'notify'}
===========================================================
{ts}Billing Name and Address{/ts}

===========================================================
{$name}
{$address}
{/if}

{if $contributeMode eq 'direct'}
===========================================================
{ts}Credit or Debit Card Information{/ts}

===========================================================
{$credit_card_type}
{$credit_card_number}
{ts}Expires{/ts}: {$credit_card_exp_date|truncate:7:''|crmDate}
{/if}
{/if} {* End of conditional section for Paid events *}

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
