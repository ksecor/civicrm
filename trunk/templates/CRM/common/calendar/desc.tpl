{if ! $trigger}
  {assign var=trigger value=trigger}
{/if}
<img src="{$config->resourceBase}i/cal.gif" id="{$trigger}" alt="{ts}Calendar{/ts}" style="padding: 5px 0px 0px 0px; vertical-align: text-bottom;"/> 
{ts}Click to select date/time from calendar.{/ts} 
