{foreach from=$blocks item=block}
<div class="block {$block.name} id="{$block.name}">
   <h2 class="title">{$block.title}</h2>
   <div class="content">
      {$block.content}
   </div>
</div>
{/foreach}
