{if $showTask}
    <div id="task_show" class="data-group">
      <a href="#" onclick="hide('task_show'); show('task_block'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
      <label>{ts}Task{/ts}</label>
    </div>
    <div id="task_block">
    <fieldset><legend><a href="#" onclick="hide('task_block'); show('task_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a>{ts}Task{/ts}</legend>
    <table class="form-layout">
         <tr>
            <td class="label">
                {$form.task_id.label}
            </td>
            <td>
                {$form.task_id.html}
            </td>
            <td class="label">
                {$form.task_status_id.label}
            </td>
            <td>
                {$form.task_status_id.html}
            </td>    
        </tr>
      </table>         
    </fieldset>
    </div>
{/if}
