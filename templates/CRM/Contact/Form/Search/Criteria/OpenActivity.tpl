<div id="openActivity">
  <fieldset><legend>{ts}Scheduled Activities{/ts}</legend>
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
