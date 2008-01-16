{if ! $trigger}
  {assign var=trigger value=trigger}
{/if}
<img src="{$config->resourceBase}i/cal.gif" id="{$trigger}" alt="{ts}Calendar{/ts}" style="padding: 5px 0px 0px 0px; vertical-align: text-bottom;"/>
{* Assign $doTime default = 0. Fields which include time should set $doTime = 1 when including desc.tpl *}
{assign var='doTime' value=$doTime|default:0}
{if $doTime EQ 0}
    {ts}Click to select date from calendar.{/ts} 
{else}
    {ts}Click to select date/time from calendar.{/ts} 
{/if}