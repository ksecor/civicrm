<div class="menu">
<ul class="indented">
{foreach from=$menu item=menuItem}
{if $menuItem.start}<li class="no-display"><ul class="indented">{/if}
<li class="{$menuItem.class}"><a href="{$menuItem.url}" {$menuItem.active}>{$menuItem.title}</a></li>
{if $menuItem.end}</ul></li>{/if}
{/foreach}
</ul>
</div>
