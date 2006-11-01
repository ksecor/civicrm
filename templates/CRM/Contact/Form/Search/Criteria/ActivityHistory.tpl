<div id="atcivityHistory_show" class="data-group">
<a href="#" onclick="hide('atcivityHistory_show'); show('atcivityHistory'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
<label>{ts}Activity History{/ts}</label>
</div>
<div id="atcivityHistory">
  <fieldset><legend><a href="#" onclick="hide('atcivityHistory'); show('atcivityHistory_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a>{ts}Activity History{/ts}</legend>
     <table class="form-layout">
        <tr>
            <td class="label">
                {$form.activity_type.label}
            </td>
            <td>
                {$form.activity_type.html}
            </td>
        </tr>
        <tr>
            <td class="label">
                {$form.activity_date_low.label}
            </td>
            <td>
                 {$form.activity_date_low.html} &nbsp; {$form.activity_date_high.label} {$form.activity_date_high.html}
            </td>
        </tr>
    </table>
    </fieldset>
</div>
<div id="openAtcivity_show" class="data-group">
<a href="#" onclick="hide('openAtcivity_show'); show('openAtcivity'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
<label>{ts}Scheduled Activities{/ts}</label>
</div>
