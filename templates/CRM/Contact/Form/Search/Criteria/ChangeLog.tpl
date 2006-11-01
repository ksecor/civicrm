<div id="changelog_show" class="data-group">
<a href="#" onclick="hide('changelog_show'); show('changelog'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
<label>{ts}Change Log{/ts}</label>
</div>
<div id="changelog">
<fieldset><legend><a href="#" onclick="hide('changelog'); show('changelog_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a>{ts}Change Log{/ts}</legend>
    <table class="form-layout">
        <tr>
            <td class="label">
                {$form.changed_by.label}
            </td>
            <td>
                {$form.changed_by.html}
            </td>
        </tr>
        <tr>
            <td class="label">
                {$form.modified_date_low.label}
            </td>
            <td>
                 {$form.modified_date_low.html} &nbsp; {$form.modified_date_high.label} {$form.modified_date_high.html}
            </td>
        </tr>
    </table>
 </fieldset>
 </div>
