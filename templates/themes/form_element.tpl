<div class="form-item">
{if $element.label_real}
  {if $element.name}
    <label for="{$element.name}">{$element.label_orig}</label>{$element.required}
  {else}
    <label>{$element.label_real}:</label>{$element.required}
  {/if}
{/if}
{$element.html}
{if $element.description}
  <div class="description">{$element.description}</div>
{/if}
</div>
