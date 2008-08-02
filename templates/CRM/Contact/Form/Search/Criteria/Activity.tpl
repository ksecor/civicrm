<div id="activity" class="form-item">
  <fieldset class="collapsible">
    <table class="form-layout">
        <tr>
            <td class="label">
                {$form.activity_type_id.label}
            </td>
            <td colspan="2">
                {$form.activity_type_id.html}
            </td>
        </tr>
        <tr>
            <td class="label">
                {$form.activity_date_low.label}
            </td>
            <td colspan="2">
                 {$form.activity_date_low.html} &nbsp; {$form.activity_date_high.label} {$form.activity_date_high.html}
            </td>
        </tr>
        <tr>
            <td class="label">
                {$form.activity_role.label}
            </td>
            <td>
                {$form.activity_role.html}
            </td>
            <td>
                {$form.activity_target_name.html}<br />
                <span class="description font-italic">{ts}Complete OR partial Contact Name.{/ts}</span> 
            </td>
        </tr>
        <tr>
            <td class="label">
                {$form.activity_status.label}
            </td>
            <td colspan="2">
                {$form.activity_status.html} 
            </td>
        </tr>
        <td class="label">
                {$form.activity_subject.label}
            </td>
            <td colspan="2">
                {$form.activity_subject.html} 
            </td>
        </tr>
        <td class="label">
                {$form.test_activities.label}
            </td>
            <td colspan="2">
                {$form.test_activities.html} 
            </td>
        </tr>
    </table>
    </fieldset>
</div>
