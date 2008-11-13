<div id="relationship" class="form-item">
    <table class="form-layout">
         <tr>
            <td>
               {$form.relation_type_id.label}<br />
               {$form.relation_type_id.html}
            </td>
            <td>
               {$form.relation_target_name.label}<br />
               {$form.relation_target_name.html|crmReplace:class:large}
                <div class="description font-italic">
                    {ts}Complete OR partial contact name.{/ts}
                </div>
            </td>    
            <td>
               {$form.relation_status.label}<br />
               {$form.relation_status.html}
            </td>
        </tr>
      </table>         
</div>

