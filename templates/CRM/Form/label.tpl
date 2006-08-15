{if $error}
  <span class="error upper">{$label}</span>
{else}
  {$label}
{/if}
{if $required}
   <span class="marker" title="{ts}This field is required.{/ts}">*</span>
{/if}
