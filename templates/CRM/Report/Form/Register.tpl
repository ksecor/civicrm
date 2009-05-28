<fieldset>
    <legend>{ts}New Report{/ts}</legend>
    <table class="form-layout-compressed">
        <tr>
            <td class="label">{$form.label.label}</td>
            <td>{$form.label.html}<br/>
	    <span class="description">{ts}Report titile appear in the dispaly screen.{/ts}</span></td>
        </tr>
        <tr>
            <td class="label">{$form.description.label}</td>
            <td>{$form.description.html}<br/>
	    <span class="description">{ts}Report titile appear in the dispaly screen.{/ts}</span></td>
        </tr>
        <tr>
            <td class="label">{$form.value.label}</td>
            <td>{$form.value.html}<br/>
	    <span class="description">{ts}Report Url must be like "contribute/summary"{/ts}</span></td>
        </tr>
        <tr>
            <td class="label">{$form.name.label}</td>
            <td>{$form.name.html}<br/>
            <span class="description">{ts}Report Class must be present before adding the report here<br/>
		E.g. "CRM_Report_Form_Contribute_Summary"{/ts}</span></td>
        </tr>
        <tr>
            <td class="label">{$form.weight.label}</td>
            <td>{$form.weight.html}</td>
        </tr>
        <tr>
            <td class="label">{$form.component_id.label}</td>
            <td>{$form.component_id.html}<br/>
                <span class="description">{ts}Specify the Report if it is belongs to any component like "CiviContribute"{/ts}</span>
            </td>
        </tr>
        <tr>
            <td class="label">{$form.is_active.label}</td>
            <td>{$form.is_active.html}</td>
        </tr> 
	<tr>
            <td></td>
            <td>{$form.buttons.html}<br/>
        </tr>
    </table>
</fieldset>