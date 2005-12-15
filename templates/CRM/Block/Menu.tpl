<div class='menu'>
<ul>
{foreach from=$menu item=menuItem}
{if $menuItem.start}<li><ul>{/if}
<li class="{$menuItem.class}"><a href="{$menuItem.url}" {$menuItem.active}>{$menuItem.title}</a></li>
{if $menuItem.end}</ul></li>{/if}
{/foreach}
</ul>
</div>
