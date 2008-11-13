<div class="menu">
<ul class="indented">
{foreach from=$shortCuts item=short}
    <li class="leaf"><a accesskey="{$short.key}" href="{$short.url}">{$short.title}</a></li>
{/foreach}
</ul>
</div>
