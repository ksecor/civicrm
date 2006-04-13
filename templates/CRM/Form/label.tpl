{if $error}
  <span class="error upper">{$label}</span>
{else}
  {$label}
{/if}
{if $required}
   <span class="marker">*</span>
{/if}
