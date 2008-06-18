<table>
<tr>
<th>{ts}Title{/ts}</th>
<th>{ts}Description{/ts}</th>
<th>{ts}Start Date{/ts}</th>
<th>{ts}End Date{/ts}</th>
<th>{ts}Location{/ts}</th>
<th>{ts}Category{/ts}</th>
<th>{ts}Email{/ts}</th>
</tr>
{foreach from=$events key=uid item=event}
<tr>
<td><a href="{crmURL p='civicrm/event/info' q="reset=1&id=`$event.event_id`"}">{$event.summary}</a></title>
<td>{if $event.description}{$event.description}{else}&nbsp;{/if}</td>
<td>{if $event.start_date}{$event.start_date|crmDate}{else}&nbsp;{/if}</td>
<td>{if $event.end_date}{$event.end_date|crmDate}{else}&nbsp;{/if}</td>
<td>{if $event.is_show_location EQ 1 AND $event.location}{$event.location}{else}&nbsp;{/if}</td>
<td>{if $event.event_type}{$event.event_type}{else}&nbsp;{/if}</td>
<td>{if $event.contact_email}{$event.contact_email}{else}&nbsp;{/if}</td>
</tr>
{/foreach}
</table>
