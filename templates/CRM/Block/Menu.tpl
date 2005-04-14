<div class='menu'>
<ul>
{foreach from=$menu item=menuItem}
<li class="{$menuItem.class}"><a href="{$menuItem.url}">{$menuItem.title}</a></li>
{/foreach}
</ul>
</div>
