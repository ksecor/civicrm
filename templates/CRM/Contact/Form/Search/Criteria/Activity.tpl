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
        <tr>
            <td class="label">
                {$form.activity_role.label}
            </td>
            <td>
                {$form.activity_role.html} 
            </td>
        </tr>
        <tr>
        <td class="label">
                {$form.activity_target_name.label}
            </td>
            <td>
                {$form.activity_target_name.html} 
            </td>
        </tr>
        <tr>
        <td class="label">
                {$form.activity_status.label}
            </td>
            <td>
                {$form.activity_status.html} 
            </td>
        </tr>
        <td class="label">
                {$form.activity_subject.label}
            </td>
            <td>
                {$form.activity_subject.html} 
            </td>
        </tr>
        <td class="label">
                {$form.test_activities.label}
            </td>
            <td>
                {$form.test_activities.html} 
            </td>
        </tr>
    </table>
    </fieldset>
</div>
