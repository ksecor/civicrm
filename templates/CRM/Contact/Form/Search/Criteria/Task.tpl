{if $showTask}
    <div id="task_block">
    <fieldset><legend>{ts}Task{/ts}</legend>
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
