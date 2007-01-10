	<table class="form-layout">
		<tr>
            <td class="font-size12pt">{$form.sort_name.label}</td>
            <td>{$form.sort_name.html}
                <div class="description font-italic">
                    {ts}Complete OR partial contact name.{/ts}
                </div>
                {$form.email.html}
                <div class="description font-italic">
                    {ts}Email.{/ts}
                </div>
            </td>
            <td>
                {$form.uf_group_id.label} {$form.uf_group_id.html}
            </td>
            <td class="label">{$form.buttons.html}</td>       
        </tr>
		<tr>
            <td><label>{ts}Contact Type(s){/ts}</label><br />
                {$form.contact_type.html}
            </td>
            <td><label>{ts}Group(s){/ts}</label><br />
                <div class="listing-box">
                    {foreach from=$form.group item="group_val"}
                    <div class="{cycle values="odd-row,even-row"}">
                    {$group_val.html}
                    </div>
                    {/foreach}
                </div>
            </td>
            <td><label>{ts}Tag(s){/ts}</label><br />
                <div class="listing-box">
                    {foreach from=$form.tag item="tag_val"} 
                    <div class="{cycle values="odd-row,even-row"}">
                    {$tag_val.html}
                    </div>
                    {/foreach}
                </div>
            </td>
	</tr>
        <tr>
            <td><br />{$form.privacy.label}</td>
            <td><br />{$form.privacy.html}</td>
        </tr>
        <tr>
            <td><br />{$form.contact_source.label}</td>
            <td><br />{$form.contact_source.html}</td>
        </tr>
    </table>
