<div id="openAtcivity">
  <fieldset><legend><a href="#" onclick="hide('openAtcivity'); show('openAtcivity_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a>{ts}Scheduled Activities{/ts}</legend>
    <table class="form-layout">
        <tr>
            <td class="label">
                {$form.open_activity_type_id.label}
            </td>
            <td>
                {$form.open_activity_type_id.html}
            </td>
        </tr>
        <tr>
            <td class="label">
                {$form.open_activity_date_low.label}
            </td>
            <td>
                 {$form.open_activity_date_low.html} &nbsp; {$form.open_activity_date_high.label} {$form.open_activity_date_high.html}
            </td>
        </tr>
    </table>
    </fieldset>
</div>
