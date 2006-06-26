<fieldset>
<legend>
    {ts}Mailing Settings{/ts}
</legend>
{strip}
<table class="form-layout">
<tr><td class="label">{ts}Mailing Name{/ts}</td><td>{$report.mailing.name}</td></tr>
<tr><td class="label">{ts}Subject{/ts}</td><td>{$report.mailing.subject}</td></tr>
<tr><td class="label">{ts}From{/ts}</td><td>{$report.mailing.from_name} &lt;{$report.mailing.from_email}&gt;</td></tr>
<tr><td class="label">{ts}Reply-to email{/ts}</td><td>&lt;{$report.mailing.replyto_email}&gt;</td></tr>

<tr><td class="label">{ts}Forward replies{/ts}</td><td>{if $report.mailing.forward_replies}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>
<tr><td class="label">{ts}Auto-respond to replies{/ts}</td><td>{if $report.mailing.auto_responder}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>

<tr><td class="label">{ts}Open tracking{/ts}</td><td>{if $report.mailing.open_tracking}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>
<tr><td class="label">{ts}URL Click-through tracking{/ts}</td><td>{if $report.mailing.url_tracking}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>
</table>
{/strip}
</fieldset>

<fieldset>
<legend>{ts}Recipients{/ts}</legend>
{if $report.group.include|@count}
<span class="label">{ts}Included{/ts}</span>
{strip}
<table>
{foreach from=$report.group.include item=group}
<tr class="{cycle values="odd-row,even-row"}">
<td>
{if $group.mailing}
{ts}Recipients of <a href="{$group.link}">{$group.name}</a>{/ts}
{else}
{ts}Members of <a href="{$group.link}">{$group.name}</a>{/ts}
{/if}
</td>
</tr>
{/foreach}
</table>
{/strip}
{/if}

{if $report.group.exclude|@count}
<span class="label">{ts}Excluded{/ts}</span>
{strip}
<table>
{foreach from=$report.group.exclude item=group}
<tr class="{cycle values="odd-row,even-row"}">
<td>
{if $group.mailing}
{ts}Recipients of <a href="{$group.link}">{$group.name}</a>{/ts}
{else}
{ts}Members of <a href="{$group.link}">{$group.name}</a>{/ts}
{/if}
</td>
</tr>
{/foreach}
</table>
{/strip}
{/if}
</fieldset>


{if $report.jobs.0.start_date}
<fieldset>
<legend>{ts}Delivery Summary{/ts}</legend>

