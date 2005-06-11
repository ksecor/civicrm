{* Displays local tasks (secondary menu) for any pages that have them *}
<div class="tabs">
    <ul class="tabs primary">
    {section name=task loop=$localTasks}
        <li {if $task.class}class="{$task.class}{/if}><a href="{$task.url}" {if $task.class}class="{$task.class}"{/if}>{$task.title}</a></li>
    {/section}
   </ul>
</div>
