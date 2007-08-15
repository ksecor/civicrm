	<table class="form-layout">
		<tr>
            <td class="font-size12pt">{$form.sort_name.label}</td>
            <td>{$form.sort_name.html}
                <div class="description font-italic">
                    {ts}Complete OR partial Contact Name.{/ts}
                </div>
                {$form.email.html}
                <div class="description font-italic">
                    {ts}Complete OR partial Email Address.{/ts}
                </div>
            </td>
            <td>
                {$form.uf_group_id.label} {$form.uf_group_id.html}
                <br /><br />
                <div class="form-item">
                    {if $form.uf_user}{$form.uf_user.label} {$form.uf_user.html}
                    &nbsp; <a href="#" title="unselect" onclick="unselectRadio('uf_user', 'Advanced'); return false;" >unselect</a>

                    <div class="description font-italic">
                        {ts 1=$config->userFramework}Does the contact have a %1 Account?{/ts}
                    </div>
{/if}
                </div>
            </td>
            <td class="label">{$form.buttons.html}</td>       
        </tr>
		<tr>
            <td><label>{ts}Contact Type(s){/ts}</label><br />
                {$form.contact_type.html}
            </td>
            <td><label>{ts}Organization(s){/ts}</label> / <span class="notorg"><label>{ts}Group(s){/ts}</label></span><br />
                <div class="listing-box">
                    {foreach from=$form.group item="group_val"}
                    <div class="{cycle values="odd-row,even-row"}">
                    {$group_val.html}
                    </div>
                    {/foreach}
                </div>
            </td>
            <td colspan="2"><label>{ts}Tag(s){/ts}</label><br />
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
            <td colspan="3"><br />{$form.privacy.html}
                 <div class="description font-italic">
                    {ts}<strong>EXCLUDE</strong> contacts who have these privacy option(s).{/ts}
                 </div>
            </td>
        </tr>
        <tr>
            <td>{$form.contact_source.label}</td>
            <td colspan="3">{$form.contact_source.html}</td>
        </tr>
    </table>
