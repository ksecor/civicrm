{if $error}
  <span class="wgm-error">{$label|upper}</span>
{else}
  {$label}
{/if}
{if $required}
   <span class="wgm-error" size="1">*</span>
{/if}