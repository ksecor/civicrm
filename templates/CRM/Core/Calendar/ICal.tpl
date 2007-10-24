BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//CiviCRM//NONSGML CiviEvent iCal//EN
{foreach from=$events key=uid item=event}
BEGIN:VEVENT
UID:{$event.uid}
SUMMARY:{$event.summary|crmICalText}
{if $event.description}
DESCRIPTION:{$event.description|crmICalText}
{/if}
{if $event.event_type}
CATEGORIES:{$event.event_type|crmICalText}
{/if}
{if $event.start_date}
DTSTART:{$event.start_date|crmICalDate}
DTEND:{$event.end_date|crmICalDate}
{/if}
{if $event.is_show_location EQ 1 && $event.location}
LOCATION:{$event.location|crmICalText}
{/if}
{if $event.contact_email}
ORGANIZER:MAILTO:{$event.contact_email|crmICalText}
{/if}
{if $event.url}
URL:{$event.url}
{/if}
END:VEVENT
{/foreach}
END:VCALENDAR
