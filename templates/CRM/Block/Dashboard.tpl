<div class="menu">
<ul class="indented">
{foreach from=$dashboardLinks item=dash}
    <li class="leaf"><a accesskey="{$dash.key}" href="{$dash.url}">{$dash.title}</a></li>
{/foreach}
</ul>
</div>
