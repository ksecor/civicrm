<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
<channel>
<title>{ts}CiviEvent Public Calendar{/ts}</title>
<link>{$config->userFrameworkBaseURL}</link>
<description>{ts}Listing of current and upcoming public events.{/ts}</description>
<language>{$rssLang}</language>
<generator>CiviCRM</generator>
<docs>http://blogs.law.harvard.edu/tech/rss</docs>
{foreach from=$events key=uid item=event}
<item>
<title>{$event.title|escape:'htmlall'}</title>
<link>{crmURL p='civicrm/event/info' q="reset=1&id=`$event.event_id`"}</link>
<description>
{if $event.summary}{$event.summary|escape:'htmlall'}
{/if}
{if $event.description}{$event.description|escape:'htmlall'}
{/if}
{if $event.start_date}{ts}When{/ts}: {$event.start_date|crmDate}{if $event.end_date} {ts}through{/ts} {strip}
        {* Only show end time if end date = start date *}
        {if $event.end_date|date_format:"%Y%m%d" == $event.start_date|date_format:"%Y%m%d"}
            {$event.end_date|date_format:"%I:%M %p"}
        {else}
            {$event.end_date|crmDate}
        {/if}{/strip}
    {/if}
{/if}
{if $event.is_show_location EQ 1 && $event.location}{ts}Where{/ts}: {$event.location|escape:'htmlall'}
{/if}
</description>
{if $event.event_type}<category>{$event.event_type|escape:'htmlall'}</category>
{/if}
{if $event.contact_email}<author>{$event.contact_email}</author>
{/if}
<guid isPermaLink="false">{$event.uid}</guid>
</item>
{/foreach}
</channel>
</rss>