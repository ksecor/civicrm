<div class="block-civicrm">
{foreach from=$dashboardLinks item=dash}
<a accesskey="{$dash.key}" href="{$dash.url}">{$dash.title}</a>
{/foreach}
</div>
