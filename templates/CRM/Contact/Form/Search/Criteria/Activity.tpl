<div id="activity">
  <fieldset class="collapsible">
    <table class="form-layout">
        <tr>
            <td class="label">
                {$form.activity_type_id.label}
            </td>
            <td>
                {$form.activity_type_id.html}
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
