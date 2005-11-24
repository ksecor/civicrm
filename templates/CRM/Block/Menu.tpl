<div class='menu'>
<ul>
{foreach from=$menu item=menuItem}
{if $menuItem.start}<ul>{/if}
<li class="{$menuItem.class}"><a href="{$menuItem.url}" {$menuItem.active}>{$menuItem.title}</a></li>
{if $menuItem.end}</ul>{/if}
{/foreach}
</ul>
</div>
