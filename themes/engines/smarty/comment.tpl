<div class="comment {if $comment->new}comment-new{/if}>
{if $comment->new}
  <a id="new"></a>
  <span class="new">{$new}</span>
{/if}

<div class="title">{$title}</div>
  {$picture}
  <div class="author">{$submitted}</div>
  <div class="content">{$content}</div>
  {if $picture}
    <br class="clear" />
  {/if}
  <div class="links">{$links}</div>
</div>
