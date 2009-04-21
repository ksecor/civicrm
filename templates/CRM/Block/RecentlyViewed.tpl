{* Displays recently viewed objects (contacts and other objects like groups, notes, etc. *}
<div id="recently-viewed">
    <ul>
    {foreach from=$recentlyViewed item=item}
         <li>{*<a href="{$item.url}">{$item.icon}</a>*}<a href="{$item.url}" title="{$item.tooltip}">{$item.title}</a></li>
    {/foreach}
   </ul>
</div>
