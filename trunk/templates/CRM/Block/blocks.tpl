{foreach from=$blocks item=block}
<div class="block {$block.name}" id="{$block.id}">
   <h2 class="title">{$block.subject}</h2>
   <div class="content">
      {$block.content}
   </div>
</div>
{/foreach}
