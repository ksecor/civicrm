{php}
  print_r($block);
  flush();
{/php}
<div class="block block-{$block->module}" id="block-{$block->module}-{$block->delta}">
  <h2>{$block->subject}</h2>
  <div class="content">{$block->content}</div>
</div>
