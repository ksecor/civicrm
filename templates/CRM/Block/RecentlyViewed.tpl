{* Displays recently viewed objects (contacts and other objects like groups, notes, etc. *}
<div id="recently-viewed">
    <ul>
    {foreach from=$recentlyViewed item=item}
         <li class="{$item.type}"><a href="{$item.url}" title="{$item.title}">
         {if $item.image_url}
            <div class="icon crm-icon {if $item.subtype}{$item.subtype}{else}{$item.type}{/if}-icon" style="background: url('{$item.image_url}')"></div>
         {else}
            <div class="icon crm-icon {$item.type}{if $item.subtype}-subtype{/if}-icon"></div>
         {/if}
         {$item.title|truncate:28:"..":true}</a></li>
    {/foreach}
   </ul>
</div>
