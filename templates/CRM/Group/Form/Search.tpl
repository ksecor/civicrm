<div class="form-item">
<fieldset><legend>{ts}Find Groups{/ts}</legend>
<table class="form-layout">
    <tr>
        <td>{$form.title.label}</td>
        <td>{$form.title.html}
            <div class="description font-italic">
                {ts}Complete OR partial group name.{/ts}
            </div>
        </td>
        <td>{$form.group_type.label}</td>
        <td>{$form.group_type.html}
            <div class="description font-italic">
                {ts}Filter search by group type(s).{/ts}
            </div>
        </td>
        <td>{$form.visibility.label}</td>
        <td>{$form.visibility.html}
            <div class="description font-italic">
                {ts}Filter search by visibility.{/ts}
            </div>
        </td>
        <td class="right">&nbsp;{$form.buttons.html}</td>
    </tr>
</table>
</fieldset>
</div>