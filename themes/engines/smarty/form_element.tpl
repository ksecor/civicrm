<div class="form-item">
  {if $title}
    {if $id}
      <label for="{$id}">{$title} XXX:</label>{$required}
    {else}
      <label>{$title}:</label>{$required}
    {/if}
  {/if}

  {$value}

  {if $description}
    <div class="description">{$description}</div>
  {/if}
</div>
