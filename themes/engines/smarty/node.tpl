<div class="node" {if $static} static{/if}>
  {if $page eq 0}
    <h2><a href="{$node_url}" title="{$title}">{$title}</a></h2>
  {/if}
  {$picture}
  
  <div class="info">{$submitted}<span class="terms">{$terms}</span></div>
  <div class="content">
    {$content}
  </div>
{if $links}
  {if $picture}
      <br class='clear' />
  {/if}
  <div class="links">{$links}</div>
{/if}
</div>
