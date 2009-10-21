{* Displays current and upcoming public Events Listing as an HTML page. *}
<table>
<tr class="columnheader">
<th>{ts}Event{/ts}</th>
<th>&nbsp;</th>
<th>{ts}When{/ts}</th>
<th>{ts}Location{/ts}</th>
<th>{ts}Category{/ts}</th>
<th>{ts}Email{/ts}</th>
</tr>
{foreach from=$events key=uid item=event}
<tr class="{cycle values="odd-row,even-row"} {$row.class}">
    <td><a href="{crmURL p='civicrm/event/info' q="reset=1&id=`$event.event_id`"}" title="{ts}read more{/ts}"><strong>{$event.title}</strong></a></td>
    <td>{if $event.summary}{$event.summary} (<a href="{crmURL p='civicrm/event/info' q="reset=1&id=`$event.event_id`"}" title="{ts}details...{/ts}">{ts}read more{/ts}...</a>){else}&nbsp;{/if}</td>
    <td class="nowrap">
        {if $event.start_date}{$event.start_date|crmDate}{if $event.end_date}<br /><em>{ts}through{/ts}</em><br />{strip}
            {* Only show end time if end date = start date *}
            {if $event.end_date|date_format:"%Y%m%d" == $event.start_date|date_format:"%Y%m%d"}
                {$event.end_date|date_format:"%I:%M %p"}
            {else}
                {$event.end_date|crmDate}
            {/if}{/strip}{/if}
        {else}{ts}(not available){/ts}{/if}
    </td>
    <td>{if $event.is_show_location EQ 1 AND $event.location}{$event.location}{else}{ts}(not available){/ts}{/if}</td>
    <td>{if $event.event_type}{$event.event_type}{else}&nbsp;{/if}</td>
    <td>{if $event.contact_email}<a href="mailto:{$event.contact_email}">{$event.contact_email}</a>{else}&nbsp;{/if}</td>
</tr>
{/foreach}
</table>
