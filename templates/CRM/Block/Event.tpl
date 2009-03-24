{* Block to display upcoming events. *}
{* You can add the following additional event elements to this tpl as needed: $ev.end_date, $ev.location, $ev.description, $ev.contact_email *}
{* Change truncate:80 to a larger or smaller value to show more or less of the summary. Remove it to show complete summary. *}
<div id="crm-event-block">
{foreach from=$event item=ev}
    <p>
    <a href="{$ev.url}">{$ev.title}</a><br />
    {$ev.start_date|truncate:10:""|crmDate}<br />
    {assign var=evSummary value=$ev.summary|truncate:80:""}
    <em>{$evSummary} (<a href="{$ev.url}">{ts}more{/ts}...</a>)</em>
    </p>
{/foreach}
</div>