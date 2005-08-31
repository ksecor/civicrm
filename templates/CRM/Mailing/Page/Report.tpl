<fieldset>
<legend>
    {ts}Mailing Settings{/ts}
</legend>
<table class="form-layout">
<tr><td class="label">{ts}Mailing Name{/ts}</td><td>{$report.mailing.name}</td></tr>
<tr><td class="label">{ts}Subject{/ts}</td><td>{$report.mailing.subject}</td></tr>
<tr><td class="label">{ts}From{/ts}</td><td>{$report.mailing.from_name} &lt;{$report.mailing.from_email}&gt;</td></tr>
<tr><td class="label">{ts}Reply-to email{/ts}</td><td>&lt;{$report.mailing.replyto_email}&gt;</td></tr>

<tr><td class="label">{ts}URL Click-through tracking{/ts}</td><td>{if $report.mailing.url_tracking}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>
<tr><td class="label">{ts}Forward replies{/ts}</td><td>{if $report.mailing.forward_replies}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>
<tr><td class="label">{ts}Auto-respond to replies{/ts}</td><td>{if $report.mailing.auto_responder}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>
<tr><td class="label">{ts}Open tracking{/ts}</td><td>{if $report.mailing.open_tracking}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>
</table>
</fieldset>

<fieldset>
<legend>{ts}Content / Components{/ts}</legend>
<table class="form-layout">
{foreach from=$report.component item=component}
<tr><td class="label">{$component.type}</td><td><a
href="{$component.link}">{$component.name}</a></td></tr>
{/foreach}
</table>
</fieldset>


<fieldset>
<legend>{ts}Recipients{/ts}</legend>
{if $report.group.include|@count}
<span class="label">{ts}Included{/ts}</span>
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
{/if}

{if $report.group.exclude|@count}
<span class="label">{ts}Excluded{/ts}</span>
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
{/if}
</fieldset>

<fieldset>
<legend>{ts}Delivery Statistics{/ts}</legend>

{if $report.jobs|@count > 1}
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
<td>{$job.queue}</td>
<td>{$job.delivered}</td>
<td>{$job.bounce}</td>
<td>{$job.unsubscribe}</td>
<td>{$job.reply}</td>
{if $report.mailing.open_tracking}
<td>{$job.opened}</td>
{/if}
{if $report.mailing.url_tracking}
<td>{$job.url}</td>
{/if}
</tr>
{/foreach}
<tr>
<th class="label" colspan=4>{ts}Totals{/ts}</th>
<th>{$report.event_totals.queue}</th>
<th>{$report.event_totals.delivered}</th>
<th>{$report.event_totals.bounce}</th>
<th>{$report.event_totals.unsubscribe}</th>
<th>{$report.event_totals.reply}</th>
{if $report.mailing.open_tracking}
<th>{$report.event_totals.opened}</th>
{/if}
{if $report.mailing.url_tracking}
<th>{$report.event_totals.url}</th>
{/if}
</tr>
<tr>
<th colspan=5>{ts}Percentages{/ts}</th>
<th>{$report.event_totals.delivered_rate}%</th>
<th>{$report.event_totals.bounce_rate}%</th>
<th>{$report.event_totals.unsubscribe_rate}%</th>
</tr>
</table>
{else}
<table class="form-layout">
<tr><td class="label">{ts}Scheduled Date{/ts}</td><td>{$report.jobs.0.scheduled_date}</td></tr>
<tr><td class="label">{ts}Start Date{/ts}</td><td>{$report.jobs.0.start_date}</td></tr>
<tr><td class="label">{ts}End Date{/ts}</td><td>{$report.jobs.0.end_date}</td></tr>
<tr><td class="label">{ts}Status{/ts}</td><td>{$report.jobs.0.status}</td></tr>
<tr><td class="label">{ts}Intended Recipients{/ts}</td><td>{$report.jobs.0.queue}</td></tr>
<tr><td class="label">{ts}Succesful Deliveries{/ts}</td><td>{$report.jobs.0.delivered}</td><td>{$report.jobs.0.delivered_rate}%</td></tr>
<tr><td class="label">{ts}Bounces{/ts}</td><td>{$report.jobs.0.bounce}</td><td>{$report.jobs.0.bounce_rate}%</td></tr>
<tr><td class="label">{ts}Unsubscriptions{/ts}</td><td>{$report.jobs.0.unsubscribe}</td><td>{$report.jobs.0.unsubscribe_rate}%</td></tr>
<tr><td class="label">{ts}Replies{/ts}</td><td>{$report.jobs.0.reply}</td></tr>
{if $report.mailing.open_tracking}
<tr><td class="label">{ts}Tracked Opens{/ts}</td><td>{$report.jobs.0.opened}</td></tr>
{/if}
{if $report.mailing.url_tracking}
<tr><td class="label">{ts}Click-throughs{/ts}</td><td>{$report.jobs.0.url}</td></tr>
{/if}
</table>
{/if}
</fieldset>

{if $report.mailing.url_tracking && $report.click_through|@count > 0}
<fieldset>
<legend>{ts}Click-through Statistics{/ts}</legend>
<table>
<tr>
<th>{ts}Clicks{/ts}</th>
<th>{ts}Unique Clicks{/ts}</th>
<th>{ts}Success Rate{/ts}</th>
<th>{ts}URL{/ts}</th></tr>
{foreach from=$report.click_through item=row}
<tr class="{cycle values="odd-row,even-row"}">
<td>{$row.clicks}</td>
<td>{$row.unique}</td>
<td>{$row.rate}%</td>
<td><a href="{$row.url}">{$row.url}</a></td>
</tr>
{/foreach}
</table>
</fieldset>
{/if}
