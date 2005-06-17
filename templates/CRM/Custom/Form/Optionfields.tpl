<fieldset><legend>{ts}Custom Options{/ts}</legend>

    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
	    <th>{ts}Defaults{/ts}</th>
            <th>{ts}Option Label{/ts}</th>
            <th>{ts}Option Value{/ts}</th>
            <th>{ts}Weight{/ts}</th>
	    <th>{ts}Status?{/ts}</th>
            <th>&nbsp;</th>
        </tr>
	<tr>
	    <td>{$form.default_opt_value.html}</td>
	    <td>{$form.label.html}</td>
	    <td>{$form.value.html}</td>
	    <td>{$form.weight.html}</td>
            <td>{$form.is_active.html}</td>
	</tr>
	<tr>
	    <td>{$form.default_opt_value.html}</td>
	    <td>{$form.label1.html}</td>
	    <td>{$form.value1.html}</td>
	    <td>{$form.weight1.html}</td>
            <td>{$form.is_active1.html}</td>
	</tr>
	</table>
	{/strip}
    </div>
    <div class="form-item">
	<a onclick="document.getElementById('showoption').style.display='none'">close</a> 
    </div>

</fieldset>


