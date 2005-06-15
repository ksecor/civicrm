{* Displays recently viewed objects (contacts and other objects like groups, notes, etc. *}
<div id="recently-viewed">
    <ul>
    <li>Recently Viewed:</li>
    {foreach from=$recentlyViewed item=item}
        <li><a href="{$item.url}"}>{$item.icon}</a><a href="{$item.url}"}>{$item.title}</a></li>
    {/foreach}
   </ul>
</div>
