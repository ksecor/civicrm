<div class="menu">
<ul class="indented">
{foreach from=$event item=values key=id}
<li class="leaf">
  <strong><a href="{crmURL p="civicrm/event/info" q="reset=1&id=`$id`"}">{$values.title}</a></strong>
{if $values.start_date}
  <strong>{$values.start_date|crmDate}&nbsp;{if $values.end_date}to{/if}&nbsp;{$values.end_date|crmDate}</strong>
{/if}
{if $values.summary}
  <br/>
  {$values.summary}
{/if}
{if $values.onlineRegistration}
   <br/>
   <strong><a href="{crmURL p="civicrm/event/register" q="reset=1&id=`$id`"}">&raquo; {$values.registration_link_text}</strong>
{/if}

{/foreach}
</ul>
</div>
