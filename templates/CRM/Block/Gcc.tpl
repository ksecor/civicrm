<div class="menu">
<ul class="indented">
{foreach from=$shortCuts item=short}
    <li class="leaf"><a href="{$short.url}">{$short.title}</a></li>
{/foreach}
</ul>
</div>
