{* Displays recently viewed objects (contacts and other objects like groups, notes, etc. *}
<div id="recently-viewed">
    <ul>
    {foreach from=$recentlyViewed item=item}
         <li class="{$item.type}"><a href="{$item.url}" title="{$item.title}"><div class="icon {$item.type}{if $item.subtype}-subtype{/if}-icon" {if $item.image_url}style="background: url('{$item.image_url}')"{/if}></div>{$item.title|truncate:28:"..":true}</a></li>
    {/foreach}
   </ul>
</div>
