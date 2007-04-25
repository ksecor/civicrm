{foreach from=$events key=uid item=event}
<entry xmlns='http://www.w3.org/2005/Atom'
    xmlns:gd='http://schemas.google.com/g/2005'>
  <category scheme='http://schemas.google.com/g/2005#kind'
    term='http://schemas.google.com/g/2005#event'></category>
  <title type='text'>{$event.summary}</title>
{if $event.description}
  <content type='text'>{$event.description}</content>
{/if}
{if $display_name}
  <author>
    <name>{$display_name}</name>
    <email>{$email}</email>
  </author>
{/if}
  <gd:transparency
    value='http://schemas.google.com/g/2005#event.opaque'>
  </gd:transparency>
  <gd:eventStatus
    value='http://schemas.google.com/g/2005#event.confirmed'>
  </gd:eventStatus>
{if $event.location}
  <gd:where valueString='{$event.location}'></gd:where>
{/if}
{if $event.start_date}
  <gd:when startTime='{$event.start_date|crmICalDate:1}'
    endTime='{$event.end_date|crmICalDate:1}'></gd:when>
{/if}
</entry>
{/foreach}
