{* Displays recently viewed objects (contacts and other objects like groups, notes, etc. *}
<div id="recently-viewed">
    <ul>
    <li>{ts}Recently Viewed:{/ts}</li>
    {foreach from=$recentlyViewed item=item}
        {*<li><a href="{$item.url}"}>{$item.icon}</a><a href="{$item.url}"}>{$item.title}</a></li>*}
        {* trucating the title for formating purposes *}
        <li><a href="{$item.url}"}>{$item.icon}</a><a href="{$item.url}"}>{$item.title|truncate:20:"...":true}</a></li>
    {/foreach}
   </ul>
</div>
