<fieldset><legend>{ts}Find Groups{/ts}</legend>
<div class="form-item">
<table class="form-layout">
    <tr>
        <td>{$form.title.label}<br />
            {$form.title.html}<br />
            <span class="description font-italic">
                {ts}Complete OR partial group name.{/ts}
            </span>
        </td>
        <td>{$form.group_type.label}<br />
            {$form.group_type.html}<br />
            <span class="description font-italic">
                {ts}Filter search by group type(s).{/ts}
            </span>
        </td>
        <td>{$form.visibility.label}<br />
            {$form.visibility.html}<br />
            <span class="description font-italic">
                {ts}Filter search by visibility.{/ts}
            </span>
        </td>
	<td>
            <label> Status</label><br />		
	    {$form.active_status.html}
	    {$form.active_status.label}&nbsp;
	    {$form.inactive_status.html}
            {$form.inactive_status.label}		
	 </td>
    </tr>
     <tr>
        <td>{$form.buttons.html}</td><td colspan="2">
    </tr>
</table>
</div>
</fieldset>
