{foreach from=$profileGroups item=group}
    <h2>{$group.title}</h2>
    {$group.content}
{/foreach}
