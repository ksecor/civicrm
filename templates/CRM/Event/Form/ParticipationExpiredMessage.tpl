{ts 1=$contact.display_name}Dear %1{/ts},

{ts}Your Event Registration has been Expired.{/ts}


===========================================================
{ts}Event Information and Location{/ts}

===========================================================
{$event.event_title}
{$event.event_start_date|crmDate}{if $event.event_end_date}-{if $event.event_end_date|date_format:"%Y%m%d" == $event.event_start_date|date_format:"%Y%m%d"}{$event.event_end_date|date_format:"%I:%M %p"}{else}{$event.event_end_date|crmDate}{/if}{/if}

{ts}Participant Role{/ts} : {$participant.role}

{if $isShowLocation}
{if $event.location.1.name}

{$event.location.1.name}
{/if}
{if $event.location.1.address.street_address}{$event.location.1.address.street_address}
{/if}
{if $event.location.1.address.supplemental_address_1}{$event.location.1.address.supplemental_address_1}
{/if}
{if $event.location.1.address.supplemental_address_2}{$event.location.1.address.supplemental_address_2}
{/if}
{if $event.location.1.address.city}{$event.location.1.address.city} {$event.location.1.address.postal_code}{if $event.location.1.address.postal_code_suffix} - {$event.location.1.address.postal_code_suffix}{/if}
{/if}

{/if}{*End of isShowLocation condition*}

{if $event.location.1.phone.1.phone || $event.location.1.email.1.email}

{ts}Event Contacts:{/ts}
{foreach from=$event.location.1.phone item=phone}
{if $phone.phone}

{if $phone.phone_type}{$phone.phone_type_display}{else}{ts}Phone{/ts}{/if}: {$phone.phone}{/if}
{/foreach}
{foreach from=$event.location.1.email item=eventEmail}
{if $eventEmail.email}

{ts}Email{/ts}: {$eventEmail.email}{/if}{/foreach}
{/if}

{capture assign=icalFeed}{crmURL p='civicrm/event/ical' q="reset=1&id=`$event.id`" h=0 a=1}{/capture}
{ts}Download iCalendar File:{/ts} {$icalFeed} 
{if $contact.email}

===========================================================
{ts}Registered Email{/ts}

===========================================================
{$contact.email}
{/if}
{if $event.is_monetary} {* This section for Paid events only.*}

===========================================================
{$event.fee_label}
===========================================================

{ts}Total Amount{/ts} : {$participant.fee_amount|crmMoney} {*here we might want to display Discount message *}

{if $register_date}
{ts}Registration Date{/ts}: {$participant.register_date|crmDate}
{/if}
{/if}

{ts 1=$domain.phone 2=$domain.email}Please contact us at %1 or send email to %2 if you have questions
or need to modify your event registration.{/ts}


{ts}Thank you for your participation.{/ts}

