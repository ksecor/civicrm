<div class="menu">
<ul>
{foreach from=$shortCuts item=short}
    <li class="leaf"><a href="{$short.url}">{$short.title}</a></li>
{/foreach}
</ul>
</div>