{if $report.jobs|@count > 1}
{strip}
<table>
<tr>
<th>{ts}Status{/ts}</th>
<th>{ts}Scheduled Date{/ts}</th>
<th>{ts}Start Date{/ts}</th>
<th>{ts}End Date{/ts}</th>
<th>{ts}Queued{/ts}</th>
<th>{ts}Delivered{/ts}</th>
<th>{ts}Bounces{/ts}</th>
<th>{ts}Unsubscriptions{/ts}</th>
<th>{ts}Forwards{/ts}</th>
<th>{ts}Replies{/ts}</th>
{if $report.mailing.open_tracking}
<th>{ts}Opens{/ts}</th>
{/if}
{if $report.mailing.url_tracking}
<th>{ts}Click-throughs{/ts}</th>
{/if}
</tr>
{foreach from=$report.jobs item=job}
<tr class="{cycle value="odd-row,even-row"}">
<td>{$job.status}</td>
<td>{$job.scheduled_date|date_format}</td>
<td>{$job.start_date|date_format}</td>
<td>{$job.end_date|date_format}</td>
<td><a href="{$job.links.queue}">{$job.queue}</a></td>
<td><a href="{$job.links.delivered}">{$job.delivered}</a></td>
<td><a href="{$job.links.bounce}">{$job.bounce}</a></td>
<td><a href="{$job.links.unsubscribe}">{$job.unsubscribe}</a></td>
<td><a href="{$job.links.forward}">{$job.forward}</a></td>
<td><a href="{$job.links.reply}">{$job.reply}</a></td>
{if $report.mailing.open_tracking}
<td><a href="{$job.links.opened}">{$job.opened}</a></td>
{/if}
{if $report.mailing.url_tracking}
<td><a href="{$job.links.clicks}">{$job.url}</a></td>
{/if}
</tr>
{/foreach}
<tr>
<th class="label" colspan=4>{ts}Totals{/ts}</th>
<th><a href="{$report.event_totals.links.queue}">{$report.event_totals.queue}</a></th>
<th><a href="{$report.event_totals.links.delivered}">{$report.event_totals.delivered}</a></th>
<th><a href="{$report.event_totals.links.bounce}">{$report.event_totals.bounce}</a></th>
<th><a href="{$report.event_totals.links.unsubscribe}">{$report.event_totals.unsubscribe}</a></th>
<th><a href="{$report.event_totals.links.forward}">{$report.event_totals.forward}</a></th>
<th><a href="{$report.event_totals.links.reply}">{$report.event_totals.reply}</a></th>
{if $report.mailing.open_tracking}
<th><a href="{$report.event_totals.links.opened}">{$report.event_totals.opened}</a></th>
{/if}
{if $report.mailing.url_tracking}
<th><a href="{$report.event_totals.links.clicks}">{$report.event_totals.url}</a></th>
{/if}
</tr>
<tr>
<th colspan=5>{ts}Percentages{/ts}</th>
<th>{$report.event_totals.delivered_rate|string_format:"%0.2f"}%</th>
<th>{$report.event_totals.bounce_rate|string_format:"%0.2f"}%</th>
<th>{$report.event_totals.unsubscribe_rate|string_format:"%0.2f"}%</th>
</tr>
</table>
{/strip}
{else}
{strip}
<table class="form-layout">
<tr><td class="label">{ts}Scheduled Date{/ts}</td><td>{$report.jobs.0.scheduled_date}</td></tr>
<tr><td class="label">{ts}Start Date{/ts}</td><td>{$report.jobs.0.start_date}</td></tr>
<tr><td class="label">{ts}End Date{/ts}</td><td>{$report.jobs.0.end_date}</td></tr>
<tr><td class="label">{ts}Status{/ts}</td><td>{$report.jobs.0.status}</td></tr>
<tr><td class="label"><a href="{$report.event_totals.links.queue}">{ts}Intended Recipients{/ts}</a></td><td>{$report.jobs.0.queue}</td></tr>
<tr><td class="label"><a href="{$report.event_totals.links.delivered}">{ts}Succesful Deliveries{/ts}</a></td><td>{$report.jobs.0.delivered} ({$report.jobs.0.delivered_rate|string_format:"%0.2f"}%)</td></tr>
<tr><td class="label"><a href="{$report.event_totals.links.bounce}">{ts}Bounces{/ts}</a></td><td>{$report.jobs.0.bounce} ({$report.jobs.0.bounce_rate|string_format:"%0.2f"}%)</td></tr>
<tr><td class="label"><a href="{$report.event_totals.links.unsubscribe}">{ts}Unsubscriptions{/ts}</a></td><td>{$report.jobs.0.unsubscribe} ({$report.jobs.0.unsubscribe_rate|string_format:"%0.2f"}%)</td></tr>
<tr><td class="label"><a href="{$report.event_totals.links.forward}">{ts}Forwards{/ts}</a></td><td>{$report.jobs.0.forward}</td></tr>
<tr><td class="label"><a href="{$report.event_totals.links.reply}">{ts}Replies{/ts}</a></td><td>{$report.jobs.0.reply}</td></tr>
{if $report.mailing.open_tracking}
<tr><td class="label"><a href="{$report.event_totals.links.opened}">{ts}Tracked Opens{/ts}</a></td><td>{$report.jobs.0.opened}</td></tr>
{/if}
{if $report.mailing.url_tracking}
<tr><td class="label"><a href="{$report.event_totals.links.clicks}">{ts}Click-throughs{/ts}</a></td><td>{$report.jobs.0.url}</td></tr>
{/if}
</table>
{/strip}
{/if}
<a href="{$report.retry}">{ts}Retry Mailing{/ts}</a>
</fieldset>

{/if}

{if $report.mailing.url_tracking && $report.click_through|@count > 0}
<fieldset>
<legend>{ts}Click-through Summary{/ts}</legend>
{strip}
<table>
<tr>
<th><a href="{$report.event_totals.links.clicks}">{ts}Clicks{/ts}</a></th>
<th><a href="{$report.event_totals.links.clicks_unique}">{ts}Unique Clicks{/ts}</a></th>
<th>{ts}Success Rate{/ts}</th>
<th>{ts}URL{/ts}</th></tr>
{foreach from=$report.click_through item=row}
<tr class="{cycle values="odd-row,even-row"}">
<td>{if $row.clicks > 0}<a href="{$row.link}">{$row.clicks}</a>{else}{$row.clicks}{/if}</td>
<td>{if $row.unique > 0}<a href="{$row.link_unique}">{$row.unique}</a>{else}{$row.unique}{/if}</td>
<td>{$row.rate|string_format:"%0.2f"}%</td>
<td><a href="{$row.url}">{$row.url}</a></td>
</tr>
{/foreach}
</table>
{/strip}
</fieldset>
{/if}


<fieldset>
<legend>{ts}Content / Components{/ts}</legend>
{strip}
<table class="form-layout">
{foreach from=$report.component item=component}
<tr><td class="label">{$component.type}</td><td>
<a href="{$component.link}">{$component.name}</a></td></tr>
{/foreach}
<tr><td class="label">{ts}Text Body{/ts}</td><td class="report">{$report.mailing.body_text|escape|nl2br}</td></tr>
<tr><td class="label">{ts}HTML Body{/ts}</td><td class="report">{$report.mailing.body_html|escape|nl2br}</td></tr>
</table>
{/strip}
</fieldset>




