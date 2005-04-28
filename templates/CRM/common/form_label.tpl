{if $error}
  <span class="error">{$label|upper}</span>
{else}
  {$label}
{/if}
{if $required}
   <span class="marker">*</span>
{/if}