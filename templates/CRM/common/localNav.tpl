{* Displays local tasks (secondary menu) for any pages that have them *}
<div class="tabs">
    <ul class="tabs primary">
    {foreach from=$localTasks item=task}
        <li {if $task.class}class="{$task.class}"{/if}><a href="{$task.url}" {if $task.class}class="{$task.class}"{/if}>{$task.title}</a></li>
    {/foreach}
   </ul>
</div>
