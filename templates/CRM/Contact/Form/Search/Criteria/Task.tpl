{if $showTask}
    <div id="task_block" class="form-item">
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
    </div>
{/if}
