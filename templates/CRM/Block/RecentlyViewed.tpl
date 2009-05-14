{* Displays recently viewed objects (contacts and other objects like groups, notes, etc. *}
<div id="recently-viewed">
    <ul>
    {foreach from=$recentlyViewed item=item}
         <li><img src="{$config->resourceBase}/i/recent/{$item.type}.png" title="{$item.type}" alt="{$item.type}"><a href="{$item.url}" title="{$item.title}">{$item.title|truncate:28:"..":true}</a></li>
    {/foreach}
   </ul>
</div>
