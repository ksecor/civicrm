{if $error}
  <span class="crm-error">{$label|upper}</span>
{else}
  {$label}
{/if}
{if $required}
   <span class="crm-error" size="1">*</span>
{/if}