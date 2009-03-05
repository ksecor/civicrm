BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//CiviCRM//NONSGML CiviEvent iCal//EN
{foreach from=$events key=uid item=event}
BEGIN:VEVENT
UID:{$event.uid}
SUMMARY:{$event.title|crmICalText}
{if $event.description}
DESCRIPTION:{$event.description|crmICalText}
{/if}
{if $event.event_type}
CATEGORIES:{$event.event_type|crmICalText}
{/if}
DTSTAMP;VALUE=DATE-TIME:{$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'|crmICalDate}
{if $event.start_date}
DTSTART;VALUE=DATE-TIME:{$event.start_date|crmICalDate}
{/if}
{if $event.end_date}
DTEND;VALUE=DATE-TIME:{$event.end_date|crmICalDate}
{else}
DTEND;VALUE=DATE-TIME:{$event.start_date|crmICalDate}
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
