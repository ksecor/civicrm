    <div id="relationship_show" class="data-group">
      <a href="#" onclick="hide('relationship_show'); show('relationship'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
      <label>{ts}Relationship{/ts}</label>
    </div>
    <div id="relationship">
    <fieldset><legend><a href="#" onclick="hide('relationship'); show('relationship_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a>{ts}Relationship{/ts}</legend>
    <table class="form-layout">
         <tr>
            <td class="label">
                {$form.relation_type_id.label}
            </td>
            <td>
                {$form.relation_type_id.html}
            </td>
            <td class="label">
                {$form.relation_target_name.label}
            </td>
            <td>
                {$form.relation_target_name.html|crmReplace:class:large}
                <div class="description font-italic">
                    {ts}Complete OR partial contact name.{/ts}
                </div>
            </td>    
        </tr>
      </table>         
    </fieldset>
    </div>

